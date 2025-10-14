<?php

if (!function_exists('svg')) {
    /**
     * Outputs an inline SVG with optional attributes.
     *
     * @param string $name The name of the SVG file (without .svg extension).
     * @param string|array $class Optional. The CSS class or array of attributes.
     * @param array $attrs Optional. Additional attributes for the SVG tag.
     */
    function svg($name, $class = '', $attrs = [])
    {
        if (is_array($class)) {
            $attrs = $class;
            $class = '';
        }

        $attrs = array_merge([
            'class' => $class,
        ], $attrs);

        array_walk($attrs, function (&$value, $attr) {
            $value = sprintf('%s="%s"', $attr, $value);
        });

        $attrs = implode(' ', $attrs);

        $theme_path = get_stylesheet_directory();

        $path = "$theme_path/assets/svg/$name.svg";

        if (!file_exists($path)) {
            return;
        }

        $svg = file_get_contents($path);

        return str_replace(
            '<svg',
            sprintf('<svg %s', $attrs),
            $svg
        );
    }
}

if (! function_exists('widont')) {
    /**
     * Prevents typographic widows by replacing the last space between words with a non-breaking space.
     *
     * @param string $string The input string.
     * @param int $value Recursive depth for widow removal.
     * @return string The processed string with widows prevented.
     *
     * @see https://shauninman.com/archive/2007/01/03/widont_2_1_wordpress_plugin
     */
    function widont($string, $value = 2)
    {
        if ($value > 2) {
            $string = widont($string, $value - 1);
        }

        return preg_replace('/([^\s])\s+([^\s]+)\s*$/', '$1&nbsp;$2', rtrim($string));
    }
}

if (! function_exists('asset')) {
    /**
     * Generates a URL for an asset in the theme's assets directory.
     *
     * @param string $path The relative path to the asset within the assets directory.
     * @return string The full URL to the asset.
     */
    function asset(string $path)
    {
        return get_template_directory_uri().'/assets/'.$path;
    }
}

if (!function_exists('component')) {
    /**
     * Renders a specific component template part with optional arguments.
     *
     * @param string $name The name of the component template file (without extension).
     * @param string|array $args Optional associative array of variables to pass to the template.
     * @return mixed The return value of get_template_part(), typically void, but could be manipulated by filters.
     */
    function component(string $name, string|array $args = []) {
        return get_template_part('components/' . $name, null, (array) $args);
    }
}

if (! function_exists('image')) {
    function image($image = null, $attributes = [], $size = 'large')
    {
        if (is_string($attributes)) {
            $attributes = ['class' => $attributes];
        }

        if (is_int($image)) {
            return wp_get_attachment_image($image, $size, false, $attributes);
        }

        if (is_string($image)) {
            return sprintf(
                '<img src="%1$s" %2$s>',
                $image,
                implode(' ', array_map(function($key, $value) {
                    return sprintf('%s="%s"', $key, esc_attr($value));
                }, array_keys($attributes), $attributes))
            );
        }

        return '';
    }
}

if (!function_exists('markdown')) {
    function markdown($markdown) {
        $converter = new League\CommonMark\CommonMarkConverter();

        $html = wp_kses_post($converter->convert($markdown)->getContent());

        return do_shortcode($html);
    }
}

if (!function_exists('ujoin')) {
    /**
     * Join an array into a readable string with commas and "and".
     *
     * @param array $items List of strings to join.
     * @return string The formatted string.
     */
    function ujoin(array $items, string $glue, string $finalGlue = ''): string
    {
        if ($finalGlue === '') {
            return implode($glue, $items);
        }

        $count = count($items);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $items[0];
        }

        return implode(', ', array_slice($items, 0, -1)) . $finalGlue . end($items);
    }
}

if (! function_exists('svg_to_base64')) {
    function svg_to_base64($name) {
        $path = get_stylesheet_directory() . "/assets/svg/$name.svg";

        if (!file_exists($path)) {
            return;
        }

        return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($path));
    }
}

if (! function_exists('eager_load_post_thumbnails')) {
    /**
     * Eager load post thumbnails
     *
     * @see https://wordpress.stackexchange.com/questions/266557/custom-sql-query-to-get-list-of-posts-with-featured-image-url
     */
    function eager_load_post_thumbnails($posts = null)
    {
        if (!$posts) {
            $posts = $GLOBALS['posts'];
        }

        $thumb_ids = array_unique(array_map(function ($post) {
            return get_post_thumbnail_id($post);
        }, $posts));

        _prime_post_caches($thumb_ids, false, true);
    }
}

if (! function_exists('get_menu_items_by_location')) {
    function get_menu_items_by_location($location)
    {
        $locations = get_nav_menu_locations();

        if (!isset($locations[$location])) {
            return null;
        }

        $menu = wp_get_nav_menu_object($locations[$location]);

        return wp_get_nav_menu_items($menu->term_id, [
            'update_post_term_cache' => false
        ]);
    }
}

if (! function_exists('build_tree')) {
    function build_tree(array $items) {
        $grouped = [];
        $current_id = get_queried_object_id();

        // Group items by parent
        foreach ($items as $item){
            $grouped[$item->menu_item_parent][] = $item;
        }

        // Recursive builder to construct the menu tree
        $builder = function($siblings) use (&$builder, $grouped, $current_id) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling->ID;

                // Mark current item
                if ($sibling->object_id == $current_id) {
                    $sibling->current = true;
                }

                // Check for children and recurse
                if(isset($grouped[$id])) {
                    $sibling->children = $builder($grouped[$id]);
                }
                $siblings[$k] = $sibling;
            }

            return $siblings;
        };

        if (!isset($grouped[0])) {
            return [];
        }

        // Start the tree building from the root
        return $builder($grouped[0]);
    }
}
