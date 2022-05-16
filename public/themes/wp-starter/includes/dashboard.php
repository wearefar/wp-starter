<?php

add_action('wp_dashboard_setup', 'clean_dashboard');
add_action('admin_menu', 'clean_admin_menu');
add_action('admin_bar_menu', 'clean_admin_bar', 999);
add_filter('admin_footer_text', function () {
    return 'Code by <a href="https://wearefar.com" target="_blank">FAR</a>';
});

function clean_dashboard()
{
    global $wp_meta_boxes;

    $boxes = [
        'dashboard_activity' => 'normal',
        'dashboard_incoming_links' => 'normal',
        'dashboard_plugins' => 'normal',
        'dashboard_recent_comments' => 'normal',
        'dashboard_right_now' => 'normal',
        'dashboard_primary' => 'side',
        'dashboard_quick_press' => 'side',
        'dashboard_recent_drafts' => 'side',
        'dashboard_secondary' => 'side',
        'dashboard_site_health' => 'normal',
    ];

    foreach ($boxes as $box => $position) {
        unset($wp_meta_boxes['dashboard'][$position]['core'][$box]);
    }

    remove_action('welcome_panel', 'wp_welcome_panel');
}

function clean_admin_menu()
{
    remove_menu_page('edit-comments.php');
}

function clean_admin_bar($menu)
{
    $menu->remove_node('wp-logo');
    $menu->remove_node('comments');
    $menu->remove_node('new-content');
}
