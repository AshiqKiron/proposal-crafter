<?php

/**
 * File doc comment.
 */
defined( 'ABSPATH' ) || exit;
?>

<div id="proposal-confirm-modal" class="proposal-crafter-modal" style="display: none;">
	<div class="proposal-crafter-modal-overlay"></div>
	<div class="proposal-crafter-modal-content">
		<div class="proposal-crafter-modal-header">
			<h3 class="proposal-crafter-modal-title"><?php esc_html_e( 'Are you sure?', 'proposal-crafter' ); ?></h3>
			<button class="proposal-crafter-modal-close">&times;</button>
		</div>
		<div class="proposal-crafter-modal-body">
			<p><?php esc_html_e( 'Are you sure you want to decline this proposal? This action cannot be undone.', 'proposal-crafter' ); ?></p>
		</div>
		<div class="proposal-crafter-modal-footer">
			<button class="proposal-crafter-btn proposal-crafter-btn-secondary proposal-crafter-modal-cancel"><?php esc_html_e( 'Cancel', 'proposal-crafter' ); ?></button>
			<button class="proposal-crafter-btn proposal-crafter-btn-danger proposal-crafter-modal-confirm"><?php esc_html_e( 'Decline Proposal', 'proposal-crafter' ); ?></button>
		</div>
	</div>
</div>
