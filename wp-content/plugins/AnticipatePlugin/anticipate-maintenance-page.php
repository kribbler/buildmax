<?php
    global $ET_Anticipate;
    if ( isset($_POST['anticipate_email']) ) $ET_Anticipate->add_email( $_POST['anticipate_email'] );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BuildMax - Laser Technology for Construction  |  Site Coming Soon!</title>

	<link rel="stylesheet" type="text/css" href="<?php echo $ET_Anticipate->location_folder ; ?>/css/style.css" />

	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo $ET_Anticipate->location_folder ; ?>/css/ie6style.css" />
		<script type="text/javascript" src="<?php echo $ET_Anticipate->location_folder ; ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>
		<script type="text/javascript">DD_belatedPNG.fix('img#logo, #anticipate-top-shadow, #anticipate-center-highlight, #anticipate-overlay, #anticipate-piece, #anticipate-social-icons img');</script>
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo $ET_Anticipate->location_folder ; ?>/css/ie7style.css" />
	<![endif]-->
	<?php wp_head(); ?>
</head>

<style>
body { background-image: url("http://buildmax.co.nz/wp-content/uploads/Background.png"); background-repeat: repeat-x;}
.content { margin-left: auto; margin-right: auto; margin-top: 0px; width: 1455px; }
</style>
<body>
<div class="content"><img src="http://buildmax.co.nz/wp-content/uploads/Landing.png" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="833,503,1037,531" href="mailto:info@buildmax.co.nz">
    <area shape="rect" coords="1180,572,1274,603" href="http://www.studioeleven.co.nz" target="_blank">
    <area shape="rect" coords="602,504,735,529" href="tel:+64274284536">
  </map>
</div>
</body>
</html>