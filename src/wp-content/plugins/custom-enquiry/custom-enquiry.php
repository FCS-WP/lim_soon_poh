<?php
/*
Plugin Name: Custom Enquiry Plugin
Plugin URI: https://example.com/
Description: Enquire plugin. Custom elementor widgets: Enquire Products, Enquiry Cart. Shortcode: enquire button, enquire page, enquire total.
Version: 3.0
Author: Zippy_Toanphd
Author URI: https://example.com/
License: GPLv2 or later
Text Domain: custom-enquiry-plugin
*/

if (! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

// Include
require_once plugin_dir_path(__FILE__) . 'src/enquiry-page/zippy_enquiry_page.php';
// JS
add_action('wp_enqueue_scripts', 'register_scripts');
function register_scripts()
{
    $version = time();
    wp_enqueue_script(
        'custom-enquiry-app',
        plugin_dir_url(__FILE__) . 'js/app.js',
        array('jquery'),
        $version,
        true
    );
    wp_localize_script('custom-enquiry-app', 'moveToEnquiry', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
    // Enqueue the CSS file
    wp_enqueue_style(
        'custom-enquiry-style',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        $version
    );
}
function custom_enquiry_include_files()
{
    if (get_option('custom_enquiry_cart_enabled')) {
        require_once plugin_dir_path(__FILE__) . 'src/enquiry-cart/zippy_enquiry_cart.php';
    }

    if (get_option('custom_enquiry_table_enabled')) {
        require_once plugin_dir_path(__FILE__) . 'src/enquiry-table/zippy_enquiry_table.php';
    }

    if (get_option('custom_enquiry_single_button_enabled')) {
        require_once plugin_dir_path(__FILE__) . 'src/enquiry-single-button/zippy_enquiry_single_button.php';
    }
    if (get_option('enable_international_enquiry')) {
        require_once plugin_dir_path(__FILE__) . 'src/international-enquiry/zippy_international_enquiry.php';
    }
}
add_action('plugins_loaded', 'custom_enquiry_include_files');

// Create Enquiry Page
function create_enquiry_page()
{
    $page_exists = get_page_by_title('Enquiry');

    if (!$page_exists) {
        $page_data = array(
            'post_title'    => 'Enquiry',
            'post_content'  => '[display_enquiry_cart_page]',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        );
        wp_insert_post($page_data);
    }
}
register_activation_hook(__FILE__, 'create_enquiry_page');
//
function custom_enquiry_menu()
{
    add_menu_page(
        __('Enquiry', 'custom-enquiry-plugin'),
        __('Enquiry', 'custom-enquiry-plugin'),
        'manage_options',
        'custom-enquiry-plugin',
        'custom_enquiry_admin_page',
        'dashicons-list-view',
        56
    );
}
add_action('admin_menu', 'custom_enquiry_menu');

function custom_enquiry_admin_page()
{
?>
    <div class="wrap">
        <h1><?php _e('Enquiry Management', 'custom-enquiry-plugin'); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=custom-enquiry-plugin&tab=general" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'general' ? 'nav-tab-active' : ''; ?>">
                <?php _e('General', 'custom-enquiry-plugin'); ?>
            </a>
        </h2>
        <div class="tab-content">
            <?php
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

            if ($active_tab == 'general') {
                custom_enquiry_general_page();
            }
            ?>
        </div>
    </div>
<?php
}

function custom_enquiry_general_page()
{
?>
    <h2><?php _e('General Settings', 'custom-enquiry-plugin'); ?></h2>
    <form method="post" action="options.php">
        <?php
        settings_fields('custom_enquiry_settings_group');
        do_settings_sections('custom-enquiry-plugin-general');
        submit_button();
        ?>
    </form>
<?php
}


function custom_enquiry_settings_init()
{
    register_setting('custom_enquiry_settings_group', 'custom_enquiry_cart_enabled');
    register_setting('custom_enquiry_settings_group', 'remove_add_to_cart_enabled');
    register_setting('custom_enquiry_settings_group', 'custom_enquiry_table_enabled');
    register_setting('custom_enquiry_settings_group', 'custom_enquiry_single_button_enabled');
    register_setting('custom_enquiry_settings_group', 'enable_international_enquiry');
    // register_setting('custom_enquiry_settings_group', 'custom_enquiry_whatsapp_number');
    register_setting('custom_enquiry_settings_group', 'custom_enquiry_receive_email');


    // Add Settings Sections
    add_settings_section(
        'custom_enquiry_general_section',
        __('Enable/Disable Enquiry Features', 'custom-enquiry-plugin'),
        'custom_enquiry_general_section_callback',
        'custom-enquiry-plugin-general'
    );

    add_settings_field(
        'custom_enquiry_cart_enabled',
        __('Enable Enquiry Cart', 'custom-enquiry-plugin'),
        'custom_enquiry_cart_enabled_callback',
        'custom-enquiry-plugin-general',
        'custom_enquiry_general_section'
    );

    add_settings_field(
        'custom_enquiry_table_enabled',
        __('Enable Enquiry Table', 'custom-enquiry-plugin'),
        'custom_enquiry_table_enabled_callback',
        'custom-enquiry-plugin-general',
        'custom_enquiry_general_section'
    );

    add_settings_field(
        'custom_enquiry_single_button_enabled',
        __('Enable Enquiry Single Button', 'custom-enquiry-plugin'),
        'custom_enquiry_single_button_enabled_callback',
        'custom-enquiry-plugin-general',
        'custom_enquiry_general_section'
    );
    add_settings_field(
        'enable_international_enquiry',
        __('Enable International Enquiry', 'custom-enquiry-plugin'),
        'enable_international_enquiry_callback',
        'custom-enquiry-plugin-general',
        'custom_enquiry_general_section'
    );
    // add_settings_field(
    //     'custom_enquiry_whatsapp_number',
    //     __('WhatsApp Number', 'custom-enquiry-plugin'),
    //     'custom_enquiry_whatsapp_number_callback',
    //     'custom-enquiry-plugin-general',
    //     'custom_enquiry_general_section'
    // );
    add_settings_field(
        'custom_enquiry_receive_email',
        __('Enquiry Email:', 'custom-enquiry-plugin'),
        'custom_enquiry_receive_email_callback',
        'custom-enquiry-plugin-general',
        'custom_enquiry_general_section'
    );
}
//
add_action('admin_init', 'custom_enquiry_settings_init');

function custom_enquiry_general_section_callback()
{
    echo '<p>' . __('Choose which features you want to enable or disable for the Enquiry Plugin.', 'custom-enquiry-plugin') . '</p>';
}

function custom_enquiry_cart_enabled_callback()
{
    $option = get_option('custom_enquiry_cart_enabled');
    echo '<input type="checkbox" name="custom_enquiry_cart_enabled" value="1" ' . checked(1, $option, false) . ' />';
    echo '<label for="custom_enquiry_cart_enabled"> ' . __('Enquiry Cart', 'custom-enquiry-plugin') . '</label>';
    echo '<p>' . __('Use Shortcode [enquiry_button_cart]', 'custom-enquiry-plugin') . '</p>';
}

function custom_enquiry_table_enabled_callback()
{
    $option = get_option('custom_enquiry_table_enabled');
    echo '<input type="checkbox" name="custom_enquiry_table_enabled" value="1" ' . checked(1, $option, false) . ' />';
    echo '<label for="custom_enquiry_table_enabled"> ' . __('Enquiry Table', 'custom-enquiry-plugin') . '</label>';
    echo '<br>';
    echo '<p>' . __('Use Shortcode [product_variation_table]', 'custom-enquiry-plugin') . '</p>';
}

function custom_enquiry_single_button_enabled_callback()
{
    $enabled = get_option('custom_enquiry_single_button_enabled');

    echo '<input type="checkbox" name="custom_enquiry_single_button_enabled" value="1" ' . checked(1, $enabled, false) . ' />';
    echo '<label for="custom_enquiry_single_button_enabled"> ' . __('Add to Enquiry Button', 'custom-enquiry-plugin') . '</label>';
    echo '<p>' . __('Use Shortcode [product_enquiry_button]', 'custom-enquiry-plugin') . '</p>';
}
function custom_enquiry_whatsapp_number_callback()
{
    $whatsapp_number = get_option('custom_enquiry_whatsapp_number', '');
    echo '<input type="text" name="custom_enquiry_whatsapp_number" value="' . esc_attr($whatsapp_number) . '" />';
    echo '<p>' . __('Enter the WhatsApp number (including country code) to send enquiry information.', 'custom-enquiry-plugin') . '</p>';
}

function custom_enquiry_receive_email_callback()
{
    $enquiry_email = get_option('custom_enquiry_receive_email', '');
    echo '<input type="email" name="custom_enquiry_receive_email" value="' . esc_attr($enquiry_email) . '" />';
    echo '<p>' . __('Enter the email address that will receive the enquiry information.', 'custom-enquiry-plugin') . '</p>';
}


function enable_international_enquiry_callback()
{
    $enabled = get_option('enable_international_enquiry');
    echo '<input type="checkbox" name="enable_international_enquiry" value="1" ' . checked(1, $enabled, false) . ' />';
    echo '<label for="enable_international_enquiry"> ' . __('Enable International Enquiry', 'custom-enquiry-plugin') . '</label>';
    echo '<p>' . __('Check this option to enable the international enquiry features.', 'custom-enquiry-plugin') . '</p>';
}

add_action('elementor/elements/categories_registered', function ($elements_manager) {
    $elements_manager->add_category(
        'zippy-elements',
        [
            'title' => __('Zippy Elements', 'child-theme'),
            'icon'  => 'eicon-mail',
        ]
    );
});

add_action('elementor/widgets/register', function ($widgets_manager) {
    require_once plugin_dir_path(__FILE__) . 'widgets/enquire-products/enquire-products-widget.php';
    require_once plugin_dir_path(__FILE__) . 'widgets/enquiry-cart/enquiry-cart-widget.php';
    $widgets_manager->register(new \Enquire_Products_Widget());
    $widgets_manager->register(new \Enquiry_Cart_Widget());
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('custom-enquire-products-style', plugin_dir_url(__FILE__) . 'css/widgets/enquire-products.css', [], 1.0);
    wp_enqueue_style('enquiry-cart-style', plugin_dir_url(__FILE__) . 'css/widgets/enquiry-cart.css', [], 1.0);
    wp_enqueue_script('custom-enquire-products-js', plugin_dir_url(__FILE__) . 'js/widgets/enquire-products.js', ['jquery'], null, true);
});

add_filter('woocommerce_locate_template', 'my_plugin_override_email_template', 99, 3);

function my_plugin_override_email_template($template, $template_name, $template_path)
{
    // Target only the admin new order email
    if ($template_name === 'emails/admin-new-order.php') {
        $plugin_template = plugin_dir_path(__FILE__) . 'woocommerce/emails/admin-new-order.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}

add_filter('woocommerce_email_subject_new_order', 'custom_email_subject_new_order', 10, 2);

function custom_email_subject_new_order($subject, $order)
{
    // You can customize this however you like
    if ($order->get_meta('_is_enquiry_order') === 'yes') {
        $custom_subject = '🛒 New Enquiry #' . $order->get_id();
        return $custom_subject;
    }
    return $subject;
}

add_filter('woocommerce_email_recipient_new_order', 'custom_new_order_email_recipient', 10, 2);

function custom_new_order_email_recipient($recipient, $order)
{
    $custom_email = get_option('custom_enquiry_receive_email');

    // Only apply if it's your custom enquiry order AND email is valid
    if ($order instanceof WC_Order && $order->get_meta('_is_enquiry_order') === 'yes' && is_email($custom_email)) {
        return $custom_email; // override default admin email
    }

    return $recipient; // fallback to default
}
