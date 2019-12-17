<?php
/**
 * https://codex.wordpress.org/Customizing_the_Read_More#Prevent_Page_Scroll_When_Clicking_the_More_Link
 */
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );

function convert_note( $content ) {
	// with 'p' (in the body)
	$content = preg_replace('|\^\[([^\[\|\]]*)\|pn([^\[\|\]]*)\]|i', '<span id="pn$2" class="pnote"><span class="annotated">$1</span><a href="#n$2" class="note">*<sup>$2</sup></a></span><span hidden> </span>', $content);
	$content = preg_replace('|\^\[([^\[\|\]]*)\|pr([^\[\|\]]*)\]|i', '<span id="pr$2" class="pnote"><span class="annotated">$1</span><a href="#r$2" class="note"><sup>[$2]</sup></a></span>', $content);
	$content = preg_replace('|\^\[([^\[\|\]]*)&#124;pn([^\[\|\]]*)\]|i', '<span id="pn$2" class="pnote"><span class="annotated">$1</span><a href="#n$2" class="note">*<sup>$2</sup></a></span><span hidden> </span>', $content);
	$content = preg_replace('|\^\[([^\[\|\]]*)&#124;pr([^\[\|\]]*)\]|i', '<span id="pr$2" class="pnote"><span class="annotated">$1</span><a href="#r$2" class="note"><sup>[$2]</sup></a></span>', $content);
	$content = preg_replace('|\^\[pn([^\[\|\]]*)\]|i', '<span id="pn$1" class="pnote"><a href="#n$1" class="note">*<sup>$1</sup></a></span><span hidden> </span>', $content);
	$content = preg_replace('|\^\[pr([^\[\|\]]*)\]|i', '<span id="pr$1" class="pnote"><a href="#r$1" class="note"><sup>[$1]</sup></span></a></span>', $content);
	
	// without 'p' (at the end)
	$content = preg_replace('|\^\[n([^\[\|\]]*)\] *|i', '<span class="note-block"><span id="n$1"><a href="#pn$1" class="note">*$1</a></span></span><span hidden> </span>', $content);
	$content = preg_replace('|\^\[r([^\[\|\]]*)\] *|i', '<span class="note-block"><span id="r$1"><a href="#pr$1" class="note">[$1]</a></span></span>', $content);
	
	if ( is_home() ) {
		$content = str_replace('class="pnote">', 'class="pnote-home">', $content);
	}
	return $content;
}
add_filter( 'the_content', 'convert_note' );

function chinese_punctuations( $content ) {
	$prefix = '<span class="cn">';
	$prefix_q_l = '<span class="cn-quot cn-quot-left">';
	$prefix_q_r = '<span class="cn-quot cn-quot-right">';
	$suffix = '</span>';

	$from = array('…', '—', '·');
	$from_q_l = array('“', '‘');
	$from_q_r = array('”', '’', '');

	// assemble output strings
	$to = $from;
	foreach ($to as &$punct) {
		$punct = $prefix . $punct . $suffix;
	}
	$to_q_l = $from_q_l;
	foreach ($to_q_l as &$punct) {
		$punct = $prefix_q_l . $punct . $suffix;
	}
	$to_q_r = $from_q_r;
	foreach ($to_q_r as &$punct) {
		$punct = $prefix_q_r . $punct . $suffix;
	}

	$from = array_merge($from, $from_q_l, $from_q_r);
	$to = array_merge($to, $to_q_l, $to_q_r);
	$except = $to;
	foreach ($except as &$punct) {
		$punct = '\\' . $punct;
	}

	$content = str_replace($from, $to, $content);
	$content = str_replace($except, $from, $content);
	return $content;
}
add_filter( 'the_content', 'chinese_punctuations' );
add_filter( 'the_title', 'chinese_punctuations' );

function replace_empty_p( $content ) {
	$content = str_replace('<p></p>', '<br>', $content);
	return $content;
}
add_filter( 'the_content', 'replace_empty_p' );

function remove_anchors_in_homepage_content( $content ) {
	if ( !is_home() ) {
		return $content;
	}
	$content = preg_replace('|<a[^>]*href ?= ?"#[^"]*"[^>]* (class ?= ?"[^"]*")[^>]*>(((?!/a>).)*)</a>|i', '<span $1>$2</span>', $content); // keep style
	$content = preg_replace('|<a[^>]*href ?= ?"#[^"]*"[^>]*>(((?!/a>).)*)</a>|i', '$1', $content);
	return $content;
}
add_filter( 'the_content', 'remove_anchors_in_homepage_content' );

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
	return str_replace("&hlsp;", "<hanla> </hanla>", $content );
}
add_action( 'the_content', 'replace_to_hanla' );
add_filter( 'the_title', 'replace_to_hanla' );
