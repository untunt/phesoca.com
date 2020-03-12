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
	$arg = '([^\[\|\]]*)'; // argument: any text expect [, |, ]
	$sep = '(?:\||\&#124;)'; // separator: &#124; = |

	$out_n_head = '<span id="pn$2" class="pnote"><span class="annotated">$1</span><a href="#$4" class="note">';
	$out_r_head = str_replace('n$', 'r$', $out_n_head);
	$out_n = $out_n_head . '*<sup>$3$5</sup></a></span><span hidden> </span>';
	$out_r = $out_r_head . '<sup>[$3]$5</sup></a></span>';

	// anchor with 'p' (in the body)
	// format: ^[text|anchor|note text|link|suffix]
	// 5 arguments
	$content = preg_replace("@\^\[(?:$arg$sep)pn$arg$sep$arg$sep$arg$sep$arg\]@i", $out_n, $content);
	$content = preg_replace("@\^\[(?:$arg$sep)pr$arg$sep$arg$sep$arg$sep$arg\]@i", $out_r, $content);
	$out_n = str_replace('$5', '', $out_n);
	$out_r = str_replace('$5', '', $out_r);
	// 4 arguments
	$content = preg_replace("@\^\[(?:$arg$sep)pn$arg$sep$arg$sep$arg\]@i", $out_n, $content);
	$content = preg_replace("@\^\[(?:$arg$sep)pr$arg$sep$arg$sep$arg\]@i", $out_r, $content);
	$out_n = str_replace('$4', 'n$2', $out_n);
	$out_r = str_replace('$4', 'r$2', $out_r);
	// 3 arguments
	$content = preg_replace("@\^\[(?:$arg$sep)pn$arg$sep$arg\]@i", $out_n, $content);
	$content = preg_replace("@\^\[(?:$arg$sep)pr$arg$sep$arg\]@i", $out_r, $content);
	$out_n = str_replace('$3', '$2', $out_n);
	$out_r = str_replace('$3', '$2', $out_r);
	// 1 or 2 arguments
	$content = preg_replace("@\^\[(?:$arg$sep)?pn$arg\]@i", $out_n, $content);
	$content = preg_replace("@\^\[(?:$arg$sep)?pr$arg\]@i", $out_r, $content);

	$out_n_head = '<span class="note-block"><span id="n$1">';
	$out_r_head = str_replace('n$', 'r$', $out_n_head);
	$out_head_link = '<a href="#$3" class="note">';
	$out_n = $out_n_head . $out_head_link . '*$2$4</a></span></span><span hidden> </span>';
	$out_r = $out_r_head . $out_head_link . '[$2]$4</a></span></span>';
	$out_n_without_link = str_replace('</a>', '', str_replace($out_head_link, '', $out_n));
	$out_r_without_link = str_replace('</a>', '', str_replace($out_head_link, '', $out_r));
	$no_link = '_no_link'; // flag in `anchor` to remove <a> tag

	// anchor without 'p' (at the end)
	// format: ^[anchor|note text|link|suffix]
	// 4 arguments
	$content = preg_replace("@\^\[n$arg$no_link$sep$arg$sep$arg$sep$arg\] *@i", $out_n_without_link, $content);
	$content = preg_replace("@\^\[r$arg$no_link$sep$arg$sep$arg$sep$arg\] *@i", $out_r_without_link, $content);
	$content = preg_replace("@\^\[n$arg$sep$arg$sep$arg$sep$arg\] *@i", $out_n, $content);
	$content = preg_replace("@\^\[r$arg$sep$arg$sep$arg$sep$arg\] *@i", $out_r, $content);
	$out_n_without_link = str_replace('$4', '', $out_n_without_link);
	$out_r_without_link = str_replace('$4', '', $out_r_without_link);
	$out_n = str_replace('$4', '', $out_n);
	$out_r = str_replace('$4', '', $out_r);
	// 3 arguments
	$content = preg_replace("@\^\[n$arg$no_link$sep$arg$sep$arg\] *@i", $out_n_without_link, $content);
	$content = preg_replace("@\^\[r$arg$no_link$sep$arg$sep$arg\] *@i", $out_r_without_link, $content);
	$content = preg_replace("@\^\[n$arg$sep$arg$sep$arg\] *@i", $out_n, $content);
	$content = preg_replace("@\^\[r$arg$sep$arg$sep$arg\] *@i", $out_r, $content);
	$out_n_without_link = str_replace('$3', 'pn$1', $out_n_without_link);
	$out_r_without_link = str_replace('$3', 'pr$1', $out_r_without_link);
	$out_n = str_replace('$3', 'pn$1', $out_n);
	$out_r = str_replace('$3', 'pr$1', $out_r);
	// 2 arguments
	$content = preg_replace("@\^\[n$arg$no_link$sep$arg\] *@i", $out_n_without_link, $content);
	$content = preg_replace("@\^\[r$arg$no_link$sep$arg\] *@i", $out_r_without_link, $content);
	$content = preg_replace("@\^\[n$arg$sep$arg\] *@i", $out_n, $content);
	$content = preg_replace("@\^\[r$arg$sep$arg\] *@i", $out_r, $content);
	$out_n_without_link = str_replace('$2', '$1', $out_n_without_link);
	$out_r_without_link = str_replace('$2', '$1', $out_r_without_link);
	$out_n = str_replace('$2', '$1', $out_n);
	$out_r = str_replace('$2', '$1', $out_r);
	// 1 argument
	$content = preg_replace("@\^\[n$arg$no_link\] *@i", $out_n_without_link, $content);
	$content = preg_replace("@\^\[r$arg$no_link\] *@i", $out_r_without_link, $content);
	$content = preg_replace("@\^\[n$arg\] *@i", $out_n, $content);
	$content = preg_replace("@\^\[r$arg\] *@i", $out_r, $content);

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
