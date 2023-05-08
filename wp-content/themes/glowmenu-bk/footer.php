<footer class="footer">
  <div class="col-lg-2 col-md-2 footer__branding">
	  <div class="footer__branding__logo"> 
	  	<a href="<?php bloginfo('url')?>/">
	       <?php include('inc/logo.php'); ?>
	    </a> 
	  </div>
  </div>
  <div class="col-lg-10 col-sm-12 col-xs-12 footer__nav">
       <?php dynamic_sidebar( 'Footer Menu') ?>
  </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>

<?php if (! is_user_logged_in() ) {
	$page = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$classes = get_body_class();
	
	if (in_array('home',$classes) || in_array('single-product',$classes)) {	

	 ?>
		<div class="modal sign-up-register">
			<div class="overlay"></div>
			<div class="container">
				<abbr>X</abbr>
				<div class="row">
					<div class="col-lg-7 col-sm-7 col-xs-12 col">
						<div id="login">
							<h4>Log in to Glowmenu</h4>
							<?php echo do_shortcode('[profilepress-login id="1" redirect="'.$page.'"]');?>
						</div>
						<div id="register" style="display:none;">
							<h4>Sign up to save progress</h4>
							<?php echo do_shortcode('[profilepress-registration id="1" redirect="'.$page.'"]');?>
						</div>
					</div>
					<div class="col-lg-5 col-sm-5 col-xs-12 col">
						<label>Or Sign in Using</label>
						<div class="api">
						<a class="facebook" href="http://glowmenus.com/login/?loginFacebook=1&redirect=<?php echo $page ?>" onclick="window.location = 'http://glowmenus.com/login/?loginFacebook=1&redirect=<?php echo $page ?>">Facebook</a>
						<a class="google" href="http://glowmenus.com/wp-login.php?loginGoogle=1&redirect=<?php echo $page ?>" onclick="window.location = 'http://glowmenus.com/wp-login.php?loginGoogle=1&redirect=<?php echo $page ?>">Google</a>
						</div>
						<hr/>
						<div class="link-register" style="display:none;">
							<label>Have an account?</label>
							<p><a id="to-login-box" href="#">Log in here</a></p>
						</div>
						<div class="link-sign-in">
							<label>Don't have an account?</label>
							<p><a id="to-signup-box" href="#">Sign up for free</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } 
	}
?>

</html>