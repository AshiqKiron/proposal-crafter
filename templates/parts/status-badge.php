<?php

/**
 * File doc comment.
 */
defined( 'ABSPATH' ) || exit;

$status = get_post_status();

if ( ! in_array( $status, [ 'approved', 'declined' ], true ) ) {
	return;
}
?>

<div class="proposal-status-wrapper">
	<div class="proposal-status-badge proposal-status-<?php echo esc_attr( $status ); ?>">
		<div class="proposal-status-icon">
			<?php if ( 'approved' === $status ) : ?>
				<img src="<?php echo esc_url( proposal_crafter_url( 'assets/svg/approval-badge.svg' ) ); ?>" alt="Approved" />
			<?php else : ?>
				<img src="<?php echo esc_url( proposal_crafter_url( 'assets/svg/declined-badge.svg' ) ); ?>" alt="Declined" />
			<?php endif; ?>
		</div>
		<div class="proposal-status-content">
			<h4 class="proposal-status-title">
				<?php
				if ( 'approved' === $status ) {
					esc_html_e( 'Proposal has been approved!', 'proposal-crafter' );
				} else {
					esc_html_e( 'Proposal has been declined!', 'proposal-crafter' );
				}
				?>
			</h4>
			<p class="proposal-status-date">
				<?php
				if ( 'approved' === $status ) {
					echo esc_html__( 'Approved at:', 'proposal-crafter' );
				} else {
					echo esc_html__( 'Declined at:', 'proposal-crafter' );
				}
				?>
				<?php echo esc_html( get_the_modified_date( 'd M, Y' ) . ' - ' . get_the_modified_time( 'h:i A' ) ); ?>
			</p>
		</div>
	</div>
</div>
