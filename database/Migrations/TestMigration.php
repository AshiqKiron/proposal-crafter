<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\Database\Migrations;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\Contracts\Migration;

/**
 * Doc comment.
 */
class TestMigration implements Migration {
	/**
	 * Doc comment.
	 */
	public function more_than_version() {
		return '1.0.0';
	}

	/**
	 * Doc comment.
	 */
	public function execute(): bool {
		return true;
	}
}
