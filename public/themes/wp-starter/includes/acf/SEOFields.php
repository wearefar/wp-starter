<?php

namespace WPStarter\ACF;

use Extended\ACF\Fields\Text;
use Extended\ACF\Location;

class SEOFields extends FieldGroup
{
    public static $title = 'SEO';

    public static $style = 'seamless';

    public function fields()
    {
        return [
            Text::make('Title tag')
                ->helpertext('See <a href="https://moz.com/learn/seo/title-tag">https://moz.com/learn/seo/title-tag</a> for best practices.'),
            Text::make('Meta description')
                ->helpertext('See <a href="https://moz.com/learn/seo/meta-description">https://moz.com/learn/seo/meta-description</a> for best practices writting descriptions.'),
        ];
    }

    public function location()
    {
        return [
            Location::where('post_type', 'page'),
            Location::where('post_type', 'post'),
        ];
    }
}

new SEOFields;
