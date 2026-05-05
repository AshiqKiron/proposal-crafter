<?php

namespace ProposalCrafter\App\Providers;

defined( 'ABSPATH' ) || exit;

use ProposalCrafter\WpMVC\Contracts\Provider;

class GoogleFontProvider implements Provider {
	public static $google_fonts = [];

	public static $done_registering = [];

	public function boot() {
		if ( ! is_admin() ) {
			add_filter( 'render_block', [ $this, 'gather_google_fonts' ], 10, 2 );
		}
	}

	public function gather_google_fonts( $block_content, $block ) {
		if ( $block_content === null ) {
			return $block_content;
		}

		if ( ! isset( $block['blockName'] ) || strpos( $block['blockName'], 'proposal-crafter/' ) === false ) {
			return $block_content;
		}

		foreach ( $block['attrs'] as $attr_name => $font_name ) {
			// Check if the attribute name ends with 'fontfamily'.
			if ( strcasecmp( substr( $attr_name, -10 ), 'fontfamily' ) === 0 ) {
				self::register_font( $font_name );
			}
		}

		return $block_content;
	}

	public static function is_web_font( $font_name ) {
		return ! in_array( strtolower( $font_name ), [ 'serif', 'sans-serif', 'monospace', 'serif-alt' ] );
	}

	public static function enqueue_google_fonts( $google_fonts, $handle = 'proposal-crafter-google-fonts' ) {
		if ( ! count( $google_fonts ) ) {
			return;
		}

		foreach ( $google_fonts as &$font ) {
			if ( ! empty( $font ) ) {
				$font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
			}
		}

		$fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%s&display=swap', implode( rawurlencode( '|' ), $google_fonts ) );
		wp_enqueue_style( $handle, $fonts_url ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	public function enqueue_frontend_block_fonts() {
		self::enqueue_google_fonts( array_keys( self::$google_fonts ) );
	}

	public function register_font( $font_name ) {
		if ( isset( self::$done_registering[ $font_name ] ) ) {
			return;
		}

		if ( ! self::is_web_font( $font_name ) ) {
			self::$done_registering[ $font_name ] = true;
			return;
		}

		if ( ! isset( self::$google_fonts[ $font_name ] ) ) {

			if ( apply_filters( 'proposal_crafter_enqueue_font', true, $font_name ) ) {
				self::$google_fonts[ $font_name ]     = true;
				self::$done_registering[ $font_name ] = true;

				// Enqueue the fonts in the footer.
				add_filter( 'wp_footer', [ $this, 'enqueue_frontend_block_fonts' ] );
			}
		}
	}

	public function str_ends_with( $haystack, $needle ) {
		$needle_len = strlen( $needle );
		return ( $needle_len === 0 || 0 === substr_compare( $haystack, $needle, - $needle_len ) );
	}
}
