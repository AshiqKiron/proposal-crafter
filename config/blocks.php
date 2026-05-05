<?php

/**
 * File doc comment.
 */

defined( 'ABSPATH' ) || exit;
$proposal_crafter_blocks_dir = proposal_crafter_dir( 'assets/blocks' );

return apply_filters(
	'proposal_crafter_blocks',
	[
		'proposal-crafter/container'      => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/proposal-title' => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/sender-name'    => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/sender-email'   => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/sender-company' => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/client-name'    => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/client-email'   => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/client-company' => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/heading'        => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/paragraph'      => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/list'           => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/signature'      => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/image'          => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/table'          => [
			'dir' => $proposal_crafter_blocks_dir,
		],
		'proposal-crafter/pricing'        => [
			'dir' => $proposal_crafter_blocks_dir,
		],
	]
);
