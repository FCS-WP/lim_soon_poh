<?php

add_action('woocommerce_review_order_before_submit', 'hide_place_order_button_outside_singapore');
function hide_place_order_button_outside_singapore()
{
    $country = WC()->customer->get_shipping_country();

    if ($country !== 'SG') {
        ?>
        <style>
            #payment {
                display: none !important;
            }
        </style>
        <div class="woocommerce-error">
            <?php _e('Orders can only be placed if the shipping destination is Singapore. Please contact us for more information.', 'woocommerce'); ?>
        </div>
        <?php
    }
}

add_action('woocommerce_checkout_process', 'restrict_checkout_outside_shipping_zone');
add_action('woocommerce_after_checkout_validation', 'restrict_checkout_outside_shipping_zone', 10, 2);

function restrict_checkout_outside_shipping_zone()
{
    $country = WC()->customer->get_shipping_country();
    $shipping_zones = WC_Shipping_Zones::get_zones();
    $is_in_zone = false;

    foreach ($shipping_zones as $zone) {
        $zone_locations = $zone['zone_locations'];
        foreach ($zone_locations as $location) {
            if ($location['type'] === 'country' && $location['code'] === $country) {
                $is_in_zone = true;
                break 2;
            }
        }
    }

    if (!$is_in_zone) {
        wc_add_notice(__('Shipping is not available to your country. Please contact us to discuss shipping fees.', 'woocommerce'), 'error');
    }
}

function custom_no_shipping_message_and_redirect($message) {
    $enquiry_page_url = add_query_arg('action', 'move_to_enquiry', site_url('/enquiry/'));

    $message = sprintf(
        __('Shipping is not available to your location. Please contact us to discuss shipping fees or <a href="%s" class="move-to-enquiry-link">submit an enquiry</a>.', 'woocommerce'),
        esc_url($enquiry_page_url)
    );

    return $message;
}
add_filter('woocommerce_cart_no_shipping_available_html', 'custom_no_shipping_message_and_redirect');
add_filter('woocommerce_no_shipping_available_html', 'custom_no_shipping_message_and_redirect');

// AJAX move to enquiry
add_action('wp_ajax_move_to_enquiry', 'ajax_move_to_enquiry');
add_action('wp_ajax_nopriv_move_to_enquiry', 'ajax_move_to_enquiry');

function ajax_move_to_enquiry() {
    if (isset(WC()->session)) {
        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }

    $enquiry_cart = WC()->session->get('enquiry_cart', array());

    $cart = WC()->cart->get_cart();

    foreach ($cart as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $variation_id = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : null;
        $quantity = $cart_item['quantity'];

        $key = ($product_id ? $product_id : '') . ($variation_id ? $variation_id : '');
        if (isset($enquiry_cart[$key])) {
            $enquiry_cart[$key]['quantity'] += $quantity;
        } else {
            $enquiry_cart[$key] = array(
                'product_id'   => $product_id,
                'variation_id' => $variation_id,
                'quantity'     => $quantity,
            );
        }
    }

    WC()->session->set('enquiry_cart', $enquiry_cart);

    WC()->cart->empty_cart();

    wp_send_json_success(array(
        'redirect_url' => site_url('/enquiry/'),
    ));
}
