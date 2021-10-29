<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs EDITOR styles based on theme options, used in TinyMCE and Gutenberg
 *
 * @var $editor string EDITOR type: 'tinymce' / 'gutenberg'
 */

$is_tinymce = FALSE;
if ( ! isset( $editor ) ) {
	return;
} elseif ( $editor == 'gutenberg' ) {
	$body = 'div.editor-styles-wrapper';
	$prefix = 'div.editor-styles-wrapper ';
} elseif ( $editor == 'tinymce' ) {
	$body = 'body.mce-content-body[data-id=content]';
	$prefix = 'body.mce-content-body[data-id=content] ';
	$is_tinymce = TRUE;
} else {
	return;
}
?>

/* Separated styles
   =============================================================================================================================== */

<?php if ( $editor == 'gutenberg' ) { ?>
figure {
	margin: 0;
	}
.wp-block-image figcaption,
.wp-block-embed figcaption,
.wp-block-pullquote {
	color: inherit;
	border-color: currentColor;
	}
blockquote.is-style-large,
.wp-block-pullquote blockquote {
	padding: 0 !important;
	}
blockquote.is-style-large:before,
.wp-block-pullquote blockquote:before {
	display: none !important;
	}
.wp-block,
.wp-block[data-align="wide"] {
	max-width: <?php echo us_get_option( 'site_content_width' ) ?>;
	}
.wp-block[data-align="full"] {
	max-width: none;
	}
.editor-post-title__block .editor-post-title__input {
	font-family: inherit !important;
	color: inherit !important;
	}
.editor-styles-wrapper .wp-block-quote__citation,
.editor-styles-wrapper .wp-block-quote cite,
.editor-styles-wrapper .wp-block-quote footer {
	font-size: 1rem;
	margin-top: 0.5rem;
	color: inherit;
	}

<?php } elseif ( $editor == 'tinymce' ) { ?>

strong {
	font-weight: 600;
	}
.mce-content-body {
	max-width: <?php echo us_get_option( 'site_content_width' ) ?>;
	}
.mce-content-body a[data-mce-selected] {
	box-shadow: none;
	background: <?php echo us_hex2rgba( us_get_color( '_content_link', FALSE, FALSE ), 0.2 ) ?>;
	}
h1, h2, h3, h4, h5, h6 {
	font-family: inherit;
	line-height: 1.4;
	margin: 0 0 1.5rem;
	padding-top: 1.5rem;
	}
h1:first-child,
h2:first-child,
h3:first-child,
h4:first-child,
h5:first-child,
h6:first-child,
h1 + h2, h1 + h3, h1 + h4, h1 + h5, h1 + h6,
h2 + h3, h2 + h4, h2 + h5, h2 + h6,
h3 + h4, h3 + h5, h3 + h6,
h4 + h5, h4 + h6,
h5 + h6 {
	padding-top: 0;
	}
p,
ul,
ol,
dl,
address,
pre,
table,
blockquote,
fieldset {
	margin: 0 0 1.5rem;
	}

<?php } ?>

/* Common styles
   =============================================================================================================================== */

<?php echo $prefix; ?>a {
	color: <?php echo us_get_color( '_content_link', FALSE, FALSE ) ?>;
	}
<?php echo $prefix; ?>ul li,
<?php echo $prefix; ?>ol li {
	margin: 0 0 0.5rem;
	}
<?php echo $prefix; ?>li > ul,
<?php echo $prefix; ?>li > ol {
	margin-bottom: 0.5rem;
	margin-top: 0.5rem;
	}
<?= $is_tinymce ? '' : $prefix; ?>blockquote {
	position: relative;
	padding: 0 3rem;
	font-size: 1.3em;
	line-height: 1.7;
	border: none;
	}
<?= $is_tinymce ? '' : $prefix; ?>blockquote:before {
	content: '\201C';
	display: block;
	font-size: 6rem;
	line-height: 0.8;
	font-family: Georgia, serif;
	position: absolute;
	left: 0;
	}
<?= $is_tinymce ? '' : $prefix; ?>blockquote p,
<?= $is_tinymce ? '' : $prefix; ?>blockquote ul,
<?= $is_tinymce ? '' : $prefix; ?>blockquote ol {
	margin-top: 0;
	margin-bottom: 0.5em;
	}
<?= $is_tinymce ? '' : $prefix; ?>pre {
	display: block;
	font-family: Consolas, Lucida Console, monospace;
	font-size: 0.9rem;
	line-height: 1.5rem;
	padding: 0.8rem 1rem;
	width: 100%;
	overflow: auto;
	background: #faf6e1;
	color: #333;
	}
<?php echo $prefix; ?>h1,
<?php echo $prefix; ?>h2,
<?php echo $prefix; ?>h3,
<?php echo $prefix; ?>h4,
<?php echo $prefix; ?>h5,
<?php echo $prefix; ?>h6 {
	color: <?php echo us_get_color( '_content_heading', FALSE, FALSE ) ?>;
	}
<?php echo $prefix; ?>td,
<?php echo $prefix; ?>th {
	border-color: <?php echo us_get_color( '_content_border', FALSE, FALSE ) ?>;
	}

<?php

// Global Text
$css = $body . '{';
$css .= 'font-family:' . strstr( us_get_option( 'body_font_family' ), '|', TRUE ) . ';';
$css .= 'font-size:' . us_get_option( 'body_fontsize' ) . ';';
$css .= 'line-height:' . us_get_option( 'body_lineheight' ) . ';';
$css .= 'background:' . us_get_color( '_content_bg', TRUE, FALSE ) . ';';
$css .= 'color:' . us_get_color( '_content_text', FALSE, FALSE ) . ';';
$css .= '}';

// Headings h1-h6
for ( $i = 1; $i <= 6; $i ++ ) {
	$css .= $prefix . 'h' . $i . '{';

	// Get only font-name, because the value also contains the font-weight
	$font_name = strstr( us_get_option( 'h' . $i . '_font_family' ), '|', TRUE );

	// For 'get_h1' value get the Heading 1 font name
	if ( $font_name === 'get_h1' ) {
		$font_name = strstr( us_get_option( 'h1_font_family' ), '|', TRUE );
	}

	// Exclude "No font specified" value
	if ( $font_name !== 'none' ) {
		$css .= 'font-family:' . $font_name . ';';
	}

	$css .= 'font-weight:' . us_get_option( 'h' . $i . '_fontweight' ) . ';';
	$css .= 'font-size:' . us_get_option( 'h' . $i . '_fontsize' ) . ';';
	$css .= 'line-height:' . us_get_option( 'h' . $i . '_lineheight' ) . ';';
	$css .= 'letter-spacing:' . us_get_option( 'h' . $i . '_letterspacing' ) . ';';
	if ( $editor == 'tinymce' ) {
		$css .= 'margin-bottom:' . us_get_option( 'h' . $i . '_bottom_indent' ) . ';';
	}
	if ( ! empty( us_get_option( 'h' . $i . '_transform' ) ) ) {
		if ( strpos( us_get_option( 'h' . $i . '_transform' ), 'italic' ) !== FALSE ) {
			$css .= 'font-style: italic;';
		}
		if ( strpos( us_get_option( 'h' . $i . '_transform' ), 'uppercase' ) !== FALSE ) {
			$css .= 'text-transform: uppercase;';
		}
	}
	if ( $color = us_get_color( us_get_option( 'h' . $i . '_color' ), FALSE, FALSE ) ) {
		$css .= 'color:' . $color . ';';
	}
	$css .= '}';
}

echo strip_tags( $css );
?>
