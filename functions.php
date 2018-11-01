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
	$content = preg_replace('|\^\[([^\[\|\]]*)\|pn([^\[\|\]]*)\]|i', '<a href="#n$2" id="pn$2" class="pnote">$1<span class="sup">*<sup>$2</sup></span></a><span hidden> </span>', $content);
	$content = preg_replace('|\^\[([^\[\|\]]*)\|pr([^\[\|\]]*)\]|i', '<a href="#r$2" id="pr$2" class="pnote">$1<span class="sup"><sup>[$2]</sup></span></a>', $content);
	$content = preg_replace('|\^\[pn([^\[\|\]]*)\]|i', '<a href="#n$1" id="pn$1" class="pnote"><span class="sup">*<sup>$1</sup></span></a><span hidden> </span>', $content);
	$content = preg_replace('|\^\[pr([^\[\|\]]*)\]|i', '<a href="#r$1" id="pr$1" class="pnote"><span class="sup"><sup>[$1]</sup></span></a>', $content);
	
	// without 'p' (at the end)
	$content = preg_replace('|\^\[n([^\[\|\]]*)\]|i', '<a href="#pn$1" id="n$1" class="note">*$1</a><span hidden> </span>', $content);
	$content = preg_replace('|\^\[r([^\[\|\]]*)\]|i', '<a href="#pr$1" id="r$1" class="note">[$1]</a>', $content);
	return $content;
}
add_filter( 'the_content', 'convert_note' );

function chinese_punctuations( $content ) {
	$prefix = '<span class="cn">';
	$prefix_q = '<span class="cn-quot">';
	$suffix = '</span>';

	$from = array('…', '—', '·');
	$from_q = array('“', '”', '‘', '’', '');
	$to = $from;
	foreach ($to as &$punct) {
		$punct = $prefix . $punct . $suffix;
	}
	$to_q = $from_q;
	foreach ($to_q as &$punct) {
		$punct = $prefix_q . $punct . $suffix;
	}
	
	$from = array_merge($from, $from_q);
	$to = array_merge($to, $to_q);
	$except = $to;
	foreach ($except as &$punct) {
		$punct = '\\' . $punct;
	}
	
	$content = str_replace($from, $to, $content);
	$content = str_replace($except, $from, $content);
	return $content;
}
add_filter( 'the_content', 'chinese_punctuations' );

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
