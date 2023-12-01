<?php
/**
 * https://codex.wordpress.org/Customizing_the_Read_More#Prevent_Page_Scroll_When_Clicking_the_More_Link
 */
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );

/**
 * flags
 *   n: note
 *   r: reference
 *   b: note or reference in the body (having anchor with 'p')
 *      input format: ^[text|anchor|note text|link|suffix]
 *   e: note or reference at the end (having anchor without 'p')
 *      input format: ^[anchor|note text|link|suffix]
 *   w: without <a> tag
 */
function convert_note_sub( $content, $flag ) {
	$out_head = '<span class="pnote"><span id="pn$2">';
	$out_mid0 =     '<span class="annotated">$1</span>';
	$out_mid1 =     '<a href="#$5" class="note">';
	$out_mid2 =         '*<sup>$3</sup>';
	$out_mid3 =     '</a>';
	$out_mid4 =     '<sup class="note-suffix">$4</sup>';
	$out_tail = '</span></span><span hidden> </span>';

	$arg = '([^\[\|\]]*)';   // argument: any text expect `[`, `|`, `]`
	$sep = '(?:\||\&#124;)'; // separator: `|` = `&#124;`
	$note_flag = 'n';        // `n` or `r`
	$no_link = '_no_link';   // flag in `anchor` to remove <a> tag
	if ( strpos( $flag, 'r' ) !== false ) {
		$out_head = str_replace( 'n$', 'r$', $out_head );
		$out_mid2 = '<sup>[$3]</sup>';
		$note_flag = 'r';
	}
	$regex_head = "@\^\[(?:$arg$sep)";
	$regex_head_ph = '@\^\[(foo){0}'; // placeholder
	$regex_mid0 = 'p';
	$regex_mid1 = $note_flag . $arg;
	$regex_mid2 = $sep . $arg;
	$regex_tail = '\]@i';

	if ( strpos( $flag, 'e' ) !== false ) {
		$out_head = str_replace( '"p', '"', $out_head );
		$out_head = str_replace( 'note', 'note-block', $out_head );
		$out_mid0 = '';
		$out_mid1 = str_replace( '#', '#p', $out_mid1 );
		$out_mid2 = str_replace( '*<sup>', '<sup>*', $out_mid2 );
		$out_mid2 = str_replace( 'sup', 'span', $out_mid2 );
		$out_mid4 = str_replace( 'sup', 'span', $out_mid4 );
		$regex_head = $regex_head_ph;
		$regex_mid0 = '';
		$regex_mid2 = $sep . $arg;
		$regex_tail = '\] *@i';
		if ( strpos( $flag, 'w' ) !== false ) {
			$out_mid1 = '<span class="note">';
			$out_mid3 = '</span>';
			$regex_mid1 = $note_flag . $arg . $no_link;
		}
	}

	$regex = array(
		5 => $regex_head . $regex_mid0 . $regex_mid1 . $regex_mid2 . $regex_mid2 . $regex_mid2 . $regex_tail,
		4 => $regex_head . $regex_mid0 . $regex_mid1 . $regex_mid2 . $regex_mid2 . $regex_tail,
		3 => $regex_head . $regex_mid0 . $regex_mid1 . $regex_mid2 . $regex_tail,
		2 => $regex_head . $regex_mid0 . $regex_mid1 . $regex_tail,
		1 => $regex_head_ph . $regex_mid0 . $regex_mid1 . $regex_tail,
	);

	$out = array();
	$out[5] = $out_head . $out_mid0 . $out_mid1 . $out_mid2 . $out_mid3 . $out_mid4 . $out_tail;
	if ( strpos( $flag, 'e' ) !== false ) {
		// move the suffix ($out_mid4) into <a> tag ($out_mid3)
		$out[5] = $out_head . $out_mid0 . $out_mid1 . $out_mid2 . $out_mid4 . $out_mid3 . $out_tail;
	}
	$out[4] = str_replace( '$5', $note_flag . '$2', $out[5] );
	$out[3] = str_replace( $out_mid4, '', $out[4] );
	$out[2] = str_replace( '$3', '$2', $out[3] );
	$out[1] = str_replace( $out_mid0, '', $out[2] );

	for ( $i = 5; $i > 0; $i-- ) {
		$content = preg_replace( $regex[$i], $out[$i], $content );
	}
	return $content;
}

function convert_note( $content ) {
	$content = convert_note_sub( $content, 'nb' );
	$content = convert_note_sub( $content, 'rb' );
	$content = convert_note_sub( $content, 'new' );
	$content = convert_note_sub( $content, 'rew' );
	$content = convert_note_sub( $content, 'ne' );
	$content = convert_note_sub( $content, 're' );

	if ( is_home() ) {
		$content = str_replace( 'class="pnote">', 'class="pnote-home">', $content );
	}
	return $content;
}
add_filter( 'the_content', 'convert_note' );

function chinese_punctuations( $content ) {
	// add "non-breaking" around quotation marks
	$content = preg_replace(
		// 0 or 1 char between quotation marks
		'/(?<!\\\\)[“‘]+([^\\\\]|\\\\[…—·“‘’”])?[’”]+|' .
		// multiple chars between quotation marks
		'(?<!\\\\)[“‘]+([^\\\\<]|\\\\[…—·“‘’”])|' .
		'\\\\[…—·“‘’”][’”]+|[^\\\\>][’”]+/u', '<span class="non-breaking">$0</span>', $content );

	$content = preg_replace( '/(?<!\\\\)…+/u', '<span class="cn-ellipsis">$0</span>', $content );
	$content = preg_replace( '/(?<!\\\\)(—+|[·“‘’”])/u', '<span class="cn">$0</span>', $content );
	$content = preg_replace( '/(?<!\\\\)·/u', '<span class="full-width-char text-align-center">$0</span>', $content );
	$content = preg_replace( '/(?<!\\\\)[“‘]/u', '<span class="full-width-char text-align-right">$0</span>', $content );
	$content = preg_replace( '/(?<!\\\\)[’”]/u', '<span class="full-width-char">$0</span>', $content );
	$content = preg_replace( '/\\\\(?=[…—·“‘’”])/u', '', $content );
	return $content;
}
add_filter( 'the_content', 'chinese_punctuations' );
add_filter( 'the_title', 'chinese_punctuations' );

function replace_empty_p( $content ) {
	$content = str_replace('<p></p>', '<br>', $content);
	return $content;
}
add_filter( 'the_content', 'replace_empty_p' );

function modify_content_on_homepage( $content ) {
	if ( is_singular() ) {
		return $content;
	}
	$content = preg_replace('|<a[^>]*href ?= ?"#[^"]*"[^>]* (class ?= ?"[^"]*")[^>]*>(((?!/a>).)*)</a>|i', '<span $1>$2</span>', $content); // keep style
	$content = preg_replace('|<a[^>]*href ?= ?"#[^"]*"[^>]*>(((?!/a>).)*)</a>|i', '$1', $content);
	$content = preg_replace( '/<([^ >]+) [^>]*? class="hide-on-homepage">.*?\/\1>/s', '', $content );
	return $content;
}
add_filter( 'the_content', 'modify_content_on_homepage' );

function text_autospace() {
	// text-autospace.js is downloaded in wp-content/plugins/
	// source: https://github.com/mastermay/text-autospace.js
	wp_enqueue_script( 'text-autospace', plugins_url( 'text-autospace.min.js' ), array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'text_autospace' );

function convert_md_tag( $content ) {
	$from = array(
		'<p>[[play script begin]]</p>',
		'<p>[[play script end]]</p>',
		'<p>[[footnotes begin]]</p>',
		'<p>[[footnotes end]]</p>',
		'<p>[[references begin]]</p>',
		'<p>[[references end]]</p>'
	);
	$to = array(
		'<div class="play-script">',
		'</div>',
		'<div class="footnotes"><hr><blockquote>',
		'</blockquote></div>',
		'<div class="references">',
		'</div>'
	);
	$content = str_replace( $from, $to, $content );
	return $content;
}
add_filter( 'the_content', 'convert_md_tag' );

function add_actor_line_class( $content ) {
	$content = preg_replace('#<p>(<span class="character-name">)#', '<p class="actor-line">$1', $content);
	$content = preg_replace('#(<span class="character-name">((?!<\/span>).)*)(<\/span>)#', '$1<span hidden> </span>$3', $content);
	return $content;
}
add_filter( 'the_content', 'add_actor_line_class' );

function exclude_category_on_homepage( $query ) {
	if ( $query->is_home ) {
		$query->set( 'cat', '-6' );
	}
	return $query;
}
add_filter( 'pre_get_posts', 'exclude_category_on_homepage' );

function replace_to_en_space( $title ) {
	if ( substr( $title, 0, 3 ) === 'AWs' ) {
		$title = preg_replace('/ /', '&ensp;', $title, 1);
	}
	return $title;
}
add_filter( 'the_title', 'replace_to_en_space' );

function replace_to_hanla( $content ) {
	$content = str_replace( '&hl;', '<hanla></hanla>', $content );
	$content = str_replace( '&hlsp;', '<hanla> </hanla>', $content );
	return $content;
}
add_action( 'the_content', 'replace_to_hanla' );
add_filter( 'the_title', 'replace_to_hanla' );

function change_posts_per_page_for_mobile( $query ) {
	if( $query->is_main_query() && wp_is_mobile() ) {
		$query->set( 'posts_per_page', '5' );
	}
}
add_action( 'pre_get_posts', 'change_posts_per_page_for_mobile' );

function remove_paragraph_end_hidden_space( $content ) {
	$content = str_replace( '<span hidden> </span></p>', '</p>', $content );
	return $content;
}
add_filter( 'the_content', 'remove_paragraph_end_hidden_space' );

// remove auto emoji conversion
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7);
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'embed_head', 'print_emoji_detection_script' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
