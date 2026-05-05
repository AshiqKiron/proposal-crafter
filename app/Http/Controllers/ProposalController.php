<?php

namespace ProposalCrafter\App\Http\Controllers;

use ProposalCrafter\WpMVC\Routing\Response;
use ProposalCrafter\WpMVC\RequestValidator\Validator;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

class ProposalController extends Controller {
	public function list( Validator $validator, WP_REST_Request $request ) {
		$page     = $request->get_param( 'page' ) ? intval( $request->get_param( 'page' ) ) : 1;
		$per_page = $request->get_param( 'per_page' ) ? intval( $request->get_param( 'per_page' ) ) : 10;
		$search   = $request->get_param( 'search' );
		$status   = $request->get_param( 'status' );

		$args = [
			'post_type'      => 'pc-proposal',
			'post_status'    => $status ? $status : 'any',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		];

		if ( $search ) {
			$args['s'] = $search;
		}

		$query = new \WP_Query( $args );
		$posts = $query->posts;
		$total = $query->found_posts;

		// Add meta fields to each post
		$meta_fields = [
			'pc_title',
			'pc_sender_email',
			'pc_sender_name',
			'pc_sender_company',
			'pc_client_email',
			'pc_client_name',
			'pc_client_company',
		];

		foreach ( $posts as $post ) {
			$post->meta = [];
			foreach ( $meta_fields as $field ) {
				$post->meta[ $field ] = get_post_meta( $post->ID, $field, true );
			}
			$post->preview_url = get_permalink( $post->ID );
			$post->price       = proposal_crafter_calculate_price( $post->post_content );
		}

		$count_posts = wp_count_posts( 'pc-proposal' );
		$total_posts = (int) $count_posts->publish +
					   (int) $count_posts->draft +
					   (int) $count_posts->pending +
					   (int) $count_posts->future +
					   (int) $count_posts->private +
					   (int) $count_posts->trash +
					   (int) ( $count_posts->declined ?? 0 ) +
					   (int) ( $count_posts->approved ?? 0 );

		$last_page = $per_page > 0 ? ceil( $total / $per_page ) : 1;

		return Response::send(
			[
				'message'    => 'Proposals retrieved successfully',
				'data'       => $posts,
				'total'      => $total_posts,
				'pagination' => [
					'total'        => $last_page,
					'per_page'     => $per_page,
					'current_page' => $page,
					'last_page'    => $last_page,
				],
			]
		);
	}

	public function get_post_details( Validator $validator, WP_REST_Request $request ) {
		$post_id = $request->get_param( 'id' );
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Post not found',
				],
				404
			);
		}

		// grab meta fields
		$meta_fields = [
			'pc_title',
			'pc_sender_email',
			'pc_sender_name',
			'pc_sender_company',
			'pc_client_email',
			'pc_client_name',
			'pc_client_company',
		];

		$post->meta = [];

		foreach ( $meta_fields as $field ) {
			$post->meta[ $field ] = get_post_meta( $post_id, $field, true );
		}

		return Response::send(
			[
				'success' => true,
				'data'    => $post,
			]
		);
	}

	public function create_proposal( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'title'         => 'required|string|max:100',
				'senderEmail'   => 'required|email',
				'senderName'    => 'required|string|max:50',
				'senderCompany' => 'string|max:100',
				'clientEmail'   => 'required|email',
				'clientName'    => 'required|string|max:50',
				'clientCompany' => 'string|max:100',
				'sourceType'    => 'required|string',
			]
		);

		$body = $request->get_params();

		// 1. Prepare post data with markup (if template) but minimal other info initially
		$post_data = [
			'post_type'   => proposal_crafter_proposal_post_type(),
			'post_status' => 'draft',
			'post_author' => get_current_user_id(),
		];

		if ( 'template' === $body['sourceType'] && isset( $body['templateId'] ) ) {
			$templates       = proposal_crafter_config( 'templates' );
			$target_template = null;
			foreach ( $templates as $template ) {
				if ( $template['id'] == $body['templateId'] ) {
					$target_template = $template;
					break;
				}
			}

			if ( $target_template ) {
				$post_data['post_content'] = $target_template['markup'];
			}
		}

		// 2. Insert Post first
		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Failed to create proposal',
					'error'   => $post_id->get_error_message(),
				],
				400
			);
		}

		// 3. Update Title and Meta explicitly AFTER insert to override any markup defaults
		wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => sanitize_text_field( $body['title'] ),
			]
		);

		update_post_meta( $post_id, 'pc_title', sanitize_text_field( $body['title'] ) );
		update_post_meta( $post_id, 'pc_sender_email', sanitize_email( $body['senderEmail'] ) );
		update_post_meta( $post_id, 'pc_sender_name', sanitize_text_field( $body['senderName'] ) );
		update_post_meta( $post_id, 'pc_sender_company', sanitize_text_field( $body['senderCompany'] ?? '' ) );
		update_post_meta( $post_id, 'pc_client_email', sanitize_email( $body['clientEmail'] ) );
		update_post_meta( $post_id, 'pc_client_name', sanitize_text_field( $body['clientName'] ) );
		update_post_meta( $post_id, 'pc_client_company', sanitize_text_field( $body['clientCompany'] ?? '' ) );

		return Response::send(
			[
				'success' => true,
				'message' => 'Proposal created successfully',
				'data'    => [
					'post_id'  => $post_id,
					'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
					'view_url' => get_permalink( $post_id ),
				],
			]
		);
	}

	public function delete_proposals( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'ids' => 'required',
			]
		);

		$ids = $request->get_param( 'ids' );

		if ( ! is_array( $ids ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Invalid IDs format. Array expected.',
				],
				400
			);
		}

		$deleted_count = 0;
		$errors        = [];

		foreach ( $ids as $id ) {
			$post = get_post( $id );

			// Check if post exists and is of correct type
			if ( ! $post || 'pc-proposal' !== $post->post_type ) {
				$errors[] = "Post {$id} not found or invalid type.";
				continue;
			}

			// Verify permissions/ownership if needed?
			// For now assuming admin/authorized route.

			// wp_delete_post with second argument true forces permanent deletion
			if ( wp_delete_post( $id, true ) ) {
				$deleted_count++;
			} else {
				$errors[] = "Failed to delete post {$id}.";
			}
		}

		return Response::send(
			[
				'success'       => true,
				'message'       => "{$deleted_count} proposals deleted successfully.",
				'deleted_count' => $deleted_count,
				'errors'        => $errors,
			]
		);
	}

	public function update_status( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'ids'    => 'required',
				'status' => 'required|string',
			]
		);

		$ids    = $request->get_param( 'ids' );
		$status = $request->get_param( 'status' );

		if ( ! is_array( $ids ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Invalid IDs format. Array expected.',
				],
				400
			);
		}

		$updated_count = 0;
		$errors        = [];

		foreach ( $ids as $id ) {
			$post = get_post( $id );

			if ( ! $post || 'pc-proposal' !== $post->post_type ) {
				$errors[] = "Post {$id} not found or invalid type.";
				continue;
			}

			// Update post status
			$result = wp_update_post(
				[
					'ID'          => $id,
					'post_status' => $status,
				],
				true
			);

			if ( is_wp_error( $result ) ) {
				$errors[] = "Failed to update post {$id}: " . $result->get_error_message();
			} else {
				$updated_count++;
			}
		}

		if ( $updated_count === 0 && count( $errors ) > 0 ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Failed to update any proposals.',
					'errors'  => $errors,
				],
				400
			);
		}

		return Response::send(
			[
				'success'       => true,
				'message'       => "{$updated_count} proposals updated successfully.",
				'updated_count' => $updated_count,
				'errors'        => $errors,
			]
		);
	}

	public function accept_proposal( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'id' => 'required',
			]
		);

		$id = $request->get_param( 'id' );

		$post = get_post( $id );

		if ( ! $post || 'pc-proposal' !== $post->post_type ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Post not found or invalid type.',
				],
				404
			);
		}

		$result = wp_update_post(
			[
				'ID'          => $id,
				'post_status' => 'approved',
			],
			true
		);

		if ( is_wp_error( $result ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => "Failed to update post {$id}: " . $result->get_error_message(),
				],
				400
			);
		}

		return Response::send(
			[
				'success' => true,
				'message' => "Post {$id} updated successfully.",
			]
		);
	}

	public function accept( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'postId'         => 'required',
				'signatureType'  => 'required|string',
				'signatureValue' => 'required|string',
				'pricing'        => 'required',
			]
		);

		$post_id = $request->get_param( 'postId' );
		$post    = get_post( $post_id );

		if ( ! $post || 'pc-proposal' !== $post->post_type ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Proposal not found.',
				],
				404
			);
		}

		if ( in_array( $post->post_status, [ 'approved', 'declined' ], true ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Proposal is already ' . $post->post_status . '.',
				],
				400
			);
		}

		// store the signature
		$signature_type = $request->get_param( 'signatureType' );
		if ( isset( $signature_type ) && ! empty( $signature_type ) ) {
			update_post_meta( $post_id, 'pc_client_signature_type', $signature_type );
		}

		$signature_value = $request->get_param( 'signatureValue' );
		if ( isset( $signature_value ) && ! empty( $signature_value ) ) {
			update_post_meta( $post_id, 'pc_client_signature_value', $signature_value );
		}

		// store pricing
		$pricing     = $request->get_param( 'pricing' );
		$update_args = [
			'ID'          => $post_id,
			'post_status' => 'approved',
		];

		if ( isset( $pricing ) && ! empty( $pricing ) && is_array( $pricing ) ) {
			// get the gutenberg blocks used in this post
			$blocks = parse_blocks( $post->post_content );

			// loop through the blocks and update the pricing
			$this->update_blocks_recursively( $blocks, $pricing );

			// Update the post content
			$update_args['post_content'] = proposal_crafter_decode_unicode( serialize_blocks( $blocks ) );
		}

		wp_update_post( $update_args );

		return Response::send(
			[
				'success' => true,
				'message' => 'Proposal accepted successfully.',
			]
		);
	}

	public function decline( Validator $validator, WP_REST_Request $request ) {
		$validator->validate(
			[
				'postId' => 'required',
			]
		);

		$post_id = $request->get_param( 'postId' );
		$post    = get_post( $post_id );

		if ( ! $post || 'pc-proposal' !== $post->post_type ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Proposal not found.',
				],
				404
			);
		}

		if ( in_array( $post->post_status, [ 'approved', 'declined' ], true ) ) {
			return Response::send(
				[
					'success' => false,
					'message' => 'Proposal is already ' . $post->post_status . '.',
				],
				400
			);
		}

		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'declined',
			]
		);

		return Response::send(
			[
				'success' => true,
				'message' => 'Proposal declined successfully.',
			]
		);
	}

	/**
	 * Recursive function to update block attributes.
	 *
	 * @param array $blocks  The blocks array.
	 * @param array $pricing The pricing data.
	 */
	private function update_blocks_recursively( &$blocks, $pricing ) {
		foreach ( $blocks as &$block ) {
			$unique_id = $block['attrs']['uniqueId'] ?? null;

			if ( $unique_id && isset( $pricing[ $unique_id ] ) && isset( $block['attrs']['items'] ) && is_array( $block['attrs']['items'] ) ) {
				$checked_items = $pricing[ $unique_id ]['items'] ?? [];

				foreach ( $block['attrs']['items'] as $index => &$item ) {
					if ( isset( $checked_items[ $index ] ) ) {
						$item['isChecked'] = (bool) $checked_items[ $index ];
					}
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->update_blocks_recursively( $block['innerBlocks'], $pricing );
			}
		}
	}
}
