<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php echo do_shortcode('[flipbook id="'.$post->ID.'" width="100%" height="100%"]'); ?>

<?php wp_footer(); ?>
<script>
	// Fix html { height:100%; } so flipbook displays full page
	jQuery('html').css('height', '100%');
</script>
</body>
</html>
