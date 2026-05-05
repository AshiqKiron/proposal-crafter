<?php 
defined( 'ABSPATH' ) || exit;
proposal_crafter_block_start( $attributes );
?>
    <p class="pc-sender-email">
        <?php echo esc_html( $attributes['content'] ?? '' ); ?>
    </p>
<?php 
proposal_crafter_block_end();
