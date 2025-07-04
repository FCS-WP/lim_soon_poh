<?php
// Enquiry button shortcode [enquiry_button_cart]
function enquiry_button_shortcode()
{
    if (!WC()->session) {
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
      }
      
    $enquiry_cart = WC()->session->get('enquiry_cart', array());

    $enquiry_count = 0;
    foreach ($enquiry_cart as $item) {
        $enquiry_count += intval($item['quantity']);
    }

    ob_start();
?>
    <div class="enquiry-cart">
        <button class="enquiry-button-cart">
            <a href="/enquiry/">Enquiry | <span><?php echo $enquiry_count; ?></span> </a>
        </button>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('enquiry_button_cart', 'enquiry_button_shortcode');

?>