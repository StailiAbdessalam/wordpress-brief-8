	</div> 
	<!-- end container -->

	<?php
		$enable_footer = get_theme_mod('enable_footer', true);
		if( $enable_footer == true ) { 
	?>
		<footer class="footer">
			<div class="container">
				<span class="copyright">
					<?php
						$copyright = get_theme_mod('copyright');
						if( $copyright ) {
							echo esc_attr( $copyright );
						} else {
							echo esc_attr__('© 2020 Bolby Theme.','bolby');
						}
					?>
				</span>
			</div>
		</footer>
	<?php } ?>

</main>
<!-- end main layout -->

<?php  if( true == get_theme_mod('go_top', true) ) : ?>
	<!-- Go to top button -->
	<a href="javascript:" id="return-to-top"><i class="fas fa-arrow-up"></i></a>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>