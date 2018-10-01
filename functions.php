<?php
/**
 * https://codex.wordpress.org/Customizing_the_Read_More#Prevent_Page_Scroll_When_Clicking_the_More_Link
 */
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );

function prevent_autospace_after_pnote( $content ) {
	$content = preg_replace('|<a[^>]*class ?= ?"pnote"[^>]*>((?!/a>).)*</a>|i', '$0<span hidden> </span>', $content);
	return $content;
}
add_filter( 'the_content', 'prevent_autospace_after_pnote' );

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
	$content = preg_replace('|<a[^>]*href ?= ?"#[^"]*"[^>]*(class ?= ?"[^"]*")[^>]*>(((?!/a>).)*)</a>|i', '<span $1>$2</span>', $content); // keep style
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
