# Architecture

How the theme is built, and the conventions for components, code and frontend. They apply to new work; don't rewrite existing code only to follow them.

## General

- Follow the patterns already established in the repository. When several components solve a similar problem, follow the most common pattern instead of introducing a new one.
- Prefer simple, readable implementations. Avoid unnecessary abstractions, dependencies and refactoring.
- Use WordPress-native PHP, ACF for structured and editorial content, Tailwind CSS for styling and Alpine.js for interactions. Don't introduce another framework unless asked.
- Write new code in English: names, comments and the original of translatable strings.

## Theme components

Reusable components are PHP template parts in `public/themes/wp-starter/components/`. Page templates assemble components; each component handles only its own markup and presentation. Before creating a component, look for an existing one with similar functionality: reuse or extend it instead of creating a duplicate.

The theme's `component()` helper is defined in `includes/helpers.php`. It delegates to WordPress `get_template_part()` and passes an argument array to the template:

```php
component('example', ['title' => 'Welcome']);
```

In the component template, WordPress exposes the passed values in `$args`. Every component defines enough defaults to render without database content, and merges the arguments it receives with them using `wp_parse_args()`. Escape values for their output context (`esc_html()`, `esc_attr()`, `esc_url()`).

`$args` is always an array in a component: `load_template()` takes it as a parameter that defaults to `[]`. Declare it with a `/** @var array $args */` annotation at the top, for the editor, and pass it to `wp_parse_args()` as it is, without `$args ?? []`.

- A component that is never called with arguments, such as `site-header` or `site-footer`, doesn't need `$args`: define its content in local variables.
- A subcomponent is rendered only by its parent, which has already merged the data with its defaults. It reads `$args` directly, without `wp_parse_args()` or defaults of its own, and declares its shape with a `/** @var array{…} $args */` annotation for the editor.
- The parent decides where and when a subcomponent shows. It wraps the call in an element with the visibility and margin (`<div class="mt-12 md:hidden">`), so the parent shows at a glance what renders at each breakpoint, and the subcomponent's root holds only its own layout.
- Add every new component, except subcomponents, to `/demo/components` (see [Build/demo page templates](#builddemo-page-templates)).

### Example: data-driven feature card

This component renders useful default content when called without arguments. When a Flexible Content layout supplies values, those values override the defaults. The placeholder image is replaced by the image ID or URL supplied by the CMS:

```php
<?php
/** @var array $args */
$args = wp_parse_args($args, [
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

### Arguments are editor fields

A component's arguments are the fields an editor fills in, so shape them for the editor and keep everything else in the code.

- **A section's title and anchor are `section_title` and `section_id`.** The prefix keeps them apart from the titles of the items inside the section and from WordPress's post ID.
- **Fixed content gets fixed fields.** When the design has room for an exact number of items, each one is its own argument, not a list. A list lets editors add or remove items that the layout can't fit.
- **Layout isn't an argument.** Values the editor never sets, such as which item goes in which column, are local variables after `wp_parse_args()`, not defaults. Defaults are for content that ACF can replace.
- **A field that is never used isn't there.** Don't add a key set to `null` for something that never has a value; treat it as optional.
- **Text, not structure, where editors type.** Prefer a textarea the component parses (for example, a blank line between periods, with each period's first line as its heading) over a repeater of headings and nested lines when that's easier to edit.
- **Name a field for what it holds, not for how it looks.** A price list is `rates` with a `name`, not `tiles` with a `title`: the editor fills in rates, and the tile is only how the design shows them.

### From ACF to components

The Flexible Content page template is the assembly point for ACF layouts: it reads the `components` field, converts each layout name from underscores to hyphens, and renders the matching component with the layout data as `$args`.

Components present the data they receive; they don't query the database or read ACF. When ACF values don't have the shape a component takes, convert them once, in ACF filters such as `acf/format_value/name=components`, before the page template renders the rows: not in the component or the page template. For example, fill an empty `section_id` with the slug of `section_title`, so components don't handle an empty one, or turn a Link field into the `text` and `href` a component takes.

Listings that show several post types take each post as a summary: one function returns the same keys for every post type, with `null` where a post has no value. A card or row takes a summary as its arguments, so it doesn't know which post type it shows.

An empty ACF field returns `''`, `false` or `[]`, depending on its type, never `null`. Convert it at the boundary (`?: null`, `?: []`) so templates only check for `null`.

Read fields with `get_field()` and escape the value where it's printed. `the_field()` and `the_sub_field()` print it unescaped; a WYSIWYG field goes through `wp_kses_post()`.

Don't rename a field that already has content: ACF stores values under the field name, and the content under the old name is lost.

### ACF Extended

The project uses ACF Pro for content fields and `vinkla/extended-acf` to define field groups in PHP. Add or update field groups in `public/themes/wp-starter/includes/acf/`: extend the shared `FieldGroup` base class and use Extended ACF's `Fields` and `Location` classes. These groups are loaded by `includes/acf.php`.

When a component should be available in Flexible Content, define its layout as a protected `layout<ComponentName>()` method in `PageFlexibleContent` (for example, `layoutFeatureCard()`), then add its result to the array returned by `layouts()`. Keep the layout name aligned with the component template: layout name `feature_card` maps to `components/feature-card.php`. Field names are the component's arguments (see [Arguments are editor fields](#arguments-are-editor-fields)), and labels and help texts are [translatable strings](#translatable-strings).

```php
use Extended\ACF\Fields\Text;

protected function layoutFeatureCard(): array
{
    return [
        'name' => 'feature_card',
        'label' => __('Feature card', 'wp-starter'),
        'fields' => [
            Text::make(__('Title', 'wp-starter'), 'section_title')
                ->required(),
            Text::make(__('Anchor', 'wp-starter'), 'section_id')
                ->helperText(__('Leave empty to use the title.', 'wp-starter')),
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

A field group's title is translatable too: a static property can't call `__()`, so override `title()` instead of setting `$title`.

```php
public function title(): string
{
    return __('Page', 'wp-starter');
}
```

### Shared theme helpers

Use the helpers in `includes/helpers.php` instead of duplicating component-loading or asset logic:

- `component()` loads a component template and passes its arguments.
- `image()` renders an attachment ID or an image URL. Configure ACF image fields to return an ID or URL; the helper doesn't accept ACF's array return format.
- `svg()` renders an SVG from `assets/svg/`. See [Icons](#icons).
- `asset()` returns a URL under the theme's `assets/` directory.
- `markdown()` converts Markdown to HTML, filters it with `wp_kses_post()` and runs shortcodes. Wrap its output in `prose` to style it. Use it for Markdown content instead of creating a converter.

### Icons

The SVGs are in `assets/svg/`, and `svg()` renders them.

- **Size at the call.** An SVG has a `viewBox` and no `width` or `height`, logos included: every call sets the size with a class, both sides (`size-6`) or one with the other on auto (`h-6 w-auto`).
- **Prefer a 24 × 24 `viewBox`**, with the drawing about 4 units from each edge, so icons at the same size look the same size. A drawing that isn't square, or that has to fill its box, is the exception.
- **A consistent stroke**, so icons drawn with a line look alike at the same size. A drawing or a call that needs another thickness is the exception.
- **Named for what they draw**, not for where they're used: `x-mark`, not `close`.

### Translatable strings

Text the theme writes itself (labels, `aria-label` values, screen-reader text) and admin labels (post type names, ACF field labels and help texts) go through WordPress's translation functions (`__()`, `esc_html__()`, `esc_attr__()`, …) with the theme's text domain. Write the original string in English; on a multilingual site, every language version is a translation.

```php
<?= esc_attr__('Close menu', 'wp-starter') ?>
```

- Pass a literal string, not a variable: string extraction only finds literals.
- For a string with placeholders, use `sprintf()` and add a `translators:` comment on the line before, explaining each placeholder.
- After you add or change a string, update the translation files from the theme directory with [WP-CLI](https://wp-cli.org): regenerate the template, merge it into each translation, translate the new entries (those with an empty `msgstr`), and compile the `.mo` files, which are what WordPress reads.

  ```sh
  wp i18n make-pot . lang/wp-starter.pot --exclude=build,node_modules
  wp i18n update-po lang/wp-starter.pot lang/
  wp i18n make-mo lang/ lang/
  ```

Content that editors fill in, such as component defaults that ACF replaces, isn't wrapped: it's translated as content.

## Build/demo page templates

Use temporary demo templates to preview pages during implementation. Create them in the theme's `page-templates/` directory with the `demo-<slug>.php` naming pattern, then add a matching route to the `template_include` filter in `public/themes/wp-starter/functions.php`.

For example, `/demo/exhibition` maps to `page-templates/demo-exhibition.php`:

```php
'demo/exhibition' => locate_template('page-templates/demo-exhibition.php'),
```

Follow the existing `/demo/components` → `demo-components.php` mapping. Preserve the filter's environment and authentication guard so demo templates are not exposed to logged-out visitors in production.

`/demo/components` renders every page component with its default arguments. Add a component to it when you create one.

## Code conventions

Code should read on its own, without comments to explain it.

- **Names say what a value is.** Name variables and arguments for the content they hold (`$rate`, `$conditions`), not for their markup or a vague role (`$tile`, `$data`, `$has_modal`).
- **No variable without a reason.** Don't store a value used once, such as a translated label: write it where it's used. A variable is worth it when it removes repetition or names a step the reader would otherwise have to work out.
- **Doc comments are short.** A component's doc comment names the section in a line and adds only what the code doesn't show, such as which parent fills a subcomponent's Alpine bindings. Don't describe classes, breakpoints or arguments: the markup, the defaults and the `@var` annotation already show them. A comment is whole sentences, each idea on its own line and starting with a capital letter, not lowercase fragments.
- **Long tags and calls go one item per line.** A tag with many attributes puts each on its own line, with the closing `>` on its own line too: from four attributes always, from three when it reads better. A long `class` alone stays on one line. A `component()` call with three or more arguments puts each on its own line.
- **Indentation follows `.editorconfig`:** 4 spaces for PHP, 2 for JavaScript, CSS and the rest. In a file with another style, keep the file's style in the lines you change, and don't reindent whole files.
- **The code runs on PHP 8.4:** no dynamic properties, no `null` passed to string functions, and check that a value exists before reading its properties.

## Frontend conventions

- Tailwind CSS v4 for styles, with the CSS and theme tokens in `resources/css/app.css`, and Alpine.js for interactions, from `resources/js/app.js`.
- Don't edit compiled files under the theme's `build/`; update the source and rebuild with `npm run build`.
- Use Tailwind's standard scales and the theme tokens. Avoid arbitrary values in every utility, not only spacing. Don't extract CSS or create custom CSS classes unless the task requires them.
- Keep Tailwind's default scales until a pattern is consolidated; add a custom token only when it repeats, not at the first need. Name tokens for what they are (`gold`, `gray-300`), never for their use (`gray-bg`, `gray-text`).
- Use Tailwind's default breakpoints (`sm` to `2xl`). A design's intermediate frames, such as 1440, take the nearest one rather than a breakpoint of their own.
- Reuse the interaction patterns of similar components, such as card hovers, instead of creating a one-off treatment.
- Tailwind only generates classes it finds written in full in the source. When a value chooses a class, such as a section's `background`, map it to full class names with `match`, not by building the name (`"bg-$color"`):

  ```php
  $background = match ($args['background']) {
      'dark' => 'bg-gray-200',
      'darker' => 'bg-gray-300',
      default => 'bg-gray-100',
  };
  ```
- For a maximum width, use a named container size (`max-w-md`, `max-w-lg`, `max-w-xl`, …) close to the design value, not an arbitrary value such as `max-w-135.5`, which comes from one screen size. A short text in a component, such as the intro under a section title, can take a `ch` width instead, which scales with the font size (`max-w-[45ch]`). Check how many characters a line it gives in the project's font: `max-w-prose` (65ch) can give too many.

### Content-sized elements

- Don't set a fixed width or height on an element whose size depends on its content, such as a button, tag or label: long or translated text breaks it. Reproduce the design's size with padding, gaps or `justify-between`.
- Size columns and cards with fractions of their row (`w-1/3`, `flex-1`), not fixed widths.
- Fixed sizes are fine for elements without variable content, such as square icon buttons.
- Keep markup flat: don't add wrapper elements only for spacing, unless they make the code more readable.

### Responsive by default

Build every component to work on small screens, even when the task covers only the desktop design. Use reasonable smaller values for the base classes and apply the desktop values from a breakpoint (`text-xl sm:text-2xl px-18 sm:px-32`). Without a mobile design, scale down font sizes, padding, gaps, offsets and icon sizes. These are first guesses that later responsive work will refine, but the component shouldn't break on small screens in the meantime.

Scale sizes to the element's available space: when a grid gains columns, sizes can step down at that breakpoint, as in `text-lg sm:text-xl md:text-lg lg:text-xl` for cards that go three across at `md`.

### Typography

Use Tailwind's `font-*`, `text-*`, `leading-*`, and `tracking-*` utilities for typography on individual elements.

Each `text-*` size carries Tailwind's default line height. Add a `leading-*` class only when the design needs a different one, and then use Tailwind's standard scale (`leading-tight`, `leading-snug`, …) rather than a custom utility.

The `@tailwindcss/typography` plugin provides `prose` styles for long-form or CMS-generated HTML. Its setup is in `resources/css/typography.css`; configure its generic defaults to match the project's fonts, colors, and type scale, using project tokens where possible. Load that stylesheet from `resources/css/app.css` before using `prose` classes. Prefer ordinary utilities for component-authored markup.

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

Use Alpine for simple interactions. Add a package only when a feature needs capabilities that would be costly or unreliable to build with the existing stack, and scope it to that feature.

- **Carousels and sliders:** prefer Swiper for touch/swipe navigation, pagination or responsive slides instead of building gesture logic. Import its JavaScript and required CSS from the component's module.
- **Expanding regions:** to animate a region to its content height, prefer the CSS grid technique (`grid-rows-[0fr]` ↔ `grid-rows-[1fr]` with an `overflow-hidden` child). Use `@alpinejs/collapse` (`x-collapse`) only if that isn't enough, and register it in `resources/js/app.js` before `Alpine.start()`:

  ```js
  import collapse from '@alpinejs/collapse'

  Alpine.plugin(collapse)
  ```

Preserve keyboard and reduced-motion accessibility in every interaction.
