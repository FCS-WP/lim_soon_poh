<?php
// Display single product do_shortcode('[add_to_enquiry_button]')
function product_enquiry_button_shortcode($atts)
{
  global $product;

  if (!$product) {
    return 'No product found.';
  }

  $product_id = $product->get_id();
  $product_title = $product->get_name();
  ob_start();

  if ('simple' === $product->get_type()) {
?>
    <?php if (!$product->get_price() || !$product->is_purchasable()) : ?>
      <div class="enquiry-button-single">
        <button class="enquiry-single-button" product-id="<?php echo esc_attr($product_id); ?>" data-product-title="<?php echo esc_attr($product_title); ?>">Add To Enquiry</button>
        <span class="success-message" id="success-message" style="display:none;">Items added successfully!</span>
      </div>
    <?php endif ?>
  <?php
  } elseif ('variable' === $product->get_type()) {
  ?>
    <?php if (!$product->get_price() || !$product->is_purchasable()) : ?>
      <div class="enquiry-button-variable">
        <button type="button" id="enquiry-variable-button" data-product-id="<?php echo esc_attr($product_id); ?>" data-product-title="<?php echo esc_attr($product_title); ?>">Add To Enquiry</button>
        <span id="success-message" style="display:none;">Items added successfully!</span>
      </div>
    <?php endif; ?>
<?php
  } else {
    return 'Unsupported product type.';
  }

  return ob_get_clean();
}

add_shortcode('add_to_enquiry_button', 'product_enquiry_button_shortcode');

add_action('wp_ajax_add_to_enquiry_cart', 'add_to_enquiry_cart');
add_action('wp_ajax_nopriv_add_to_enquiry_cart', 'add_to_enquiry_cart');

function add_to_enquiry_cart()
{
  if (isset($_POST['products']) && is_array($_POST['products'])) {
    $products = $_POST['products'];
    // Early initialize customer session
    if (isset(WC()->session) && ! WC()->session->has_session()) {
      WC()->session->set_customer_session_cookie(true);
    }
    if (!empty($products['enquiry'])) {
      $product = reset($products['enquiry']);
      $product_id = intval($product['productId']);
      $quantity = intval($product['quantity']);


      if ($product_id > 0 && $quantity > 0) {
        $enquiry_cart = WC()->session->get('enquiry_cart', array());
        if (isset($enquiry_cart[$product_id])) {
          $enquiry_cart[$product_id]['quantity'] += $quantity;
        } else {
          $enquiry_cart[$product_id] = array(
            'product_id' => $product_id,
            'quantity' => $quantity,
          );
        }
        WC()->session->set('enquiry_cart', $enquiry_cart);
        wp_send_json(['success' => true, 'message' => 'added_to_enquiry_cart']);
      } else {
        wp_send_json_error('Invalid product ID or quantity.');
      }
    } else {
      wp_send_json_error('No products to process.');
    }
  } else {
    wp_send_json_error('Invalid request.');
  }
}

add_action('wp_ajax_add_variation_to_enquiry', 'add_variation_to_enquiry');
add_action('wp_ajax_nopriv_add_variation_to_enquiry', 'add_variation_to_enquiry');

function add_variation_to_enquiry()
{
  if (!isset($_POST['products']) || !is_array($_POST['products'])) {
    wp_send_json_error('No products to process.');
    return;
  }

  if (isset(WC()->session) && !WC()->session->has_session()) {
    WC()->session->set_customer_session_cookie(true);
  }

  $products = $_POST['products'];

  if (!empty($products['enquiry'])) {
    foreach ($products['enquiry'] as $product) {
      $variation_id = intval($product['variationId']);
      $quantity = intval($product['quantity']);

      if ($variation_id > 0 && $quantity > 0) {
        $enquiry_cart = WC()->session->get('enquiry_cart', array());

        if (isset($enquiry_cart[$variation_id])) {
          $enquiry_cart[$variation_id]['quantity'] += $quantity;
        } else {
          $enquiry_cart[$variation_id] = array(
            'variation_id' => $variation_id,
            'quantity' => $quantity
          );
        }

        WC()->session->set('enquiry_cart', $enquiry_cart);
      }
    }
  }
  wp_send_json_success('Products processed successfully.');
}

function add_to_enquiry_btn()
{
  if (function_exists('is_product') && is_product()) {
    global $product;
    if ($product && $product->is_type('variable')) {
      echo do_shortcode('[add_to_enquiry_button]');
    } else {
      return;
    }
  }
}
add_action('woocommerce_after_add_to_cart_button', 'add_to_enquiry_btn', 30);

//display enquiry for simple product
// function display_button_enquiry_for_simple_no_price()
// {
//   if (function_exists('is_product') && is_product()) {
//     global $product;
//     if ($product && $product->is_type('simple')) {
//       echo do_shortcode('[add_to_enquiry_button]');
//     } else {
//       return;
//     }
//   }
// }

// add_action('woocommerce_after_add_to_cart_form', 'display_button_enquiry_for_simple_no_price', 10);
