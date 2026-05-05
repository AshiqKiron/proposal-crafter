<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\Database;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\Database\Schema\Schema;

/**
 * Doc comment.
 */
class Setup {
	/**
	 * Doc comment.
	 */
	public function execute() {
		Schema::create(
			'pc_settings',
			function( $table ) {
				$table->big_increments( 'id' );
				$table->string( 'setting_key', 255 );
				$table->text( 'value' )->nullable();
				$table->string( 'setting_group', 100 )->default( 'general' );
				$table->timestamps();

				$table->unique( [ 'setting_key', 'setting_group' ] );
			}
		);
	}
}
