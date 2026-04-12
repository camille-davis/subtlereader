<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package Subtle
 */

?>
<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'subtlereader' ); ?></h1>
<div class="entry-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms.', 'subtlereader' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'subtlereader' ); ?></p>
		<?php endif; ?>
</div>
