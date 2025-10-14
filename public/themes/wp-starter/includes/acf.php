<?php

require __DIR__.'/acf/FieldGroup.php';
require __DIR__.'/acf/FlexibleContent.php';

require __DIR__.'/acf/ThemeOptions.php';
require __DIR__.'/acf/SEOFields.php';

// Disable Options Pages Translations
add_filter('acf/settings/current_language', '__return_false');
