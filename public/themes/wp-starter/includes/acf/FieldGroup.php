<?php

namespace WPStarter\ACF;

abstract class FieldGroup
{
    public static $title;

    public static $style = 'normal';

    public static $hide_on_screen = [];

    public static $key;

    public static $position = 'normal';

    public static $show_in_rest = false;

    public function __construct()
    {
        add_action('acf/init', [$this, 'register']);
    }

    public function register()
    {
        register_extended_field_group($this->settings());
    }

    public function settings()
    {
        return [
            'title' => static::$title,
            'fields' => $this->fields(),
            'location' => $this->location(),
            'hide_on_screen' => static::$hide_on_screen,
            'style' => static::$style,
            'key' => static::$key,
            'position' => static::$position,
            'show_in_rest' => static::$show_in_rest,
        ];
    }

    abstract public function fields();

    abstract public function location();
}
