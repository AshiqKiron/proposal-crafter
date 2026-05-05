<?php
use ProposalCrafter\App\Repositories\SettingsRepository;

defined( 'ABSPATH' ) || exit;

proposal_crafter_block_start( $attributes );

// Get Currency from Global Settings
$proposal_crafter_settings_repo = new SettingsRepository();
$proposal_crafter_template_info = $proposal_crafter_settings_repo->get_template_info();
$proposal_crafter_currency_code = isset($proposal_crafter_template_info['currency']) ? $proposal_crafter_template_info['currency'] : 'USD';

$proposal_crafter_currency_symbols = [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥',
    'CAD' => 'C$',
    'AUD' => 'A$',
    'CHF' => 'Fr',
    'CNY' => '¥',
    'INR' => '₹',
    'BRL' => 'R$',
];

$proposal_crafter_currency_symbol = isset($proposal_crafter_currency_symbols[$proposal_crafter_currency_code]) ? $proposal_crafter_currency_symbols[$proposal_crafter_currency_code] : '$';

// Helper for formatting currency
if (!function_exists('proposal_crafter_format_currency')) {
    function proposal_crafter_format_currency($value, $symbol) {
        $proposal_crafter_is_negative = $value < 0;
        $proposal_crafter_abs_value = number_format(abs($value));
        return $proposal_crafter_is_negative ? '-' . $symbol . $proposal_crafter_abs_value : $symbol . $proposal_crafter_abs_value;
    }
}
?>
    <?php
    $proposal_crafter_items = isset($attributes['items']) ? $attributes['items'] : [];
    $proposal_crafter_subtotal_label = isset($attributes['subtotalLabel']) ? $attributes['subtotalLabel'] : 'Subtotal';
    $proposal_crafter_total_label = isset($attributes['totalLabel']) ? $attributes['totalLabel'] : 'Total';
    $proposal_crafter_show_subtotal = isset($attributes['showSubtotal']) ? $attributes['showSubtotal'] : true;
    $proposal_crafter_show_total = isset($attributes['showTotal']) ? $attributes['showTotal'] : true;
    $proposal_crafter_adjustments = isset($attributes['adjustments']) ? $attributes['adjustments'] : [];

    $proposal_crafter_subtotal_value = 0;
    foreach ($proposal_crafter_items as $proposal_crafter_item) {
        if (empty($proposal_crafter_item['isChecked'])) {
            continue;
        }
        $proposal_crafter_price = isset($proposal_crafter_item['price']) ? floatval($proposal_crafter_item['price']) : 0;
        $proposal_crafter_quantity = isset($proposal_crafter_item['quantity']) ? floatval($proposal_crafter_item['quantity']) : 1;
        $proposal_crafter_subtotal_value += $proposal_crafter_price * $proposal_crafter_quantity;
    }

    $proposal_crafter_adjustments_total = 0;
    foreach ($proposal_crafter_adjustments as $proposal_crafter_adjustment) {
        $proposal_crafter_val = isset($proposal_crafter_adjustment['value']) ? floatval($proposal_crafter_adjustment['value']) : 0;
        $proposal_crafter_amount_type = isset($proposal_crafter_adjustment['amountType']) ? $proposal_crafter_adjustment['amountType'] : 'fixed';
        
        if ($proposal_crafter_amount_type === 'percentage') {
            $proposal_crafter_val = ($proposal_crafter_val / 100) * $proposal_crafter_subtotal_value;
        }

        $proposal_crafter_operation = isset($proposal_crafter_adjustment['operation']) ? $proposal_crafter_adjustment['operation'] : (isset($proposal_crafter_adjustment['type']) ? $proposal_crafter_adjustment['type'] : 'addition');
        
        if ($proposal_crafter_operation === 'deduction') {
            $proposal_crafter_adjustments_total -= $proposal_crafter_val;
        } else {
            $proposal_crafter_adjustments_total += $proposal_crafter_val;
        }
    }
    $proposal_crafter_total_value = $proposal_crafter_subtotal_value + $proposal_crafter_adjustments_total;
    ?>
    <table 
        class="pc-pricing-table" 
        data-title="<?php echo esc_attr(isset($attributes['tableTitle']) ? $attributes['tableTitle'] : ''); ?>" 
        data-adjustments="<?php echo esc_attr(json_encode($proposal_crafter_adjustments)); ?>" 
        data-currency-symbol="<?php echo esc_attr($proposal_crafter_currency_symbol); ?>"
        data-block-id="<?php echo esc_attr($attributes['uniqueId']); ?>"
    >
        <thead>
            <tr>
                <th style="text-align: left;"><?php echo esc_html(isset($attributes['itemColumnLabel']) ? $attributes['itemColumnLabel'] : 'Item'); ?></th>
                <th style="text-align: right;"><?php echo esc_html(isset($attributes['priceColumnLabel']) ? $attributes['priceColumnLabel'] : 'Price'); ?></th>
                <th style="text-align: right;"><?php echo esc_html(isset($attributes['quantityColumnLabel']) ? $attributes['quantityColumnLabel'] : 'Quantity'); ?></th>
                <th style="text-align: right;"><?php echo esc_html(isset($attributes['totalColumnLabel']) ? $attributes['totalColumnLabel'] : 'Subtotal'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($proposal_crafter_items)) : ?>
                <?php foreach ($proposal_crafter_items as $proposal_crafter_item) : 
                    $proposal_crafter_price = isset($proposal_crafter_item['price']) ? floatval($proposal_crafter_item['price']) : 0;
                    $proposal_crafter_quantity = isset($proposal_crafter_item['quantity']) ? floatval($proposal_crafter_item['quantity']) : 1;
                    $proposal_crafter_row_total = $proposal_crafter_price * $proposal_crafter_quantity;
                ?>
                    <tr>
                        <td>
                            <label class="pc-pricing-item-wrapper" style="display: inline-flex; align-items: center; cursor: <?php echo !empty($proposal_crafter_item['isChecked']) ? 'default' : 'pointer'; ?>; <?php echo !empty($proposal_crafter_item['isChecked']) ? 'pointer-events: none;' : ''; ?>">
                                <input type="checkbox" class="pc-pricing-item-checkbox" data-price="<?php echo esc_attr($proposal_crafter_price); ?>" data-quantity="<?php echo esc_attr($proposal_crafter_quantity); ?>" style="margin-right: 4px;" <?php echo !empty($proposal_crafter_item['isChecked']) ? 'checked' : ''; ?>>
                                <div class="pc-pricing-item-title"><?php echo wp_kses_post(isset($proposal_crafter_item['title']) ? $proposal_crafter_item['title'] : ''); ?></div>
                            </label>
                            <div class="pc-pricing-item-description"><?php echo wp_kses_post(isset($proposal_crafter_item['description']) ? $proposal_crafter_item['description'] : ''); ?></div>
                        </td>
                        <td class="pc-pricing-column" style="vertical-align: top; text-align: right;">
                            <div style="display: flex; align-items: center; justify-content: flex-end;">
                                <span><?php echo esc_html(proposal_crafter_format_currency($proposal_crafter_price, $proposal_crafter_currency_symbol)); ?></span>
                            </div>
                        </td>
                        <td class="pc-pricing-column" style="vertical-align: top; text-align: right;">
                            <div><?php echo esc_html($proposal_crafter_quantity); ?></div>
                        </td>
                        <td class="pc-pricing-column" style="vertical-align: top; text-align: right;">
                            <div style="display: flex; align-items: center; justify-content: flex-end;">
                                <span><?php echo esc_html(proposal_crafter_format_currency($proposal_crafter_row_total, $proposal_crafter_currency_symbol)); ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <?php if ($proposal_crafter_show_subtotal || $proposal_crafter_show_total) : ?>
            <tfoot>
                <?php if ($proposal_crafter_show_subtotal) : ?>
                    <tr class="pc-subtotal-row">
                        <td colspan="3" style="text-align: right; vertical-align: middle;">
                            <?php echo wp_kses_post($proposal_crafter_subtotal_label); ?>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <div style="display: flex; align-items: center; justify-content: flex-end;">
                                <span class="pc-subtotal-value"><?php echo esc_html(proposal_crafter_format_currency($proposal_crafter_subtotal_value, $proposal_crafter_currency_symbol)); ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                
                <?php foreach ($proposal_crafter_adjustments as $proposal_crafter_adjustment) : 
                    $proposal_crafter_val = isset($proposal_crafter_adjustment['value']) ? floatval($proposal_crafter_adjustment['value']) : 0;
                    $proposal_crafter_amount_type = isset($proposal_crafter_adjustment['amountType']) ? $proposal_crafter_adjustment['amountType'] : 'fixed';
                    $proposal_crafter_operation = isset($proposal_crafter_adjustment['operation']) ? $proposal_crafter_adjustment['operation'] : (isset($proposal_crafter_adjustment['type']) ? $proposal_crafter_adjustment['type'] : 'addition');
                    $proposal_crafter_is_deduction = $proposal_crafter_operation === 'deduction';
                ?>
                    <tr class="pc-adjustments-row">
                        <td colspan="3" style="text-align: right; vertical-align: middle;">
                            <?php echo esc_html(isset($proposal_crafter_adjustment['label']) ? $proposal_crafter_adjustment['label'] : ''); ?>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <div style="display: flex; align-items: center; justify-content: flex-end;">
                                <span>
                                    <?php 
                                    if ($proposal_crafter_amount_type === 'percentage') {
                                        echo ($proposal_crafter_is_deduction ? '- ' : '') . esc_html(number_format($proposal_crafter_val) . '%');
                                    } else {
                                        $proposal_crafter_display_val = $proposal_crafter_is_deduction ? -$proposal_crafter_val : $proposal_crafter_val;
                                        echo esc_html(proposal_crafter_format_currency($proposal_crafter_display_val, $proposal_crafter_currency_symbol));
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($proposal_crafter_show_total) : ?>
                    <tr class="pc-total-row">
                        <td colspan="3" style="text-align: right; vertical-align: middle;">
                            <?php echo wp_kses_post($proposal_crafter_total_label); ?>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <div style="display: flex; align-items: center; justify-content: flex-end;">
                                <span class="pc-total-value"><?php echo esc_html(proposal_crafter_format_currency($proposal_crafter_total_value, $proposal_crafter_currency_symbol)); ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tfoot>
        <?php endif; ?>
    </table>
<?php 
proposal_crafter_block_end();