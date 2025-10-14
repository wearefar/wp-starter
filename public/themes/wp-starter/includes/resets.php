<?php

// Disable the admin bar on the front end.
show_admin_bar(false);

// Set permalink structure to use post names.
$GLOBALS['wp_rewrite']->set_permalink_structure('/%postname%/');

// Enable post thumbnails (featured images).
add_theme_support('post-thumbnails');

// Allow WordPress to manage the document title.
add_theme_support('title-tag');

// Disable WordPress emoji support.
add_filter('emoji_svg_url', '__return_false');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Clean up unnecessary links in the head.
remove_action('wp_head', 'wp_generator'); // WordPress version.
remove_action('wp_head', 'rsd_link'); // RSD link.
remove_action('wp_head', 'wlwmanifest_link'); // Windows Live Writer.
remove_action('wp_head', 'wp_shortlink_wp_head'); // Shortlink.
remove_action('wp_head', 'rest_output_link_wp_head'); // REST API link.
remove_action('wp_head', 'wp_oembed_add_discovery_links'); // oEmbed links.
remove_action('wp_head', 'feed_links_extra', 3); // Extra feed links.
remove_action('wp_head', 'wp_custom_css_cb', 101); // Custom CSS.

// Dequeue and deregister unnecessary scripts/styles.
add_action('wp_enqueue_scripts', function () {
    wp_deregister_script('wp-embed'); // Remove wp-embed script.
    wp_dequeue_style('wp-block-library'); // Remove block library styles.
    wp_dequeue_style('global-styles'); // Remove global styles.
    wp_dequeue_style('classic-theme-styles'); // Remove classic theme styles.
});

// Dequeue core block supports style in the footer.
add_action('wp_footer', function () {
    wp_dequeue_style('core-block-supports'); // Remove core block supports styles.
});

// Load specific scripts as modules.
add_filter('script_loader_tag', function (string $tag, string $handle, string $src) {
    if (in_array($handle, ['vite', 'app'])) {
        return '<script type="module" src="' . esc_url($src) . '" defer></script>';
    }
    return $tag;
}, 10, 3);

// Disable the block editor (Gutenberg) for all post types.
add_filter('use_block_editor_for_post_type', '__return_false');

// Remove the default WordPress welcome panel from the dashboard.
remove_action('welcome_panel', 'wp_welcome_panel');

// Clean up the WordPress dashboard by removing specific widgets.
add_action('wp_dashboard_setup', function () {
    // Access the global $wp_meta_boxes variable, which holds the dashboard widgets.
    global $wp_meta_boxes;

    // Array of dashboard widgets to remove, with their positions.
    $boxes = [
        'dashboard_activity' => 'normal',            // Activity widget
        'dashboard_recent_comments' => 'normal',     // Recent Comments widget
        'dashboard_right_now' => 'normal',           // At a Glance/Right Now widget
        'dashboard_primary' => 'side',               // WordPress News widget
        'dashboard_quick_press' => 'side',           // Quick Draft widget
        'dashboard_recent_drafts' => 'side',         // Recent Drafts widget
        'dashboard_site_health' => 'normal',         // Site Health Status widget
    ];

    // Loop through each widget and remove it from the dashboard.
    foreach ($boxes as $box => $position) {
        unset($wp_meta_boxes['dashboard'][$position]['core'][$box]);
    }
});

// Remove the "Comments" menu item from the admin sidebar.
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Customize the WordPress admin bar by removing certain menu items.
add_action('admin_bar_menu', function ($menu) {
    // Remove the WordPress logo from the admin bar.
    $menu->remove_node('wp-logo');
    // Remove the "Comments" item from the admin bar.
    $menu->remove_node('comments');
    // Remove the "New" content dropdown from the admin bar.
    $menu->remove_node('new-content');
}, 999);

// Customize the footer text in the WordPress admin dashboard.
add_filter('admin_footer_text', function () {
    return 'Code by <a href="https://domo-a.com" target="_blank">DOMO-A</a>';
});

// Customize the wysiwyg editor
add_filter('tiny_mce_before_init', function ($settings) {
    $settings['toolbar1'] = 'formatselect,bold,italic,bullist,numlist,blockquote,link,wp_adv';
    $settings['toolbar2'] = 'strikethrough,hr,pastetext,removeformat,charmap,undo,redo,wp_help';
    $settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6';

    $settings['paste_as_text'] = true;
    return $settings;
});
