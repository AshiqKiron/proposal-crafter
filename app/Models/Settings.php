<?php

/**
 * File doc comment.
 */

namespace ProposalCrafter\App\Models;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\App;
use ProposalCrafter\WpMVC\Database\Eloquent\Model;
use ProposalCrafter\WpMVC\Database\Resolver;

/**
 * Doc comment.
 */
class Settings extends Model {
	/**
	 * Doc comment.
	 */
	public static function get_table_name():string {
		return 'pc_settings';
	}

	/**
	 * Doc comment.
	 */
	public function resolver():Resolver {
		return App::$container->get( Resolver::class );
	}
}
