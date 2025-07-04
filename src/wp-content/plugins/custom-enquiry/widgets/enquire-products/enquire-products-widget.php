<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) exit;

class Enquire_Products_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'enquire_products';
    }

    public function get_title()
    {
        return __('Enquire Products', 'child-theme');
    }

    public function get_icon()
    {
        return 'eicon-products';
    }

    public function get_categories()
    {
        return ['zippy-elements'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Product Settings', 'child-theme'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ],
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => __('Products Per Page', 'child-theme'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
            ],
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => __('Products Per Page', 'child-theme'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
            ]
        );

        $this->add_control(
            'manual_selection',
            [
                'label'       => __('Manual Product Selection', 'child-theme'),
                'type'        => Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple'    => true,
                'options'     => $this->get_all_products(),
                'description' => __('If products are selected here, they will override other filters.', 'child-theme'),
            ]
        );

        $this->add_control(
            'product_cat',
            [
                'label' => __('Product Categories', 'child-theme'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_categories(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'child-theme'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'child-theme'),
                    'title' => __('Title', 'child-theme'),
                    'price' => __('Price', 'child-theme'),
                    'rand' => __('Random', 'child-theme'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'child-theme'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => __('Descending', 'child-theme'),
                    'ASC' => __('Ascending', 'child-theme'),
                ],
            ]
        );

        $this->add_control(
            'product_type',
            [
                'label' => __('Product Type', 'child-theme'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all'       => __('All Products', 'child-theme'),
                    'featured'  => __('Featured Products', 'child-theme'),
                    'sale'      => __('Sale Products', 'child-theme'),
                    'bestsellers' => __('Best Sellers', 'child-theme'),
                ],
            ]
        );

        // Show/Hide Price
        $this->add_control(
            'show_price',
            [
                'label' => __('Show Price', 'child-theme'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'child-theme'),
                'label_off' => __('Hide', 'child-theme'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // Enquire Button Text
        $this->add_control(
            'enquire_text',
            [
                'label' => __('Enquire Button Text', 'child-theme'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Enquire', 'child-theme'),
                'placeholder' => __('Enter button text', 'child-theme'),
            ]
        );


        $this->end_controls_section();
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __('Layout Settings', 'child-theme'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Product gap/spacing
        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __('Spacing Between Items', 'child-theme'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-products-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Products per row
        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Products Per Row', 'child-theme'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class' => 'custom-grid-cols-',
            ]
        );

        $this->end_controls_section();
        $this->register_style_controls();
    }

    protected function register_style_controls()
    {

        // === Product Title ===
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => __('Product Title', 'child-theme'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Text Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .custom-product-item h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .custom-product-item h3',
            ]
        );

        $this->end_controls_section();

        // === Product Price ===
        $this->start_controls_section(
            'style_price_section',
            [
                'label' => __('Product Price', 'child-theme'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Text Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .custom-product-item .price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .custom-product-item .price',
            ]
        );

        $this->end_controls_section();

        // === Enquire Button ===
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => __('Enquire Button', 'child-theme'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Alignment
        $this->add_responsive_control(
            'button_align',
            [
                'label' => __('Alignment', 'child-theme'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'child-theme'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'child-theme'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'child-theme'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .enquire-button',
            ]
        );

        // Text color
        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background
        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .enquire-button',
            ]
        );

        // Border radius
        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'child-theme'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Padding
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'child-theme'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Margin
        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __('Margin', 'child-theme'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Hover Effects
        $this->start_controls_tabs('button_hover_tabs');

        // Normal state
        $this->start_controls_tab(
            'button_tab_normal',
            [
                'label' => __('Normal', 'child-theme'),
            ]
        );

        // Already added above

        $this->end_controls_tab();

        // Hover state
        $this->start_controls_tab(
            'button_tab_hover',
            [
                'label' => __('Hover', 'child-theme'),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Text Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => __('Background Color', 'child-theme'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .enquire-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_hover_border',
                'selector' => '{{WRAPPER}} .enquire-button:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => $settings['products_per_page'],
            'order'          => $settings['order'],
        ];

        switch ($settings['orderby']) {
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                break;

            case 'title':
                $args['orderby'] = 'title';
                break;

            case 'rand':
                $args['orderby'] = 'rand';
                break;

            case 'date':
            default:
                $args['orderby'] = 'date';
                break;
        }

        // ✅ Manual product selection
        if (! empty($settings['manual_selection'])) {
            $args['post__in'] = $settings['manual_selection'];
            $args['orderby']  = 'post__in'; // preserve manual order
        } else {
            // 🏷️ Filter by category
            if (! empty($settings['product_cat'])) {
                $args['tax_query'][] = [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $settings['product_cat'],
                    ],
                ];
            }

            // 🎯 Product type filters
            switch ($settings['product_type']) {
                case 'featured':
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                        'operator' => 'IN',
                    ];
                    break;

                case 'sale':
                    $args['meta_query'][] = [
                        'key'     => '_sale_price',
                        'value'   => 0,
                        'compare' => '>',
                        'type'    => 'NUMERIC',
                    ];
                    break;

                case 'bestsellers':
                    $args['meta_key'] = 'total_sales';
                    $args['orderby']  = 'meta_value_num';
                    break;
            }
        }

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            // Outer wrapper (optional, useful for spacing or sectioning)
            echo '<div class="custom-products-grid-wrapper">';

            // Grid layout container (uses CSS grid + Elementor responsive class)
            echo '<div class="custom-products-grid">';

            while ($loop->have_posts()) : $loop->the_post();
                global $product;
?>
                <div class="custom-product-item">
                    <div>
                        <a href="<?php the_permalink(); ?>"><?php echo $product->get_image(); ?></a>
                        <h3><?php the_title(); ?></h3>
                    </div>

                    <?php if ($settings['show_price'] === 'yes') : ?>
                        <div>
                            <span class="price"><?php echo $product->get_price_html(); ?></span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <button class="enquire-button enquiry-single-button" data-product-id="<?php echo $product->get_id(); ?>">
                            <?php echo esc_html($settings['enquire_text'] ?: 'Enquire'); ?>
                        </button>
                        <div class="success-message" style="display: none;">
                            Added to enquiry cart!
                        </div>
                    </div>
                </div>
<?php
            endwhile;

            echo '</div>'; // .custom-products-grid
            echo '</div>'; // .custom-products-grid-wrapper
        } else {
            echo __('No products found', 'child-theme');
        }

        wp_reset_postdata();
    }


    public function get_style_depends()
    {
        return ['custom-enquire-products-style'];
    }

    public function get_script_depends()
    {
        return ['custom-enquire-products-js'];
    }

    /**
     * Helper Function
     */

    private function get_product_categories()
    {
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $categories = [];

        foreach ($terms as $term) {
            $categories[$term->slug] = $term->name;
        }

        return $categories;
    }

    private function get_all_products()
    {
        $products = get_posts([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $options = [];

        foreach ($products as $product) {
            $options[$product->ID] = $product->post_title;
        }

        return $options;
    }
}
