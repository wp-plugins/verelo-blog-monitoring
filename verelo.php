<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

global $verelo;
$action = '';
$location = "options-general.php?page=verelo"; // based on the location of your sub-menu page

switch($action) :
default:
endswitch;

if ($action) {
	// clear $_POST array if needed
	// redirect after header definitions - cannot use wp_redirect($location);
	?>
   	<script type="text/javascript">
   	<!--
	window.location='<?php echo $location; ?>';
   	//-->
   	</script>
	<?php
	exit;
}	

$messages[1] = __('Blog montoring', 'verelo');

if ( isset($_GET['message']) && (int) $_GET['message'] ) {
	$message = $messages[$_GET['message']];
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
	
$title = __('Verelo Blog Monitoring', 'verelo');
$admin_email = get_option('admin_email');
?>
    <div class="wrap">   
    <?php screen_icon(); ?>
    <h2>Blog Monitoring by <a href="http://www.verelo.com">verelo.com</a></h2>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p>Blog monitoring is currently enabled.</div>
	<h3>Quick Login Details</h3>
	<p>
		<strong><a href="<?php echo get_option("verelo_autologin_service_url","https://app.verelo.com/"); ?>">Click here to auto login</a></strong>
	</p>
	<h3>Manual Login</h3>
	<p>
	To login manually visit: <a href="https://app.verelo.com/" target="_BLANK">https://app.verelo.com/</a><br/>
	<strong>User:</strong> <?php echo $admin_email; ?><br/>
	<strong>Password:</strong> Was randomly generated and emailed to <?php echo $admin_email; ?>. <a target="_BLANK" href="https://app.verelo.com/forgotpassword?email=<?php echo urlencode($admin_email); ?>">Forgot your password?</a><br/>
	</p>
	<h3>What does Verelo Blog Monitoring do?</h3>
	<p>We check your site every 5 minutes from multiple locations around the world and ensure it is working correctly. If we spot an issue we will notify you by email, SMS or Phone. Blog monitoring is free for the first two URL's, you only need to upgrade if you wish to obtain SMS or Phone notifications and monitor more than 2 end points.</p>

</div>


