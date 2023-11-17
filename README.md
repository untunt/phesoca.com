# phesoca.com

Modified files (php, css, etc.) from WordPress's “Twenty Sixteen” theme for [phesoca.com](https://phesoca.com), my personal website.

## Files and Their Paths

|Filename|Path|
|-|-|
|additional.css|[“Additional CSS” in “Customize”]|
|functions.php|wp-content/themes/twentysixteen/functions.php<br>[only supplement at the end of the file]|

## Features

### Full-Width Font for Chinese Punctuation

Following punctuation marks are considered Chinese (East Asian) punctuation by default, and will be set to full-width:

- Apostrophes (“”‘’)
- Ellipsis (…)
- Em dash (—)
- Middot (·)

To use these punctuation marks in half-width (e.g. in English or Math), put a backslash (\\) before them. For example: `An \‘apostrophe\’ in English`.

For the middot before syllables with neutral tone (轻声) in pinyin, use “ꞏ” (`U+A78F` modifier letter middot) instead.

If the interior character next to an apostrophe is a less- or greater-than sign (`“<` or `>”`), the `class="non-breaking"` tag will not be added. Please manually add under this circumstance.

Chinese punctuation is set to full-width by using classes `cn`, `cn-quot`, and `full-width-char`.

### Table with Horizontal Scrolling

Class `hori-scroll` adds horizontal scrolling to its content, which is often a wide table. Usage:

```html
<div class="hori-scroll">
	<table>...</table>
</div>
```

### text-autospace.js

Uses [text-autospace.js](https://github.com/mastermay/text-autospace.js) (see my [fork](https://github.com/untunt/text-autospace.js)) to automatically insert spacing between East Asian characters and European alphabets.

To prevent spacing, add `<span hidden> </span>` between East Asian characters and European alphabets.

### Expression for Footnotes and References

Format for number in the body: `^[noted text|anchor|note text|link]`. The 2<sup>nd</sup> argument must start with either flags: `pn` for footnote, or `pr` for reference. The 1<sup>st</sup>, 3<sup>rd</sup>, and 4<sup>th</sup> arguments can be omitted. For example:

- `^[pn1]` and `^[|pn1]` both produces “footnoted text\*<sup>1</sup>” with anchor id `pn1` and a link to `#n1`.
- `^[footnoted text|pn1|note text|link]` produces “footnoted text\*<sup>note text</sup>” with anchor id `pn2` and a link to `#link`.

Format for number at the end: `^[anchor|note text|link]`. The 1<sup>st</sup> argument must start with either flags: `n` for footnote, or `r` for reference. The 2<sup>nd</sup> and 3<sup>rd</sup> arguments can be omitted. For example:

- `^[n1]` produces “\*1” with anchor id `n1` and a link to `#pn1`.
- `^[n2|note text|link]` produces “\*note text” with anchor id `n2` and a link to `#link`.

### Tooltip

Visible on hover. Usage:

```html
<span class="hint" data-tooltip="hUNTun">馄饨</span>
```

### 行内夹注 Inline Note

- `inline-note`: Main class
- `phono-term`: For Historical Chinese phonology terms like those in 麻<sub>二</sub> and 脂<sub>合</sub>, but not for notes like 去声 or 文读
- `er`: For *er* (儿) suffix
- `weakened`: For unt-defined weakened form (inspired from writing *er* suffix as inline note)

When an article/passage uses unt-defined inline note for weakened form, both `weakened` and `er` should be given to *er* suffixes.

## Formatting Guide

### Alphabetic Year Numbering

Use `&apos;` instead of `'` for years before 2011 (e.g. `0&apos;11.21` (0'11.21) meaning Nov 21, 2010), since `'` will be converted into `’`.

## PHP Modification

### Cache `the_content` to save loading time

```PHP
// apply_filters()@wp-includes/plugin.php:205
// From
$filtered = $wp_filter[ $hook_name ]->apply_filters( $value, $args );
// To
static $filtered_content = '';
if ( $hook_name == 'the_content' && $filtered_content && is_singular() && get_the_ID() != 99 ) {
	$filtered = $filtered_content;
} else {
	$filtered = $wp_filter[ $hook_name ]->apply_filters( $value, $args );
	if ( $hook_name == 'the_content' ) {
		$filtered_content = $filtered;
	}
}

// wp_trim_excerpt()@wp-includes/formatting.php:3974
// Make sure the content passed into `apply_filters()` in `get_the_excerpt()` is the same as that in `the_content()`
// From
$text = strip_shortcodes( $text );
$text = excerpt_remove_blocks( $text );
$text = excerpt_remove_footnotes( $text );
$filter_image_removed = remove_filter( 'the_content', 'wp_filter_content_tags', 12 );
$filter_block_removed = remove_filter( 'the_content', 'do_blocks', 9 );
// To
if ( !is_singular() ) {
	$text = strip_shortcodes( $text );
	$text = excerpt_remove_blocks( $text );
	$text = excerpt_remove_footnotes( $text );
}
$filter_image_removed = !is_singular() && remove_filter( 'the_content', 'wp_filter_content_tags', 12 );
$filter_block_removed = !is_singular() && remove_filter( 'the_content', 'do_blocks', 9 );
```

### Strip text hidden on homepage for `get_the_excerpt()`

```PHP
// wp-includes/shortcodes.php:715:strip_shortcodes()
// Insert after `apply_filters()`
$text = preg_replace( '/<([^ >]+) [^>]*? class="hide-on-homepage">.*?\/\1>/s', '', $text );
```

### Optimize SEO time consumption

```PHP
// replace()@wp-content/plugins/wordpress-seo/inc/class-wpseo-replace-vars.php:149
// Comment out (these seem to have no effects)
if ( isset( $args['post_content'] ) && ! empty( $args['post_content'] ) ) {
	$args['post_content'] = YoastSEO()->helpers->string->strip_shortcode( $args['post_content'] );
}
if ( isset( $args['post_excerpt'] ) && ! empty( $args['post_excerpt'] ) ) {
	$args['post_excerpt'] = YoastSEO()->helpers->string->strip_shortcode( $args['post_excerpt'] );
}
```
