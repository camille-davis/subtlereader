<?php
/**
 * The footer template file
 *
 * @package Subtle
 */

?>
<footer class="site-footer">
	<?php if ( is_active_sidebar( 'footer' ) ) : ?>
		<div class="footer">
			<?php dynamic_sidebar( 'footer' ); ?>
		</div>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
