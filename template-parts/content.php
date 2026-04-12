<?php
/**
 * Template part for displaying content
 *
 * @package Subtle
 */

if ( is_singular() ) {
	the_title( '<h1 class="entry-title" id="title">', '</h1>' );
	?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php
} else {
	the_title(
		'<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">',
		'</a></h1>'
	);
	?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
		<p class="more-link-wrap">
			<a href="<?php the_permalink(); ?>" class="more-link">
				Continue reading
				<span class="visually-hidden">
					<?php the_title(); ?>
				</span>
			</a>
		</p>
	</div>
	<?php
}
