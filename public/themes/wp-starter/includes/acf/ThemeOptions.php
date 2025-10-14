<?php

namespace WPStarter\ACF;

use Extended\ACF\Location;

class ThemeOptions extends FieldGroup
{
    public static $title = 'Theme Options';

    public static $style = 'seamless';

    public function __construct()
    {
        parent::__construct();

        acf_add_options_page([
            'icon_url' => svg_to_base64('adjustments-vertical'),
            'menu_slug' => 'theme_options',
            'page_title' => static::$title,
            'redirect' => false,
        ]);
    }

    public function fields()
    {
        return [
            //
        ];
    }

    public function location()
    {
        return [
            Location::where('options_page', 'theme_options'),
        ];
    }
}

new ThemeOptions;
