		<div id="footer">
			&copy; 2007 <a href="<?php echo get_home_link(); ?>"><?php echo get_title(); ?></a>
			<br/>
			Powered by <a href="http://planetoid-project.org/" title="Planetoid <?php echo PLANETOID_VERSION.'.'.PLANETOID_REVISION; ?>">Planetoid</a> &amp; 
			<?php
					 require_once('../inc/simplepie/simplepie.inc');
					 echo simplepie_linkback(); ?>
		</div>
	</body>
</html>