<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template part for displaying content
 */

if ( is_singular() ) {
	?>
	<section class="l-section"><div class="l-section-h i-cf">
	<?php
	the_title( '<h1 class="entry-title">', '</h1>' );
} else {
	?>
	<article <?php post_class(); ?>>
	<?php
	the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
}

// Display the content
the_content();

// Display post pagination
us_wp_link_pages( 1 );

// Display date
echo sprintf(
	'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
	esc_attr( get_the_date( DATE_W3C ) ),
	esc_html( get_the_date() )
);

// Display tags list, needed for Theme Check
the_tags();

// For posts loop
if ( is_singular() ) {
	?>
	</div></section>
	<?php
} else {
	?>
	</article>
	<?php
}

// Display the comments section
comments_template();
