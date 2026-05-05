<?php

namespace ProposalCrafter\App\Providers\Admin;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\Contracts\Provider;

class MenuServiceProvider implements Provider {
	public function boot() {
		add_action( 'admin_menu', [ $this, 'action_admin_menu' ] );
		add_action( 'admin_head', [ $this, 'action_admin_head' ] );
	}

	/**
	 * Loading menu activation js code only helpgent admin page
	 */
	public function action_admin_head() : void {
		?>
		<style>
			.wp-submenu-wrap a[href="https://asphaltthemes.com"] {
				color: #f06060 !important;
				font-weight: bold;
			}
		</style>
		<script>
			jQuery(($) => {
				const $scope = $('#toplevel_page_proposal-crafter-menu');
				const $submenuWrap = $scope.find('.wp-submenu-wrap');
				const $overviewMenu = $scope.find('.wp-first-item');
				const $overviewMenuLink = $overviewMenu.find('a');

				const currentUrl = $overviewMenuLink.attr('href');
				$overviewMenuLink.attr('href', currentUrl + '#overview');

				// Handle submenu clicks
				$submenuWrap.on('click', 'li', function(e) {
					$(this).addClass('current').siblings().removeClass('current');
				});

				// Set active submenu based on URL hash
				const hash = window.location.hash;
				if (hash) {
					const $activeSubmenu = $submenuWrap.find(`a[href$="${hash}"]`).parent();
					if ($activeSubmenu.length) {
						$activeSubmenu.addClass('current').siblings().removeClass('current');
					}
				} else {
					$overviewMenu.addClass('current').siblings().removeClass('current');
				}
			})
		</script>
		<?php
	}

	public function action_admin_menu() {
		$page_url = admin_url( 'admin.php?page=proposal-crafter' );
		$icon_dir = proposal_crafter_dir( 'assets/svg/plugin-small-icon.svg' );

		$icon = file_get_contents( $icon_dir );
		$icon = 'data:image/svg+xml;base64,' . base64_encode( $icon );

		add_menu_page(
			esc_html__( 'Proposal Crafter', 'proposal-crafter' ),
			esc_html__( 'Proposal Crafter', 'proposal-crafter' ),
			'manage_options',
			'proposal-crafter-menu',
			null,
			$icon,
			5
		);

		add_submenu_page(
			'proposal-crafter-menu',
			esc_html__( 'Overview', 'proposal-crafter' ),
			esc_html__( 'Overview', 'proposal-crafter' ),
			'manage_options',
			'proposal-crafter',
			[ $this, 'content' ]
		);

		add_submenu_page(
			'proposal-crafter-menu',
			esc_html__( 'Proposals', 'proposal-crafter' ),
			esc_html__( 'Proposals', 'proposal-crafter' ),
			'manage_options',
			$page_url . '#/proposals'
		);

		add_submenu_page(
			'proposal-crafter-menu',
			esc_html__( 'Templates', 'proposal-crafter' ),
			esc_html__( 'Templates', 'proposal-crafter' ),
			'manage_options',
			$page_url . '#/templates'
		);

		add_submenu_page(
			'proposal-crafter-menu',
			esc_html__( 'Settings', 'proposal-crafter' ),
			esc_html__( 'Settings', 'proposal-crafter' ),
			'manage_options',
			$page_url . '#/settings'
		);

		remove_submenu_page( 'proposal-crafter-menu', 'proposal-crafter-menu' );
	}

	public function content() {
		echo '<div id="proposal-crafter-dashboard-root"></div>';
	}
}
