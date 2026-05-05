<?php

/**
 * File doc comment.
 */

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\App;
use ProposalCrafter\Database\Setup;

/**
 * Plugin Name:       Proposal Crafter
 * Description:       This plugin is build with WordPress
 * Version:           0.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * Author:            WpMVC
 * Author URI:        https://github.com/wpdotplugins/proposal-crafter
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       proposal-crafter
 * Domain Path:       /languages
 */

require_once __DIR__ . '/vendor/vendor-src/autoload.php';
require_once __DIR__ . '/app/Helpers/helper.php';

/**
 * Doc comment.
 */
final class ProposalCrafter {
	/**
	 * Doc comment.
	 */
	public static ProposalCrafter $instance;

	/**
	 * Doc comment.
	 */
	public static function instance(): ProposalCrafter {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Doc comment.
	 */
	public function load() {
		// Run Activation Tasks.
		register_activation_hook(
			__FILE__,
			function() {
				( new Setup() )->execute();
				(new \ProposalCrafter\App\Providers\PostTypeServiceProvider())->register_post_type();
				flush_rewrite_rules();
			}
		);

		$application = App::instance();

		$application->boot( __FILE__, __DIR__ );

		/**
		 * Fires once activated plugins have loaded.
		 */
		add_action(
			'plugins_loaded',
			function () use ( $application ): void {

				do_action( 'proposal_crafter_before_load' );

				$application->load();

				do_action( 'proposal_crafter_after_load' );
			}
		);
	}
}

ProposalCrafter::instance()->load();
