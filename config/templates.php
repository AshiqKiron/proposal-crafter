<?php

/**
 * File doc comment.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Doc comment.
 */
function proposal_crafter_get_template_markup( $id ) {
	$template_path = proposal_crafter_dir( "assets/templates/{$id}/markup.php" );
	if ( file_exists( $template_path ) ) {
		ob_start();
		include $template_path;
		$markup = ob_get_clean();

		return proposal_crafter_decode_unicode( $markup );
	}
	return '';
}

return apply_filters(
	'proposal_crafter_templates',
	[
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Basic Proposal',
			'description'     => 'A clean, versatile, and minimalist proposal template suitable for a wide range of general inquiries and simple project offers where clarity is key.',
			'package'         => 'free',
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/6/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/6/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 6 ),
		],
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Design Proposal',
			'description'     => 'A visually engaging proposal template tailored for creative professionals. Highlight your aesthetic approach, design philosophy, and portfolio to win over design-centric clients.',
			'package'         => 'free',
			'coming_soon'     => true,
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/2/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/2/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 2 ),
		],
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Project Proposal',
			'description'     => 'A comprehensive project skeleton that helps you detail the scope, objectives, and execution plan for any business engagement, ensuring alignment with client expectations.',
			'package'         => 'free',
			'coming_soon'     => true,
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/1/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/1/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 1 ),
		],
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Social Media Marketing Proposal',
			'description'     => "A strategic proposal format for digital marketers. Outline campaigns, content strategies, and growth metrics to demonstrate how you'll elevate the client's social presence.",
			'package'         => 'free',
			'coming_soon'     => true,
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/3/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/3/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 3 ),
		],
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Website Development Proposal',
			'description'     => 'A technical yet accessible proposal designed for web developers. Clearly define architecture, technology stacks, feature sets, and development milestones.',
			'package'         => 'free',
			'coming_soon'     => true,
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/4/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/4/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 4 ),
		],
		[
			'id'              => wp_unique_id( 'temp-' ),
			'title'           => 'Service Contract Proposal',
			'description'     => 'A formal and structured template for solidifying service agreements. Clearly state terms, conditions, deliverables, and payment schedules to protect both parties.',
			'package'         => 'free',
			'coming_soon'     => true,
			'categories'      => [ 'business', 'project' ],
			'thumbnail_image' => proposal_crafter_url( 'assets/templates/5/thumbnail.webp' ),
			'preview_image'   => proposal_crafter_url( 'assets/templates/5/preview.webp' ),
			'markup'          => proposal_crafter_get_template_markup( 5 ),
		],
	]
);
