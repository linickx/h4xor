
<hr />
<div id="footer">
	<?php if (function_exists('themehacker_browser')) {
		themehacker_browser(); 
	} ?>
	<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	
	<p>
		<?php bloginfo('name'); ?> powered by 
		<a href="http://wordpress.org">WordPress</a>
		<br />h4x0r theme by <a href="http://www.linickx.com">[LINICKX]</a>
		<?php echo $wpdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds.
	</p>
</div>
</div>


		<?php do_action('wp_footer'); ?>

</body>
</html>
