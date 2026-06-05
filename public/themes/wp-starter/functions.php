<?php

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__.'/includes/resets.php';
require __DIR__.'/includes/helpers.php';
require __DIR__.'/includes/acf.php';

if (class_exists('SitePress')) {
    require __DIR__.'/includes/wpml.php';
}

add_action('after_setup_theme', function () {
    load_theme_textdomain('package_slug', get_template_directory() . '/lang');

    register_nav_menus([
        'main' => __('Main navigation', 'package_slug'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    $manifestPath = get_theme_file_path('build/.vite/manifest.json');

    if (
        wp_get_environment_type() === 'local' &&
        is_array(wp_remote_get('http://localhost:5173/')) // is Vite.js running
    ) {
        wp_enqueue_script('vite', 'http://localhost:5173/@vite/client', [], null);
        wp_enqueue_script('app', 'http://localhost:5173/resources/js/app.js', [], null);
        wp_enqueue_style('app', 'http://localhost:5173/resources/css/app.css', [], null);
    } elseif (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        wp_enqueue_script('app', get_theme_file_uri('build/' . $manifest['resources/js/app.js']['file']), [], null);
        wp_enqueue_style('app', get_theme_file_uri('build/' . $manifest['resources/css/app.css']['file']), [], null);
    }
});

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
