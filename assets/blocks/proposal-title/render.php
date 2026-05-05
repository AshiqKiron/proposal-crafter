<?php 
defined( 'ABSPATH' ) || exit;
$tag       = $attributes['htmlTag'] ?? 'h2';

proposal_crafter_block_start( $attributes );
?>
    <<?php echo esc_html( $tag ); ?> class="pc-proposal-title">
        <?php echo esc_html( $attributes['content'] ?? '' ); ?>
    </<?php echo esc_html( $tag ); ?>>
<?php 
proposal_crafter_block_end();