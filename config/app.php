<?php

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\App\Http\Middleware\EnsureIsUserAdmin;
use ProposalCrafter\App\Providers\Admin\MenuServiceProvider;
use ProposalCrafter\App\Providers\PostTypeServiceProvider;
use ProposalCrafter\App\Providers\BlockServiceProvider;
use ProposalCrafter\App\Providers\GoogleFontProvider;
// use ProposalCrafter\Database\Migrations\TestMigration;
use ProposalCrafter\WpMVC\Helpers\Helpers;

return [
	/**
	 * The version of the plugin.
	 */
	'version'                     => Helpers::get_plugin_version( 'proposal-crafter' ),

	/**
	 * Configuration for the REST API.
	 */
	'rest_api'                    => [
		/**
		 * The namespace for the REST API.
		 */
		'namespace' => 'proposal-crafter',

		/**
		 * The versions of the REST API.
		 */
		'versions'  => [],
	],

	/**
	 * Configuration for the AJAX API.
	 */
	'ajax_api'                    => [
		/**
		 * The namespace for the AJAX API.
		 */
		'namespace' => 'proposal-crafter',

		/**
		 * The versions of the AJAX API.
		 */
		'versions'  => [],
	],

	/**
	 * Service providers for the plugin.
	 */
	'providers'                   => [
		PostTypeServiceProvider::class,
		BlockServiceProvider::class,
		GoogleFontProvider::class,
	],

	/**
	 * Service providers for the admin area of the plugin.
	 */
	'admin_providers'             => [
		MenuServiceProvider::class,
	],

	/**
	 * Middleware configuration for the plugin.
	 */
	'middleware'                  => [
		'is_user_admin' => EnsureIsUserAdmin::class,
	],
	/**
	 * Post type names must be between 1 and 20 characters in length
	 */
	'proposal_post_type'          => 'pc-proposal',

	/**
	 * The database option key for storing migration information.
	 */
	'migration_db_option_key'     => 'proposal_crafter_migrations',

	/**
	 * List of migrations for the plugin.
	 */
	'migrations'                  => [
		// 'test-migration' => TestMigration::class,
	],

	/**
	 * This configuration option defines a hook that will fire before executing the route callback,
	 * such as before a controller action. It provides two parameters:
	 *
	 * @param WP_REST_Request $wp_rest_request The current REST request object.
	 * @param string $full_route The full route being accessed.
	 */
	'rest_response_action_hook'   => 'proposal-crafter_rest_response_action',

	/**
	 * Configuration for the REST API response filter hook.
	 *
	 * This filter hook allows overriding the entire REST API response.
	 *
	 * @param $response The response object from the controller.
	 * @param WP_REST_Request  $wp_rest_request The request object.
	 * @param string           $full_route The full route of the request.
	 */
	'rest_response_filter_hook'   => 'proposal-crafter_rest_response_filter',

	/**
	 * This filter hook that can override all REST API permissions.
	 *
	 * @param mixed $permission The current permission setting.
	 * @param mixed $middleware The middleware being applied.
	 * @param string $full_route The full route of the API endpoint.
	 */
	'rest_permission_filter_hook' => 'proposal-crafter_rest_permission_filter',
];
