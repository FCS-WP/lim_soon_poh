<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (! defined('ABSPATH')) exit;

class Enquiry_Cart_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'enquiry_cart';
    }

    public function get_title()
    {
        return __('Enquiry Cart', 'child-theme');
    }

    public function get_icon()
    {
        return 'eicon-cart';
    }

    public function get_categories()
    {
        return ['zippy-elements'];
    }

    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Cart Icon', 'child-theme'),
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => __('Icon', 'child-theme'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'eicon-cart',
                    'library' => 'elementor',
                ],
            ]
        );

        $this->add_control(
            'show_badge',
            [
                'label' => __('Show Badge', 'child-theme'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'child-theme'),
                'label_off' => __('No', 'child-theme'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'label_text',
            [
                'label' => __('Label Text', 'child-theme'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Enquiry', 'child-theme'),
            ]
        );

        $this->end_controls_section();

        // Style section
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'child-theme'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'child-theme'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquiry-cart-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg',
            [
                'label' => __('Background', 'child-theme'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquiry-cart-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'child-theme'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 10, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}} .enquiry-cart-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .enquiry-cart-label',
            ]
        );
        $this->add_control(
            'redirect_url',
            [
                'label' => __('Redirect URL', 'child-theme'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-site.com/enquiry-cart',
                'description' => __('Enter the URL to redirect when cart icon is clicked.', 'child-theme'),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (!WC()->session) {
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }

        $enquiry_cart = WC()->session->get('enquiry_cart', array());

        $enquiry_count = 0;
        foreach ($enquiry_cart as $item) {
            $enquiry_count += intval($item['quantity']);
        }

        $redirect_url = !empty($settings['redirect_url']['url']) ? $settings['redirect_url']['url'] : false;
        $target_attr = $settings['redirect_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow_attr = $settings['redirect_url']['nofollow'] ? ' rel="nofollow"' : '';

        echo '<div class="enquiry-cart-wrapper">';

        if ($settings['label_text']) {
            echo '<div class="enquiry-cart-label">' . esc_html($settings['label_text']) . '</div>';
        }

        echo '<div>';

        // Start icon box
        if ($redirect_url) {
            echo '<a href="' . esc_url($redirect_url) . '"' . $target_attr . $nofollow_attr . '>';
        }

        echo '<div class="enquiry-cart-iconbox">';

        if ($settings['show_badge'] === 'yes') {
            echo '<span class="enquiry-cart-badge">' . esc_html($enquiry_count) . '</span>';
        }

        if (! empty($settings['icon']['value'])) {
            echo '<span class="enquiry-cart-icon">';
            \Elementor\Icons_Manager::render_icon(
                $settings['icon'],
                [
                    'aria-hidden' => 'true',
                ]
            );
            echo '</span>';
        }

        echo '</div>'; // .enquiry-cart-iconbox

        if ($redirect_url) {
            echo '</a>';
        }

        echo '</div>'; // inner wrapper
        echo '</div>'; // .enquiry-cart-wrapper
    }

    public function get_style_depends()
    {
        return ['enquiry-cart-style'];
    }
}
