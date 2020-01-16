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

function add_tag_expect_slash( $content, $regex, $prefix, $suffix, $suffix_reg ) {
	$content = preg_replace( "#($regex)#u", "$prefix$1$suffix", $content );
	$content = preg_replace( "#\\\\$prefix($regex)$suffix_reg#u", "\\\\$1", $content );
	return $content;
}

function add_span_expect_slash( $content, $regex, $prefix ) {
	$suffix = '</span>';
	$suffix_reg = '<\\/span>';
	return add_tag_expect_slash( $content, $regex, $prefix, $suffix, $suffix_reg );
}

function chinese_punctuations( $content ) {
	$mark_line = '…|—';
	$mark_dot = '·';
	$mark_quot_l = '“|‘';
	$mark_quot_r = '”|’';
	$mark_all = "$mark_line|$mark_dot|$mark_quot_l|$mark_quot_r";

	$prefix_nobr = '<span class="non-breaking">';
	$prefix_cn = '<span class="cn">';
	$prefix_cnquot_l = '<span class="cn-quot text-align-right">';
	$prefix_cnquot_r = '<span class="cn-quot">';
	$prefix_fw = '<span class="full-width-char">';

	$subgrp_l = "(?:$mark_quot_l)+";
	$subgrp_r = "(?:$mark_quot_r)+";
	$subgrp_a  = "[^\\\\]?|\\\\(?:$mark_all)"; // expect \ and include \“
	$subgrp_b_l = "$|[^\\\\<]|\\\\(?:$mark_all)"; // expect “<
	$subgrp_b_r = "^|[^\\\\>]|\\\\(?:$mark_all)"; // expect >”

	// add "non-breaking" around quotation marks
	$grp = "(?:$subgrp_l)(?:$subgrp_a)(?:$subgrp_r)" // matching 0 or 1 char between quotation marks
		. "|(?:$subgrp_l)(?:$subgrp_b_l)|(?:$subgrp_b_r)(?:$subgrp_r)"; // matching multiple chars between quotation marks
	$content = add_span_expect_slash( $content, $grp, $prefix_nobr );

	// add "cn" around line and dot marks
	$grp = "(?:$mark_line)+|$mark_dot";
	$content = add_span_expect_slash( $content, $grp, $prefix_cn );

	// add "cn" around quotation marks
	$content = add_span_expect_slash( $content, $subgrp_l, $prefix_cnquot_l );
	$content = add_span_expect_slash( $content, $subgrp_r, $prefix_cnquot_r );

	// add "full-width-char" around dot and quotation marks
	$grp = "$mark_dot|$mark_quot_l|$mark_quot_r";
	$content = add_span_expect_slash( $content, $grp, $prefix_fw );

	// keep non-Chinese style
	$content = preg_replace( "#\\\\($mark_all)#", "$1", $content );
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

function change_posts_per_page_for_mobile( $query ) {
	if( $query->is_main_query() && wp_is_mobile() ) {
		$query->set( 'posts_per_page', '5' );
	}
}
add_action( 'pre_get_posts', 'change_posts_per_page_for_mobile' );
