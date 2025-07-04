<?php
function display_enquiry_cart_page()
{
    if (!is_admin()) {
        if (isset(WC()->session)) {
            if (!is_admin() && !WC()->session->has_session()) {
                WC()->session->set_customer_session_cookie(true);
            }
        }
        $enquiry_cart = WC()->session->get('enquiry_cart', array());

        // Merge duplicate products
        $merged_cart = array();
        foreach ($enquiry_cart as $item) {
            $product_id = isset($item['product_id']) ? $item['product_id'] : null;
            $variation_id = isset($item['variation_id']) ? $item['variation_id'] : null;
            $key = ($product_id ? $product_id : '') . ($variation_id ? $variation_id : '');
            if ($key != '') {
                if (isset($merged_cart[$key])) {
                    $merged_cart[$key]['quantity'] += $item['quantity'];
                } else {
                    $merged_cart[$key] = $item;
                }
            }
        }
        $enquiry_cart = $merged_cart;

        if (empty($enquiry_cart)) {
            echo '<div class="row" style="margin-top: 50px">Your enquiry cart is empty.</div>';
            return;
        }

        ob_start();
?>
        <div class="woocommerce enquiry-table">
            <form method="post" id="enquiry_cart_form">
                <div class="table-enquiry-page">
                    <table class="enquiry-cart-table">
                        <thead>
                            <tr>
                                <th class="product-name">Product</th>
                                <th class="quantity">Quantity</th>
                                <th class="remove">Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($enquiry_cart as $key => $item) {
                                $product = isset($item['variation_id']) && $item['variation_id'] ? wc_get_product($item['variation_id']) : wc_get_product($item['product_id']);
                                $product_name = $product->get_name();
                                $product_link = $product->get_permalink();
                                $product_image = $product->get_image();
                                $quantity = $item['quantity'];
                            ?>
                                <tr class="table_cart_enquiry_item" data-item-key="<?php echo $key; ?>">
                                    <td>
                                        <div class="d-flex align-items-center enquiry-product">
                                            <div class="product-image">
                                                <a href="<?php echo $product_link; ?>">
                                                    <?php echo $product_image; ?>
                                                </a>
                                            </div>
                                            <div class="enquiry-product-info">
                                                <a href="<?php echo $product_link; ?>">
                                                    <?php echo $product_name; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="enquiry-product-quantity quantity w-100 d-flex justify-content-center">
                                            <input type="button" value="-" class="qty_button minus" data-item-key="<?php echo $key; ?>">
                                            <input type="number" step="1" min="1" name="quantity[<?php echo $key; ?>]" value="<?php echo $quantity; ?>" title="Qty" class="input-text qty text" size="4" />
                                            <input type="button" value="+" class="qty_button plus" data-item-key="<?php echo $key; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="enquiry-product-remove w-100 d-flex justify-content-center">
                                            <a href="#" class="remove remove-enquiry-item" aria-label="Remove <?php echo $product_name; ?> from cart" data-item-key="<?php echo $key; ?>">×</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="my-enquiry-actions d-flex justify-content-between align-items-center">
                    <div class="back-to-shop"><a href="/shop/">Back to Shop</a></div>
                    <div class="update-enquiry-cart-button"><button type="submit" name="update_enquiry_cart" value="1">Update Listing</button></div>
                </div>
            </form>
        </div>
        <div class="enquiry-form w-100">
            <h2>Send Enquiry</h2>
            <form method="post" action="">
                <div class="form-wrapped">
                    <div class="w-50">
                        <label for="enquiry_name">Your Name<span class="text-danger">*</span>:</label>
                        <input type="text" id="enquiry_name" name="enquiry_name" required>
                    </div>
                    <div class="w-50">
                        <label for="enquiry_email">Your Email<span class="text-danger">*</span>:</label>
                        <input type="email" id="enquiry_email" name="enquiry_email" required>
                    </div>
                </div>
                <div class="w-100">
                    <label for="enquiry_message">Message:</label>
                    <textarea id="enquiry_message" name="enquiry_message" rows="4"></textarea>
                </div>
                <div>
                    <button type="submit" name="send_enquiry" value="1">Confirm Enquiry</button>
                </div>
            </form>
        </div>
<?php

        if (isset($_POST['send_enquiry']) && $_POST['send_enquiry'] == 1) {
            handle_enquiry_form_submission($enquiry_cart);
        }

        return ob_get_clean();
    }
}
function update_enquiry_cart()
{
    if (isset($_POST['quantity'])) {
        $enquiry_cart = WC()->session->get('enquiry_cart', array());
        foreach ($_POST['quantity'] as $key => $quantity) {
            if ($quantity > 0) {
                $enquiry_cart[$key]['quantity'] = intval($quantity);
            } else {
                unset($enquiry_cart[$key]);
            }
        }
        WC()->session->set('enquiry_cart', $enquiry_cart);
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'No quantity data found.'));
    }
}
add_action('wp_ajax_update_enquiry_cart', 'update_enquiry_cart');
add_action('wp_ajax_nopriv_update_enquiry_cart', 'update_enquiry_cart');
function remove_enquiry_item()
{

    $item_key = isset($_POST['item_key']) ? sanitize_text_field($_POST['item_key']) : '';
    if (empty($item_key)) {
        wp_send_json_error(array('message' => 'Invalid item key.'));
    }
    if (isset(WC()->session)) {
        if (!is_admin() && !WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }

    $enquiry_cart = WC()->session->get('enquiry_cart', array());
    unset($enquiry_cart[$item_key]);

    WC()->session->set('enquiry_cart', $enquiry_cart);

    wp_send_json_success();
}
add_action('wp_ajax_remove_enquiry_item', 'remove_enquiry_item');
add_action('wp_ajax_nopriv_remove_enquiry_item', 'remove_enquiry_item');

function handle_enquiry_form_submission($enquiry_cart)
{
    // Sanitize user inputs
    $name    = sanitize_text_field($_POST['enquiry_name'] ?? '');
    $email   = sanitize_email($_POST['enquiry_email'] ?? '');
    $message = sanitize_textarea_field($_POST['enquiry_message'] ?? '');
    $enquiry_email = get_option('custom_enquiry_receive_email', '');

    if (empty($enquiry_cart)) {
        echo '<div class="woocommerce-error">No products found in the enquiry cart.</div>';
        return;
    }

    // Create a WooCommerce order
    $order = wc_create_order();

    foreach ($enquiry_cart as $item) {
        $product = !empty($item['variation_id']) ? wc_get_product($item['variation_id']) : wc_get_product($item['product_id']);
        $quantity = intval($item['quantity']);

        if ($product && $quantity > 0) {
            $order->add_product($product, $quantity);
        }
    }

    // Set minimal billing address
    $order->set_address([
        'first_name' => $name,
        'email'      => $email
    ], 'billing');

    // Save custom meta
    $order->update_meta_data('_enquiry_message', $message);
    $order->update_meta_data('_is_enquiry_order', 'yes');

    // Update status to pending (not pending-payment since it's an enquiry)
    $order->set_status('pending', 'Enquiry placed by customer.');
    $order->save();

    // Trigger WooCommerce's default admin email
    WC()->mailer()->get_emails()['WC_Email_New_Order']->trigger($order->get_id());

    // Send custom email to configured address
    if (!empty($enquiry_email) && is_email($enquiry_email)) {
        $subject = 'New Enquiry Submitted';
        $message_body = "Name: {$name}\nEmail: {$email}\nMessage:\n{$message}\n\nOrder ID: #" . $order->get_id();
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($enquiry_email, $subject, $message_body, $headers);
    }

    // Clear enquiry cart session
    WC()->session->set('enquiry_cart', []);

    // Show success message
    echo '<div class="woocommerce-message" style="margin-top: 20px;">Your enquiry has been sent successfully as an order.</div>';
}


function add_enquiry_message_to_order_email($order, $sent_to_admin, $plain_text, $email)
{
    if ($sent_to_admin && $order->get_meta('_is_enquiry_order') === 'yes') {
        $enquiry_message = $order->get_meta('_enquiry_message');
        if ($enquiry_message) {
            echo '<h2>Message</h2><p>' . nl2br(esc_html($enquiry_message)) . '</p>';
        }
    }
}
add_action('woocommerce_email_order_meta', 'add_enquiry_message_to_order_email', 10, 4);

add_shortcode('display_enquiry_cart_page', 'display_enquiry_cart_page');

?>