<?php
/**
 * The main template file
 *
 * @package Subtle
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;

		the_posts_pagination(
			array(
				'prev_text' => '&laquo; Previous page',
    				'next_text' => 'Next page &raquo;',
			)
		);

	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>
</main>

<?php
get_footer();
