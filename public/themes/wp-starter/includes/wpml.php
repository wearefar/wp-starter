<?php

global $sitepress;

// Prevent loading WPML language selector CSS.
define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);

// Prevent loading WPML languages JavaScript.
define('ICL_DONT_LOAD_LANGUAGES_JS', true);

// Prevent WPML from displaying promotional messages.
define('ICL_DONT_PROMOTE', true);

// Remove the WPML block editor styles from being enqueued.
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wpml-blocks');
});

// Remove WPML's meta generator tag from the head section of the page.
remove_action('wp_head', array($sitepress, 'meta_generator_tag'));

// Override the default WPML translator check to always return true.
add_filter('wpml_override_is_translator', '__return_true');
