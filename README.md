# phesoca.com

Modified files (php, css, etc.) from WordPress's “Twenty Sixteen” theme for [phesoca.com](https://phesoca.com), my personal website.

## Files and Their Paths

|Filename|Path|
|-|-|
|additional.css|[“Additional CSS” in “Customize”]|
|functions.php|wp-content/themes/twentysixteen/functions.php<br>[only supplement at the end of the file]|
|header.php|wp-content/themes/twentysixteen/header.php|
|index.php|wp-content/themes/twentysixteen/index.php|

## Features

### Full-Width Font for Chinese Punctuation

Following punctuation marks are considered Chinese (East Asian) punctuation by default, and will be set to full-width:

- Apostrophes (“”‘’)
- Ellipsis (…)
- Em dash (—)
- Middot (·)

To use these punctuation marks in half-width (e.g. in English or Math), put a backslash (\\) before them. For example: `An \‘apostrophe\’ in English`.

For the middot before syllables with neutral tone (轻声) in pinyin, use “ꞏ” (`U+A78F` modifier letter middot) instead.

Chinese punctuation is set to full-width by using classes `cn` and `cn-quot`.

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

`^[footnoted text|pn1]` produces “footnoted text*<sup>1</sup>” with anchor id `pn1` and a link to `#n1`.

`^[n1]` produces “\*1” with anchor id `n1` and a link to `#pn1`.

## Formatting Guide

### Alphabetic Year Numbering

Use `&apos;` instead of `'` for years before 2011 (e.g. `0&apos;11.21` (0'11.21) meaning Nov 21, 2010), since `'` will be converted into `’`.
