<?php

namespace ProposalCrafter\App\Repositories;

defined( 'ABSPATH' ) || exit;
use ProposalCrafter\App\DTO\SenderInfoDTO;
use ProposalCrafter\App\DTO\TemplateInfoDTO;
use ProposalCrafter\App\Models\Settings;

class SettingsRepository {
	public function save_sender_info( SenderInfoDTO $dto ) {
		$this->save_settings( 'sender', $dto->to_array() );
	}

	public function get_sender_info() {
		return $this->get_settings( 'sender' );
	}

	public function save_template_info( TemplateInfoDTO $dto ) {
		$this->save_settings( 'template', $dto->to_array() );
	}

	public function get_template_info() {
		$settings = $this->get_settings( 'template' );

		return array_map(
			function( $value ) {
				if ( $value === '1' ) {
					  return true;
				}
				if ( $value === '0' ) {
					return false;
				}
				if ( $value === 'true' ) {
					return true;
				}
				if ( $value === 'false' ) {
					return false;
				}
				return $value;
			},
			$settings
		);
	}

	private function save_settings( string $group, array $settings ) {
		foreach ( $settings as $key => $value ) {
			$existing = Settings::query()
				->where( 'setting_key', $key )
				->where( 'setting_group', $group )
				->first();

			$val_to_store = $value;
			if ( is_bool( $value ) ) {
				$val_to_store = $value ? '1' : '0';
			}

			if ( empty( $val_to_store ) && $val_to_store !== '0' ) {
				if ( $existing ) {
					Settings::query()
						->where( 'setting_key', $key )
						->where( 'setting_group', $group )
						->delete();
				}
			} else {
				if ( $existing ) {
					Settings::query()
						->where( 'setting_key', $key )
						->where( 'setting_group', $group )
						->update( [ 'value' => $val_to_store ] );
				} else {
					Settings::query()->insert(
						[
							'setting_key'   => $key,
							'setting_group' => $group,
							'value'         => $val_to_store,
						]
					);
				}
			}
		}
	}

	private function get_settings( string $group ): array {
		$settings = Settings::query()->where( 'setting_group', $group )->get();

		$result = [];
		foreach ( $settings as $setting ) {
			$result[ $setting->setting_key ] = $setting->value;
		}

		return $result;
	}
}
