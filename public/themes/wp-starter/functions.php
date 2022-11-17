<?php

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__.'/includes/helpers.php';
require __DIR__.'/includes/dashboard.php';

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
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');

    $manifestPath = get_theme_file_path('assets/manifest.json');

    if (
        wp_get_environment_type() === 'local' &&
        is_array(wp_remote_get('http://localhost:5173/')) // is Vite.js running
    ) {
        wp_enqueue_script('vite', 'http://localhost:5173/@vite/client', [], null);
        wp_enqueue_script('app', 'http://localhost:5173/resources/js/app.js', [], null);
        wp_enqueue_style('app', 'http://localhost:5173/resources/css/app.css', [], null);
    } elseif (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        wp_enqueue_script('app', get_theme_file_uri('assets/' . $manifest['resources/js/app.js']['file']), [], null);
        wp_enqueue_style('app', get_theme_file_uri('assets/' . $manifest['resources/css/app.css']['file']), [], null);
    }
});

// Load scripts as modules.
add_filter('script_loader_tag', function (string $tag, string $handle, string $src) {
    if (in_array($handle, ['vite', 'app'])) {
        return '<script type="module" src="' . esc_url($src) . '" defer></script>';
    }

    return $tag;
}, 10, 3);

add_filter('use_block_editor_for_post_type', '__return_false');

add_action('phpmailer_init', function(PHPMailer $phpmailer) {
    $phpmailer->Host = env('MAIL_HOST', null);

    $phpmailer->setFrom(
        env('MAIL_FROM_ADDRESS', 'noreply@wearefar.com'),
        env('MAIL_FROM_NAME', 'FAR')
    );

    if (env('MAIL_MAILER', 'mail') === 'smtp') {
        $phpmailer->isSMTP();
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = env('MAIL_PORT', 25);
        $phpmailer->Username = env('MAIL_USERNAME', null);
        $phpmailer->Password = env('MAIL_PASSWORD', null);
        $phpmailer->SMTPSecure = env('MAIL_ENCRYPTION', '');
    }
});
