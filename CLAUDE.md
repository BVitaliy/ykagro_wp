# Project Rules

## Project context
- WordPress **custom theme** (`wp-content/themes/ykagro`), no page-builder plugins
- YKagro — agribusiness site: poultry, feed production, logistics
- Built by integrating a static PHP markup project into WordPress
- Markup source of truth: `/Applications/MAMP/htdocs/ykagro/app/` — when a template
  looks wrong, diff it against the original markup before "fixing" the CSS

## Project structure
```
functions.php     — constants + get_template_part() bootstrap only
functions/        — PHP feature classes and helpers
includes/         — reusable partials, ACF bootstrap, page-builder renderer
includes/acf/     — ACF bootstrap + helpers
acf-json/         — ACF field groups (source of truth — see .claude/acf-flexible-content.md)
templates/        — page templates
template-parts/   — page-level section partials
js/               — custom JavaScript (js/vendors/ = third-party, do not edit)
css/              — compiled CSS (do not edit; generated from scss/ by `gulp styles`)
scss/             — SCSS source
img/              — committed optimized images (output of `gulp img`)
images/           — local source images for `gulp img` (gitignored)
```

## Build
`gulp` in the theme dir runs styles + img + BrowserSync (proxies the local WP site)
and watches. **Never edit `css/*.css` directly** — it is overwritten by `gulp styles`.
`js/vendors/` is refreshed by `gulp updateJS` — never hand-edit those files.

## Prefixes and text domain
- Text domain: `ykagro`
- PHP functions/globals: `yka_`
- PHP classes: `YKA_` (e.g. `YKA_Theme`, `YKA_Nav_Walker`)
- Constants: `YKA_UPPER_SNAKE`
- CSS classes: come from the markup — **do not rename them**

## Language
- All code, comments, variable names, and commit messages in **English**
- User-facing strings are written in **Ukrainian** — the site and its admin are
  Ukrainian, and an English source string would only add a translation layer
  nobody maintains. Always wrap them so they stay translatable:
  `__( 'Детальніше', 'ykagro' )`, `esc_html_e( 'Меню', 'ykagro' )`
- This includes post type / taxonomy labels and admin-facing text

## Escaping ACF output — no exceptions
`get_field()` / `get_sub_field()` return unescaped data. Escape at output:
`esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()` for wysiwyg.

## PHP — checking variables before use
Prefer **`! empty( $var )`** when you only need to know "has a usable value".
**`empty()` already implies an `isset`-safe check** — do not combine redundant
`isset()` / `is_string( $x ) && $x !== ''` patterns for the same purpose.

```php
// ✅
if ( ! empty( $grid_heading ) ) {
    echo wp_kses_post( $grid_heading );
}

// ❌
if ( is_string( $grid_heading ) && $grid_heading !== '' ) {
    echo wp_kses_post( $grid_heading );
}
```

Use stricter checks only when the type truly matters (e.g. explicit `=== false` for API flags).

## PHP — no alternative control structure syntax
Always use curly braces `{}`. Never use `:` / `endif` / `endforeach` / `endwhile` / `endfor`.

```php
// ✅
if ( $condition ) {
    echo 'yes';
}

// ❌
if ( $condition ) :
    echo 'yes';
endif;
```

## ACF repeaters — prefer `foreach` for display
When only outputting repeater rows, load with **`get_field()`** and iterate with **`foreach`**.
Use **`have_rows()` / `the_row()` / `get_sub_field()`** for nested repeaters or flexible content.

```php
// ✅
$items = get_field( 'footer_social', 'options' );
if ( is_array( $items ) ) {
    foreach ( $items as $item ) {
        if ( empty( $item['link'] ) || empty( $item['icon']['url'] ) ) {
            continue;
        }
        // ...
    }
}
```

## No debug code in commits
Remove all `var_dump()`, `console.log()`, `error_log()` before committing.

---

## Additional rules
- [PHP Standards](.claude/php-standards.md)
- [JavaScript Standards](.claude/js-standards.md)
- [Security](.claude/security.md)
- [ACF Flexible Content](.claude/acf-flexible-content.md)
