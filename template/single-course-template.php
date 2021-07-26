<?php
sejoli_header();

/**
 * @since 3.0.0
 */
do_action( 'learn-press/before-main-content' );
do_action( 'learn-press/before-main-content-single-course' );

while ( have_posts() ) {
	the_post();
	learn_press_get_template( 'content-single-course' );
}

/**
 * @since 3.0.0
 */
do_action( 'learn-press/after-main-content-single-course' );
do_action( 'learn-press/after-main-content' );

/**
 * LP sidebar
 */
// do_action( 'learn-press/sidebar' );

sejoli_footer();
