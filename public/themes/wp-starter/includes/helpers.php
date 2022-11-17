<?php

if (!function_exists('svg')) {
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

        $path = "$theme_path/svg/$name.svg";

        if (!file_exists($path)) {
            return;
        }

        $svg = file_get_contents($path);

        echo str_replace(
            '<svg',
            sprintf('<svg %s', $attrs),
            $svg
        );
    }
}

if (! function_exists('widont')) {
    /**
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
    function asset(string $path)
    {
        return get_template_directory_uri().'/assets/'.$path;
    }
}
