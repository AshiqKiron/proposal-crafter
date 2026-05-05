<?php

/**
 * File doc comment.
 */
use ProposalCrafter\WpMVC\Enqueue\Enqueue;
defined( 'ABSPATH' ) || exit;

wp_enqueue_media();
Enqueue::script( 'proposal-crafter-dashboard', 'build/js/dashboard' );
Enqueue::style( 'proposal-crafter-dashboard', 'build/css/dashboard', [ 'wp-components' ] );

wp_localize_script(
	'proposal-crafter-dashboard',
	'proposalCrafter',
	[
		'assetsUrl' => proposal_crafter_url( 'assets' ),
	]
);

global $post;
if ( $post && 'pc-proposal' === $post->post_type ) {

	Enqueue::script( 'proposal-crafter-gutenberg', 'build/js/gutenberg' );
	Enqueue::style( 'proposal-crafter-gutenberg', 'build/css/gutenberg' );
}
