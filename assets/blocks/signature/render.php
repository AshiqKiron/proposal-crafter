<?php
defined( 'ABSPATH' ) || exit;

use ProposalCrafter\App\Repositories\SettingsRepository;

$proposal_crafter_unique_id = $attributes['uniqueId'] ?? '';
$proposal_crafter_user_type = $attributes['userType'] ?? 'sender';
$proposal_crafter_use_default_sender_signature = $attributes['useDefaultSenderSignature'] ?? true;
$proposal_crafter_placeholder = $attributes['placeholder'] ?? 'Signature';

$proposal_crafter_settings_repository = new SettingsRepository();

// Default: use block attributes
$proposal_crafter_final_signature_type = $attributes['signatureType'] ?? 'type';
$proposal_crafter_final_value = ''; 
$proposal_crafter_is_prefilled = false;

// Logic to determine signature type and value from Settings if needed
if ( $proposal_crafter_user_type === 'sender' && $proposal_crafter_use_default_sender_signature ) {
    $proposal_crafter_sender_info = $proposal_crafter_settings_repository->get_sender_info();
    if ( ! empty( $proposal_crafter_sender_info ) ) {
        $proposal_crafter_final_signature_type = $proposal_crafter_sender_info['signature_type'] ?? 'type';
        
        if ( $proposal_crafter_final_signature_type === 'type' ) {
            $proposal_crafter_final_value = $proposal_crafter_sender_info['signature_text'] ?? '';
        } else {
            $proposal_crafter_image_id = $proposal_crafter_sender_info['signature_image'] ?? '';
            // For upload, we want the URL for the preview
            if ( is_numeric( $proposal_crafter_image_id ) ) {
                $proposal_crafter_final_value = wp_get_attachment_url( $proposal_crafter_image_id );
            } else {
                $proposal_crafter_final_value = $proposal_crafter_image_id;
            }
        }
        $proposal_crafter_is_prefilled = true;
    }
}

// look for client signature
if ( $proposal_crafter_user_type === 'client' ) {
    $proposal_crafter_client_signature_type = get_post_meta( get_the_ID(), 'pc_client_signature_type', true );
    $proposal_crafter_client_signature_value = get_post_meta( get_the_ID(), 'pc_client_signature_value', true );

    if ( ! empty( $proposal_crafter_client_signature_value ) ) {
        $proposal_crafter_final_signature_type = $proposal_crafter_client_signature_type;
        $proposal_crafter_final_value          = $proposal_crafter_client_signature_value;
        $proposal_crafter_is_prefilled         = true;
    }
}

proposal_crafter_block_start( $attributes );
?>
    <div 
        class="signature-content signature-block-user-type-<?php echo esc_attr( $proposal_crafter_user_type ); ?>"
        data-signature-type="<?php echo esc_attr( $proposal_crafter_final_signature_type ); ?>"
        data-signature-value="<?php echo esc_attr( $proposal_crafter_final_value ); ?>"
        data-signature-user-type="<?php echo esc_attr( $proposal_crafter_user_type ); ?>"
    >
        <?php if ( $proposal_crafter_final_signature_type === 'type' ): ?>
            <div class="signature-mode-type">
                <input
                    type="text"
                    class="signature-type-input signature-content"
                    placeholder="<?php echo esc_attr( $proposal_crafter_placeholder ); ?>"
                    value="<?php echo esc_attr( $proposal_crafter_final_value ); ?>"
                    <?php echo $proposal_crafter_is_prefilled ? 'readonly' : ''; ?>
                />
            </div>
        <?php endif; ?>

        <?php if ( $proposal_crafter_final_signature_type === 'upload' ): ?>
            <div class="signature-mode-upload">
                <input
                    type="file"
                    class="signature-upload-input"
                    accept="image/png,image/jpg,image/jpeg"
                    id="signature-file-input-<?php echo esc_attr( $proposal_crafter_unique_id ); ?>"
                    style="display: none;"
                    <?php echo $proposal_crafter_is_prefilled ? 'disabled' : ''; ?>
                />
                
                <?php if ( ! $proposal_crafter_is_prefilled || empty( $proposal_crafter_final_value ) ): ?>
                    <label
                        for="signature-file-input-<?php echo esc_attr( $proposal_crafter_unique_id ); ?>"
                        class="signature-upload-label signature-content"
                    >
                        <?php echo wp_kses_post( $proposal_crafter_placeholder ); ?>
                    </label>
                <?php endif; ?>

                <label
                    for="signature-file-input-<?php echo esc_attr( $proposal_crafter_unique_id ); ?>"
                    class="signature-preview-wrapper"
                    style="<?php echo ( ! empty( $proposal_crafter_final_value ) ) ? 'display: block;' : 'display: none;' ?>"
                >
                    <img
                        class="signature-upload-preview"
                        alt="Signature Preview"
                        src="<?php echo esc_attr( $proposal_crafter_final_value ); ?>"
                        style="<?php echo ( empty( $proposal_crafter_final_value ) ) ? 'display: none;' : '' ?>"
                    />
                </label>
            </div>
        <?php endif; ?>
    </div>
    <div class="signature-error-message">
        Please sign the proposal.
    </div>
<?php
proposal_crafter_block_end();