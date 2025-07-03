<?php

function zippy_products_by_category_shortcode($atts)
{
    $atts = shortcode_atts([
        'category' => '',
        'limit'    => 10,
    ], $atts, 'products_by_category');

    if (empty($atts['category'])) {
        return '<p>Please provide a category slug.</p>';
    }

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => intval($atts['limit']),
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($atts['category']),
            ],
        ],
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No products found in this category.</p>';
    }

    $output = '<div></div>';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
    }
    wp_reset_postdata();
    $output .= '</ul>';

    return $output;
}

add_shortcode('products_by_category', 'zippy_products_by_category_shortcode');
