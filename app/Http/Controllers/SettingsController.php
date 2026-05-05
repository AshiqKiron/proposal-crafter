<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\App\Http\Controllers;

use ProposalCrafter\WpMVC\Routing\Response;
use ProposalCrafter\WpMVC\RequestValidator\Validator;
use ProposalCrafter\App\Repositories\SettingsRepository;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

/**
 * Doc comment.
 */
class SettingsController extends Controller {
	/**
	 * Doc comment.
	 */
	public SettingsRepository $repository;

	/**
	 * Doc comment.
	 */
	public function __construct( SettingsRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Doc comment.
	 */
	public function get_sender_info() {
		$sender_info = $this->repository->get_sender_info();

		return Response::send(
			[
				'message' => 'Sender info retrieved successfully',
				'data'    => $sender_info,
			]
		);
	}

	/**
	 * Doc comment.
	 */
	public function set_sender_info( Validator $validate, WP_REST_Request $request ) {
		$validate->validate(
			[
				'name'            => 'string|max:255',
				'email'           => 'string|max:100',
				'whatsapp_number' => 'string|max:20',
				'company'         => 'string|max:100',
				'signature_type'  => 'string|max:10',
			]
		);

		// Validate signature type manually.
		$signature_type = $request->get_param( 'signature_type' );
		if ( ! in_array( $signature_type, [ 'upload', 'type' ], true ) ) {
			return Response::send(
				[
					'message' => 'Invalid signature type. Must be either "upload" or "type".',
				],
				400
			);
		}

		$dto = new \ProposalCrafter\App\DTO\SenderInfoDTO();
		$dto->set_name( $request->get_param( 'name' ) )
			->set_email( $request->get_param( 'email' ) )
			->set_whatsapp_number( $request->get_param( 'whatsapp_number' ) )
			->set_company( $request->get_param( 'company' ) )
			->set_signature_type( $signature_type )
			->set_signature_text( $request->get_param( 'signature_text' ) )
			->set_signature_image( $request->get_param( 'signature_image' ) );

		$this->repository->save_sender_info( $dto );

		return Response::send(
			[
				'message' => 'Sender information has been saved successfully.',
			],
			201
		);
	}

	/**
	 * Doc comment.
	 */
	public function get_template_info() {
		$template_info = $this->repository->get_template_info();

		return Response::send(
			[
				'message' => 'Template info retrieved successfully',
				'data'    => $template_info,
			]
		);
	}

	/**
	 * Doc comment.
	 */
	public function set_template_info( Validator $validate, WP_REST_Request $request ) {
		$validate->validate(
			[
				'hide_footer' => 'boolean',
				'hide_header' => 'boolean',
				'currency'    => 'string|max:10',
			]
		);

		$dto = new \ProposalCrafter\App\DTO\TemplateInfoDTO();
		$dto->set_hide_footer( $request->get_param( 'hide_footer' ) )
			->set_hide_header( $request->get_param( 'hide_header' ) )
			->set_currency( $request->get_param( 'currency' ) );

		$this->repository->save_template_info( $dto );

		return Response::send(
			[
				'message' => 'Template information has been saved successfully.',
			],
			201
		);
	}
}
