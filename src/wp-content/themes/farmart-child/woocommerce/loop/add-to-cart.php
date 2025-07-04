<?php

/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.2.0
 */

if (! defined('ABSPATH')) {
    exit;
}

global $product;

$aria_describedby = isset($args['aria-describedby_text']) ? sprintf('aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s"', esc_attr($product->get_id())) : '';
$condition1 = $product->is_type('simple') && !$product->get_price();
$condition2 = $product->is_type('simple') && !$product->is_in_stock();
if ($condition1 || $condition2) :
    echo apply_filters(
        'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
        sprintf(
            '<a role="button" %s data-quantity="%s" data-product-id="%s" class="default-enquire-button enquire-button enquiry-single-button" %s data-title="%s"><span class="p-icon" title="%s" data-rel="tooltip">%s</span><span class="add-to-cart-text">%s</span></a>',
            $aria_describedby,
            esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
            $product->get_id(),
            isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
            esc_attr($product->get_title()),
            esc_attr($product->add_to_cart_text()),
            "",
            esc_html("Enquire Now")
        ),
        $product,
        $args
    );
?>
    <div class="success-message" style="display: none;">
        Added to enquiry cart!
    </div>
<?php else:
    echo apply_filters(
        'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
        sprintf(
            '<a href="%s" %s data-quantity="%s" class="quantity_button %s" %s data-title="%s"><span class="p-icon" title="%s" data-rel="tooltip">%s</span><span class="add-to-cart-text">%s</span></a>',
            esc_url($product->add_to_cart_url()),
            $aria_describedby,
            esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
            esc_attr(isset($args['class']) ? $args['class'] : 'button'),
            isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
            esc_attr($product->get_title()),
            esc_attr($product->add_to_cart_text()),
            Farmart\Icon::get_svg('cart', '', 'shop'),
            esc_html($product->add_to_cart_text())
        ),
        $product,
        $args
    );
endif; ?>
<?php if (isset($args['aria-describedby_text'])) : ?>
    <span id="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" class="screen-reader-text">
        <?php echo esc_html($args['aria-describedby_text']); ?>
    </span>
<?php endif; ?>