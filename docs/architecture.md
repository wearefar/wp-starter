# Architecture

## Theme components

Reusable components are PHP template parts in `public/themes/wp-starter/components/`. Keep page templates responsible for assembling components; keep each component focused on its own markup and presentation.

The theme's `component()` helper is defined in `includes/helpers.php`. It delegates to WordPress `get_template_part()` and passes an argument array to the template:

```php
component('example', ['title' => 'Welcome']);
```

In the component template, WordPress exposes the passed values in `$args`. Every component should define enough defaults to render without database content, then merge caller-provided values with `wp_parse_args()`. Escape values for their output context.

### Example: data-driven feature card

This component renders useful default content when called without arguments. When a Flexible Content layout supplies values, those values override the defaults. The placeholder image is replaced by the image ID or URL supplied by the CMS:

```php
<?php
$args = wp_parse_args($args ?? [], [
    'title' => 'A welcoming place to stay',
    'description' => 'Thoughtful details make every visit memorable.',
    'image' => 'https://placehold.co/1200x800',
]);
?>

<div>
    <?= image($args['image'], [
        'class' => 'aspect-video w-full object-cover',
    ]) ?>
    <h2><?= esc_html($args['title']) ?></h2>
    <p><?= esc_html($args['description']) ?></p>
</div>
```

The `image()` helper accepts an attachment ID or URL. For an attachment ID, WordPress renders the image; for a URL, the helper renders an `<img>` element. Configure ACF image fields to return an ID or URL, and pass that value as `image`; the helper does not accept ACF's array return format.

The Flexible Content page template is the assembly point for ACF layouts: it reads the `components` field, converts each layout name from underscores to hyphens, and renders the matching component with the layout data as `$args`. Keep ACF/data-source handling at this boundary where possible; components should present the data they receive rather than perform page-level queries.

### ACF Extended

The project uses ACF Pro for content fields and `vinkla/extended-acf` to define field groups in PHP. Add or update field groups in `public/themes/wp-starter/includes/acf/`: extend the shared `FieldGroup` base class and use Extended ACF's `Fields` and `Location` classes. These groups are loaded by `includes/acf.php`.

When a component should be available in Flexible Content, define its layout as a protected `layout<ComponentName>()` method in `PageFlexibleContent` (for example, `layoutFeatureCard()`), then add its result to the array returned by `layouts()`. Keep the layout name aligned with the component template: the page template converts underscores to hyphens, so layout name `feature_card` maps to `components/feature-card.php`.

```php
use Extended\ACF\Fields\Text;

protected function layoutFeatureCard(): array
{
    return [
        'name' => 'feature_card',
        'label' => 'Feature card',
        'fields' => [
            Text::make('Title', 'title'),
        ],
    ];
}

public function layouts(): array
{
    return [
        $this->layoutFeatureCard(),
    ];
}
```

### Shared theme helpers

Use the existing helpers in `includes/helpers.php` where appropriate:

- `component()` loads a component template and passes its arguments.
- `image()` renders an attachment or image URL.
- `svg()` renders an SVG from `assets/svg/`.
- `asset()` returns a URL under the theme's `assets/` directory.

Prefer these established paths and helpers over creating duplicate component-loading or asset logic.

## Build/demo page templates

Use temporary demo templates to preview pages during implementation. Create them in the theme's `page-templates/` directory with the `demo-<slug>.php` naming pattern, then add a matching route to the `template_include` filter in `public/themes/wp-starter/functions.php`.

For example, `/demo/exhibition` maps to `page-templates/demo-exhibition.php`:

```php
'demo/exhibition' => locate_template('page-templates/demo-exhibition.php'),
```

Follow the existing `/demo/components` → `demo-components.php` mapping. Preserve the filter's environment and authentication guard so demo templates are not exposed to logged-out visitors in production.

## Frontend conventions

- Use Tailwind CSS v4 utilities and Alpine.js for interactions, following existing patterns.
- Put CSS and theme tokens in `resources/css/app.css`; put JavaScript in `resources/js/app.js`.
- Prefer Tailwind's standard scales and existing theme tokens over arbitrary values. Do not add custom CSS classes unless the task requires them.
- Do not edit compiled files under the theme's `build/`; update source files and rebuild with `npm run build`.

### Typography

Use Tailwind's `font-*`, `text-*`, `leading-*`, and `tracking-*` utilities for typography on individual elements. The `@tailwindcss/typography` plugin provides `prose` styles for long-form or CMS-generated HTML. Its setup is in `resources/css/typography.css`; configure its generic defaults to match the project's fonts, colors, and type scale, using project tokens where possible. Load that stylesheet from `resources/css/app.css` before using `prose` classes. Prefer ordinary utilities for component-authored markup.

### Alpine component logic

Keep short interactions inline in `x-data`. When a component needs substantial JavaScript, put its data factory in a separate module under `resources/js/components/`, import it in `resources/js/app.js`, and register it with `Alpine.data()` before `Alpine.start()`.

```js
// resources/js/components/dropdown.js
export default () => ({
    open: false,

    toggle() {
        this.open = !this.open
    },
})
```

```js
// resources/js/app.js
import Alpine from 'alpinejs'
import dropdown from './components/dropdown'

window.Alpine = Alpine
Alpine.data('dropdown', dropdown)
Alpine.start()
```

Use the registered name in the template:

```html
<section x-data="dropdown">
    <button type="button" @click="toggle">Toggle</button>
</section>
```

### Optional JavaScript packages

Use Alpine for simple component interactions. Add an optional package only when a feature needs capabilities that would be costly or unreliable to implement with the existing stack. Keep this list focused on packages with clear use cases; extend it as project needs arise.

- **Carousels and sliders:** Prefer Swiper for touch/swipe navigation, pagination, or responsive slide behavior instead of building those features from scratch. It is optional; add it only when a component needs it and import its JavaScript and required CSS from that component's module.
- **Alpine transitions:** `@alpinejs/collapse` is a frequently useful optional Alpine plugin for animating expandable regions with `x-collapse`. Add it when needed and register it in `resources/js/app.js` before `Alpine.start()`:

  ```js
  import collapse from '@alpinejs/collapse'

  Alpine.plugin(collapse)
  ```

Keep package usage scoped to the feature and preserve keyboard and reduced-motion accessibility.
