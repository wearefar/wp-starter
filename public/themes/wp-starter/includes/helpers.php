<?php

if (!function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @throws \Exception
     *
     * @return string
     */
    function mix(string $path, string $manifestDirectory = 'assets'): string
    {
        static $manifest;

        if (strpos($path, '/') !== 0) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && strpos($manifestDirectory, '/') !== 0) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (!$manifest) {
            if (!file_exists($manifestPath = get_theme_file_path($manifestDirectory.'/mix-manifest.json'))) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (!array_key_exists($path, $manifest)) {
            throw new Exception("Unable to locate Mix file: {$path}. Please check your webpack.mix.js output paths and try again.");
        }

        return get_theme_file_uri($manifestDirectory.$manifest[$path]);
    }
}

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

if (! function_exists('asset')) {
    function asset(string $path)
    {
        return get_template_directory_uri().'/assets/'.$path;
    }
}
