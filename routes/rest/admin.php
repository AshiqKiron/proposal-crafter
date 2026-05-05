<?php

/**
 * File doc comment.
 */

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\App\Http\Controllers\ProposalController;
use ProposalCrafter\App\Http\Controllers\TemplateController;
use ProposalCrafter\App\Http\Controllers\SettingsController;
use ProposalCrafter\WpMVC\Routing\Route;

Route::group(
	'admin',
	function() {
		Route::group(
			'proposals',
			function() {
				Route::get( 'list', [ ProposalController::class, 'list' ] );
				Route::get( 'list/{id}', [ ProposalController::class, 'get_post_details' ] );
				Route::post( 'create', [ ProposalController::class, 'create_proposal' ] );
				Route::post( 'delete', [ ProposalController::class, 'delete_proposals' ] );
				Route::post( 'status', [ ProposalController::class, 'update_status' ] );
			}
		);

		Route::group(
			'templates',
			function() {
				Route::get( 'list', [ TemplateController::class, 'list' ] );
			}
		);

		Route::group(
			'settings',
			function() {
				Route::get( 'sender-info', [ SettingsController::class, 'get_sender_info' ] );
				Route::post( 'sender-info', [ SettingsController::class, 'set_sender_info' ] );

				Route::get( 'template-info', [ SettingsController::class, 'get_template_info' ] );
				Route::post( 'template-info', [ SettingsController::class, 'set_template_info' ] );
			}
		);
	},
	[ 'is_user_admin' ]
);
