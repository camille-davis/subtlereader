<?php
/**
 * The template for displaying all single posts
 *
 * @package Subtle
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
				get_template_part( 'template-parts/content' );
				the_post_navigation(
					array(
						'prev_text' => '<span aria-hidden="true">&laquo;</span><span class="visually-hidden">Previous post:</span> %title',
						'next_text' => '<span class="visually-hidden">Next post:</span> %title <span aria-hidden="true">&raquo;</span>',
					)
				);
			?>
		</article>

	<?php endwhile; ?>
</main>

<?php
get_footer();
