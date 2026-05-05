<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\App\Providers;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\Contracts\Provider;

/**
 * Doc comment.
 */
class BlockServiceProvider implements Provider {
	/**
	 * Doc comment.
	 */
	public function boot() {
		add_action( 'init', [ $this, 'action_init' ] );
		add_action( 'save_post', [ $this, 'save_block_css' ], 10, 3 );

		if ( ! is_admin() && ! wp_is_json_request() ) {
			add_filter( 'wp_footer', [ $this, 'load_block_css' ], 10, 2 );
		}
	}

	/**
	 * Doc comment.
	 */
	public function action_init() : void {
		foreach ( proposal_crafter_config( 'blocks' ) as $block_name => $block_data ) {
			$name = ltrim( $block_name, 'proposal-crafter' );

			register_block_type( $block_data['dir'] . '/' . $name );
		}
	}

	/**
	 * Doc comment.
	 */
	public static function extract_block_name( $block ) {
		$parts = explode( '/', $block );
		return end( $parts );
	}

	/**
	 * Doc comment.
	 */
	public static function parse_blocks( $blocks, &$all_blocks ) {
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'proposal-crafter/' ) !== false ) {
				array_push( $all_blocks, $block );
			}

			self::parse_blocks( $block['innerBlocks'], $all_blocks );
		}
	}

	/**
	 * Doc comment.
	 */
	public function save_block_css( $post_id, $post, $update ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( 'attachment' === $post->post_type ||
			'revision' === $post->post_type ||
			'nav_menu_item' === $post->post_type ||
			'wp_template' === $post->post_type ||
			'wp_template_part' === $post->post_type ) {
			return;
		}

		$blocks     = parse_blocks( $post->post_content );
		$all_blocks = [];
		self::parse_blocks( $blocks, $all_blocks );

		$stylesheet = '';
		foreach ( $all_blocks as $block ) {
			$css         = isset( $block['attrs']['css'] ) ? $block['attrs']['css'] : '';
			$stylesheet .= $css;

			// Mapping of block names to meta keys.
			$block_meta_map = [
				'proposal-crafter/proposal-title' => [
					'meta' => 'pc_title',
					'attr' => 'content',
				],
				'proposal-crafter/sender-name'    => [
					'meta' => 'pc_sender_name',
					'attr' => 'content',
				],
				'proposal-crafter/sender-email'   => [
					'meta' => 'pc_sender_email',
					'attr' => 'content',
				],
				'proposal-crafter/sender-company' => [
					'meta' => 'pc_sender_company',
					'attr' => 'content',
				],
				'proposal-crafter/client-name'    => [
					'meta' => 'pc_client_name',
					'attr' => 'content',
				],
				'proposal-crafter/client-email'   => [
					'meta' => 'pc_client_email',
					'attr' => 'content',
				],
				'proposal-crafter/client-company' => [
					'meta' => 'pc_client_company',
					'attr' => 'content',
				],
			];

			// Iterate through the map and update post meta if the block matches.
			foreach ( $block_meta_map as $block_name => $args ) {
				if ( $block['blockName'] === $block_name ) {
					$attribute = $args['attr'] ?? 'content';
					$meta_key  = $args['meta'];
					$value     = isset( $block['attrs'][ $attribute ] ) ? $block['attrs'][ $attribute ] : '';

					if ( ! empty( $value ) ) {
						update_post_meta( $post_id, $meta_key, sanitize_text_field( $value ) );
					}
				}
			}
		}

		if ( ! empty( $stylesheet ) ) {
			update_post_meta( $post_id, '_pc_stylesheet', $stylesheet );
		} else {
			delete_post_meta( $post_id, '_pc_stylesheet' );
		}
	}

	/**
	 * Doc comment.
	 */
	public function load_block_css() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_queried_object_id();
		$css     = get_post_meta( $post_id, '_pc_stylesheet', true );

		if ( ! empty( $css ) ) {
			wp_register_style( 'proposal-crafter-inline-style', false, [], proposal_crafter_version(), 'all' );
			wp_enqueue_style( 'proposal-crafter-inline-style' );
			wp_add_inline_style( 'proposal-crafter-inline-style', $css );

			global $wp_styles;
			if ( isset( $wp_styles->registered['proposal-crafter-inline-style'] ) ) {
				$wp_styles->registered['proposal-crafter-inline-style']->extra['group'] = 0; // 0 = head, 1 = footer
			}
		}
	}
}
