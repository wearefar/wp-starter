<?php
/**
 * Template Name: Flexible Content
 */

$components = get_field('components') ?: [];

get_header();

foreach ($components as $component) {
    component(str_replace('_', '-', $component['acf_fc_layout']), $component);
}

get_footer();
