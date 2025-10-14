<?php

namespace WPStarter\ACF;

use WPStarter\ACF\FieldGroup;
use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Location;

class PageFlexibleContent extends FieldGroup
{
    public static $title = 'Flexible Content';

    public static $hide_on_screen = [
        'the_content',
    ];

    public static $style = "seamless";

    public function layouts()
    {
        return [
            //
        ];
    }

    public function fields()
    {
        return [
            FlexibleContent::make(__('Components', 'esbaluard'), 'components')
                ->helpertext(__('Add components to build the page layout.', 'esbaluard'))
                ->button(__('Add component', 'esbaluard'))
                ->layouts(array_map(function ($layout) {
                    return Layout::make(__($layout['label'], 'esbaluard'), $layout['name'])
                        ->layout('block')
                        ->fields($layout['fields']);
                }, $this->layouts())),
        ];
    }

    public function location()
    {
        return [
            Location::where('page_template', 'page-templates/page-flexible-content.php'),
        ];
    }
}

new PageFlexibleContent;
