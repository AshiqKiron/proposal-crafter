<?php

/**
 * File doc comment.
 */

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\App\Http\Controllers\ProposalController;
use ProposalCrafter\WpMVC\Routing\Route;

Route::group(
	'public',
	function() {
		Route::group(
			'proposals',
			function() {
				Route::post( 'accept', [ ProposalController::class, 'accept' ] );
				Route::post( 'decline', [ ProposalController::class, 'decline' ] );
			}
		);
	}
);
