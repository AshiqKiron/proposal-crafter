<?php
/**
 * Template Information Data Transfer Object
 *
 * Represents template settings for proposals including
 * options to hide header and footer.
 *
 * @package ProposalCrafter\App\DTO
 * @since 1.0.0
 */

namespace ProposalCrafter\App\DTO;

defined( 'ABSPATH' ) || exit;

/**
 * TemplateInfoDTO class
 *
 * Encapsulates template configuration data.
 */
class TemplateInfoDTO extends DTO {
	/**
	 * Whether to hide the footer
	 *
	 * @var bool
	 */
	private bool $hide_footer;

	/**
	 * Whether to hide the header
	 *
	 * @var bool
	 */
	private bool $hide_header;

	/**
	 * Currency
	 *
	 * @var string
	 */
	private string $currency;

	/**
	 * Get hide_footer value
	 *
	 * @return bool
	 */
	public function get_hide_footer(): bool {
		return $this->hide_footer;
	}

	/**
	 * Set hide_footer value
	 *
	 * @param bool $hide_footer
	 * @return self
	 */
	public function set_hide_footer( bool $hide_footer ): self {
		$this->hide_footer = $hide_footer;
		return $this;
	}

	/**
	 * Get hide_header value
	 *
	 * @return bool
	 */
	public function get_hide_header(): bool {
		return $this->hide_header;
	}

	/**
	 * Set hide_header value
	 *
	 * @param bool $hide_header
	 * @return self
	 */
	public function set_hide_header( bool $hide_header ): self {
		$this->hide_header = $hide_header;
		return $this;
	}

	/**
	 * Get currency value
	 *
	 * @return string
	 */
	public function get_currency(): string {
		return $this->currency;
	}

	/**
	 * Set currency value
	 *
	 * @param string $currency
	 * @return self
	 */
	public function set_currency( string $currency ): self {
		$this->currency = $currency;
		return $this;
	}
}
