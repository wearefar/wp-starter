<?php

require 'includes/helpers.php';

add_action('after_setup_theme', function () {
    show_admin_bar(false);

    $GLOBALS['wp_rewrite']->set_permalink_structure('/%postname%/');

    load_theme_textdomain('package_slug', get_template_directory() . '/lang');

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

    register_nav_menus([
        'main' => __('Main navigation', 'package_name'),
    ]);

    add_filter('emoji_svg_url', '__return_false');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'feed_links_extra', 3);
});

add_action('wp_enqueue_scripts', function () {
    wp_deregister_script('wp-embed');

    wp_dequeue_style('wp-block-library');

    wp_enqueue_style('app', mix('css/app.css'), null, null);
    wp_enqueue_script('app', mix('js/app.js'), null, null, true);
});
