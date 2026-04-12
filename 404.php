<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Subtle
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="error-404 not-found">
    <h1 class="entry-title"><?php esc_html_e( 'Page not found', 'subtlereader' ); ?></h1>
		<div class="entry-content">
			<p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button">
					<?php esc_html_e( 'Return to Homepage', 'subtlereader' ); ?>
				</a>
			</p>
		</div>
	</div>
</main>

<?php
get_footer();

