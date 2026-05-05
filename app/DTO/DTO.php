<?php
/**
 * Base Data Transfer Object
 *
 * Provides core functionality for DTOs including property validation,
 * serialization, and nested object handling.
 *
 * @package ProposalCrafter\App\DTO
 * @since 1.0.0
 */

namespace ProposalCrafter\App\DTO;

defined( 'ABSPATH' ) || exit;

use ReflectionClass;
use ReflectionProperty;

/**
 * Abstract DTO base class
 *
 * Provides common functionality for all Data Transfer Objects
 * including property checking and array conversion.
 */
abstract class DTO {
	/**
	 * Check if a property has been initialized with a value
	 *
	 * @param string $property_name The name of the property to check.
	 * @return bool True if the property exists and is initialized, false otherwise.
	 */
	public function has_property( string $property_name ): bool {
		try {
			$reflector = new ReflectionClass( $this );

			if ( ! $reflector->hasProperty( $property_name ) ) {
				return false;
			}

			$reflected_property = $reflector->getProperty( $property_name );
			$reflected_property->setAccessible( true );

			return $reflected_property->isInitialized( $this );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Convert DTO to associative array
	 *
	 * Recursively converts the DTO and any nested DTOs to array format.
	 * Handles nested objects and collections automatically.
	 *
	 * @param bool  $include_id      Whether to include the ID field in output.
	 * @param array $excluded_fields Fields to exclude from the output.
	 * @return array Associative array representation of the DTO.
	 */
	public function to_array( bool $include_id = false, array $excluded_fields = [ 'created_at' ] ): array {
		$data       = [];
		$reflector  = new ReflectionClass( $this );
		$properties = $reflector->getProperties( ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE );

		foreach ( $properties as $property ) {
			$property->setAccessible( true );

			// Skip uninitialized properties.
			if ( ! $property->isInitialized( $this ) ) {
				continue;
			}

			$field_name = $property->getName();

			// Apply field exclusion rules.
			if ( $this->should_exclude_field( $field_name, $include_id, $excluded_fields ) ) {
				continue;
			}

			$field_value         = $this->get_property_value( $field_name );
			$data[ $field_name ] = $this->serialize_value( $field_value, $include_id, $excluded_fields );
		}

		return $data;
	}

	/**
	 * Determine if a field should be excluded from array output
	 *
	 * @param string $field_name      The field name to check.
	 * @param bool   $include_id      Whether ID should be included.
	 * @param array  $excluded_fields List of fields to exclude.
	 * @return bool True if field should be excluded, false otherwise.
	 */
	protected function should_exclude_field( string $field_name, bool $include_id, array $excluded_fields ): bool {
		// Check if field is in exclusion list.
		if ( in_array( $field_name, $excluded_fields, true ) ) {
			return true;
		}

		// Check ID field exclusion.
		if ( 'id' === $field_name && ! $include_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Get property value using getter method
	 *
	 * @param string $property_name The property name.
	 * @return mixed The property value.
	 */
	protected function get_property_value( string $property_name ) {
		$getter_method = "get_{$property_name}";

		if ( method_exists( $this, $getter_method ) ) {
			return $this->{$getter_method}();
		}

		return $this->{$property_name} ?? null;
	}

	/**
	 * Serialize a value for array conversion
	 *
	 * Handles nested DTOs, arrays of DTOs, and primitive values.
	 *
	 * @param mixed $value           The value to serialize.
	 * @param bool  $include_id      Whether to include ID in nested objects.
	 * @param array $excluded_fields Fields to exclude from nested objects.
	 * @return mixed Serialized value.
	 */
	protected function serialize_value( $value, bool $include_id, array $excluded_fields ) {
		// Handle nested DTO objects.
		if ( $value instanceof self ) {
			return $value->to_array( $include_id, $excluded_fields );
		}

		// Handle arrays (potentially containing DTOs)
		if ( is_array( $value ) && ! empty( $value ) ) {
			return $this->serialize_array( $value, $include_id, $excluded_fields );
		}

		return $value;
	}

	/**
	 * Serialize an array, converting any DTO objects within
	 *
	 * @param array $items           The array to serialize.
	 * @param bool  $include_id      Whether to include ID in nested objects.
	 * @param array $excluded_fields Fields to exclude from nested objects.
	 * @return array Serialized array.
	 */
	protected function serialize_array( array $items, bool $include_id, array $excluded_fields ): array {
		return array_map(
			function ( $item ) use ( $include_id, $excluded_fields ) {
				return $this->serialize_value( $item, $include_id, $excluded_fields );
			},
			$items
		);
	}
}
