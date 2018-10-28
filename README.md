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

### text-autospace.js

Uses [text-autospace.js](https://github.com/mastermay/text-autospace.js) (see my [fork](https://github.com/untunt/text-autospace.js)) to automatically add space between East Asian characters and European alphabets.

### Expression for Footnotes and References

`^[footnoted text|pn1]` produces “footnoted text*<sup>1</sup>” with anchor id `pn1` and a link to `#n1`.

`^[n1]` produces “\*1” with anchor id `n1` and a link to `#pn1`.
