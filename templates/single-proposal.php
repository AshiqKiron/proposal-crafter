<?php

/**
 * File doc comment.
 */

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\App\Repositories\SettingsRepository;

$proposal_crafter_settings_repo = proposal_crafter_singleton( SettingsRepository::class );
$proposal_crafter_template_info = $proposal_crafter_settings_repo->get_template_info();

$proposal_crafter_hide_header = $proposal_crafter_template_info['hide_header'] ?? false;
$proposal_crafter_hide_footer = $proposal_crafter_template_info['hide_footer'] ?? false;

$proposal_crafter_page_title = get_post_meta( get_the_ID(), 'pc_title', true ) ?: get_the_title();

if ( ! $proposal_crafter_hide_header ) {
	get_header();
} else {
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php
}
?>

<div class="proposal-crafter-wrapper">
	<div class="proposal-crafter-body">
		<?php
		if ( file_exists( proposal_crafter_dir( 'templates/parts/status-badge.php' ) ) ) {
			include proposal_crafter_dir( 'templates/parts/status-badge.php' );
		}
		?>
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
	<div class="proposal-crafter-footer">
			<div>
				<p class="proposal-crafter-label">Proposal About</p>
				<h3 class="proposal-crafter-title"><?php echo esc_html( $proposal_crafter_page_title ); ?></h3>
			</div>
			<div>
				<?php
				$proposal_crafter_status      = get_post_status();
				$proposal_crafter_is_finished = in_array( $proposal_crafter_status, [ 'approved', 'declined' ], true );
				?>
				<button class="decline-proposal-btn" <?php disabled( $proposal_crafter_is_finished ); ?>>Decline Proposal</button>
				<button class="accept-proposal-btn" <?php disabled( $proposal_crafter_is_finished ); ?>>Accept Proposal</button>
			</div>
	</div>
</div>

<?php
if ( file_exists( proposal_crafter_dir( 'templates/parts/confirm-modal.php' ) ) ) {
	include proposal_crafter_dir( 'templates/parts/confirm-modal.php' );
}
?>

<?php
if ( ! $proposal_crafter_hide_footer ) {
	get_footer();
} else {
	wp_footer();
	?>
	</body>
	</html>
	<?php
}
?>
