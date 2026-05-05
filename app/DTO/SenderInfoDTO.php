<?php
/**
 * Sender Information Data Transfer Object
 *
 * Represents sender/author information for proposals including
 * contact details, company information, and signature data.
 *
 * @package ProposalCrafter\App\DTO
 * @since 1.0.0
 */

namespace ProposalCrafter\App\DTO;

defined( 'ABSPATH' ) || exit;

/**
 * SenderInfoDTO class
 *
 * Encapsulates sender information data with validation and type safety.
 */
class SenderInfoDTO extends DTO {
	/**
	 * Signature type constant for uploaded image
	 */
	public const SIGNATURE_TYPE_UPLOAD = 'upload';

	/**
	 * Signature type constant for typed text
	 */
	public const SIGNATURE_TYPE_TYPE = 'type';

	/**
	 * Sender's full name
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Sender's email address
	 *
	 * @var string
	 */
	private string $email;

	/**
	 * Sender's company name
	 *
	 * @var string
	 */
	private string $company;

	/**
	 * Sender's WhatsApp number
	 *
	 * @var string
	 */
	private string $whatsapp_number;

	/**
	 * Signature type: 'upload' or 'type'
	 *
	 * @var string
	 */
	private string $signature_type;

	/**
	 * Signature text for 'type' mode
	 *
	 * @var string
	 */
	private string $signature_text = '';

	/**
	 * Signature image ID/URL for 'upload' mode
	 *
	 * @var string
	 */
	private string $signature_image = '';

	/**
	 * Get sender's name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Set sender's name
	 *
	 * @param string $name
	 * @return self
	 */
	public function set_name( string $name ): self {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get sender's email
	 *
	 * @return string
	 */
	public function get_email(): string {
		return $this->email;
	}

	/**
	 * Set sender's email
	 *
	 * @param string $email
	 * @return self
	 */
	public function set_email( string $email ): self {
		$this->email = $email;
		return $this;
	}

	/**
	 * Get sender's company
	 *
	 * @return string
	 */
	public function get_company(): string {
		return $this->company;
	}

	/**
	 * Set sender's company
	 *
	 * @param string $company
	 * @return self
	 */
	public function set_company( string $company ): self {
		$this->company = $company;
		return $this;
	}

	/**
	 * Get sender's WhatsApp number
	 *
	 * @return string
	 */
	public function get_whatsapp_number(): string {
		return $this->whatsapp_number;
	}

	/**
	 * Set sender's WhatsApp number
	 *
	 * @param string $whatsapp_number
	 * @return self
	 */
	public function set_whatsapp_number( string $whatsapp_number ): self {
		$this->whatsapp_number = $whatsapp_number;
		return $this;
	}

	/**
	 * Get signature type
	 *
	 * @return string
	 */
	public function get_signature_type(): string {
		return $this->signature_type;
	}

	/**
	 * Set signature type
	 *
	 * @param string $signature_type
	 * @return self
	 */
	public function set_signature_type( string $signature_type ): self {
		$this->signature_type = $signature_type;
		return $this;
	}

	/**
	 * Get signature text
	 *
	 * @return string
	 */
	public function get_signature_text(): string {
		return $this->signature_text;
	}

	/**
	 * Set signature text
	 *
	 * @param string $signature_text
	 * @return self
	 */
	public function set_signature_text( string $signature_text ): self {
		$this->signature_text = $signature_text;
		return $this;
	}

	/**
	 * Get signature image
	 *
	 * @return string
	 */
	public function get_signature_image(): string {
		return $this->signature_image;
	}

	/**
	 * Set signature image
	 *
	 * @param string $signature_image
	 * @return self
	 */
	public function set_signature_image( string $signature_image ): self {
		$this->signature_image = $signature_image;
		return $this;
	}
}

