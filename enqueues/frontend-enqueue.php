<?php

/**
 * File doc comment.
 */
use ProposalCrafter\WpMVC\Enqueue\Enqueue;
defined( 'ABSPATH' ) || exit;

global $post;
if ( $post && 'pc-proposal' === $post->post_type ) {
	wp_enqueue_media();

	Enqueue::script( 'proposal-crafter-frontend', 'build/js/frontend' );
	Enqueue::style( 'proposal-crafter-frontend', 'build/css/frontend' );

	wp_localize_script(
		'proposal-crafter-frontend',
		'proposalCrafterFrontend',
		[
			'restApi'   => get_rest_url( null, 'proposal-crafter/public' ),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'postId'    => $post->ID,
		]
	);
}
