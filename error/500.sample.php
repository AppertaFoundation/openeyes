<?php header('HTTP/1.1 500 Internal Server Error');

class Config {
	function __construct($data) {
		$this->config = $data;
	}
}

if (file_exists("../protected/config/params.php")) {
	$t = new Config(require("../protected/config/params.php"));

	$helpdesk_phone = $t->config['params']['helpdesk_phone'];
	$helpdesk_email = $t->config['params']['helpdesk_email'];
}
?>
<!doctype html> 
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]--> 
<!--[if IE 7]>		<html class="no-js ie7 oldie" lang="en"> <![endif]--> 
<!--[if IE 8]>		<html class="no-js ie8 oldie" lang="en"> <![endif]--> 
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]--> 
<head> 
	<meta charset="utf-8"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
 
<title>OpenEyes - Login</title> 
	<meta name="viewport" content="width=device-width"> 
	<link rel="icon" href="favicon.ico" type="image/x-icon" /> 
	<link rel="shortcut icon" href="/favicon.ico"/> 
	<link rel="stylesheet" href="/css/style-error.css"> 
</head> 
 
<body> 
 
	<div id="container"> 
	<div id="header" class="clearfix"> 
		<div id="brand" class="ir"><a href="/site/index"><h1>OpenEyes</h1></a></div> 
	</div> <!-- #header --> 

	<!-- breadcrumbs -->

	<div id="content"> 
		<div id="down-form" class="form_greyBox">
			<h3>OpenEyes is broken</h3>
			<div style="height: 1em;"></div>
			<p>
				<strong>There has been a problem trying to access OpenEyes, please try again in a moment</strong>
			</p>
			<p>
				If there continues to be a problem please contact support
			</p>
			<p>
				Support Options
				<ul>
					<li>Immediate support (8:00am to 8:00pm) - Phone <?php echo @$helpdesk_phone?></li>
					<li>Less urgent issues email <a href="mailto:<?php echo @$helpdesk_email?>"><?php echo @$helpdesk_email?></a></li>
					<li>Log a support call or question at ???????????</li>
				</ul>
			</p>
		</div>
	</div> <!-- #content --> 
	<div id="help" class="clearfix"> 
	</div> <!-- #help --> 
	</div> 
	<!--#container --> 
	
	<div id="footer"> 
		<h6>&copy; Copyright OpenEyes Foundation 2011 &nbsp;&nbsp;|&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals &nbsp;&nbsp;|&nbsp;&nbsp; served by <?php echo trim(`hostname`)?></h6> 
	</div> <!-- #footer --> 
</body> 
</html> 
