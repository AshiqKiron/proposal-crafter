<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\App\Http\Controllers;

use ProposalCrafter\WpMVC\Routing\Response;
use ProposalCrafter\WpMVC\RequestValidator\Validator;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

/**
 * Doc comment.
 */
class TemplateController extends Controller {
	/**
	 * Doc comment.
	 */
	public function list() {
		$templates = proposal_crafter_config( 'templates' );

		foreach ( $templates as &$template ) {
			unset( $template['markup'] );
		}

		return Response::send(
			[
				'message' => 'Templates retrieved successfully',
				'data'    => $templates,
			]
		);
	}
}
