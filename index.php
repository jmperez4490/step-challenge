<?php
require_once('php_code/api-manager.php');
session_start();
$path = "http://$_SERVER[HTTP_HOST]/";
$uri = "$_SERVER[REQUEST_URI]";
if($uri != "/server/api/")
	$uri = str_replace("/","","$_SERVER[REQUEST_URI]");
$api = new apiManager();

if($uri == "/server/api/") {
	if($_POST['action'] == "login") {
		$responds = $api -> Control($_POST);
		if(array_key_exists('token', $responds)) {
			$_SESSION = $responds;
			echo TRUE;
		}
		else {
			echo $responds[0];
		}
		return;
	}
	else 
		return $api -> Control($_POST);
}
elseif(array_key_exists('token', $_SESSION) == FALSE and strlen($uri) > 1 and $uri != 'register')
{
	header('location: ./');
}
elseif ($uri == "log-out") {
	session_unset();
}
?>
<!DOCTYPE html>
	<head>
		<title> Health &amp; Wellness | Step Challenge</title>
		<link rel="stylesheet" type="text/css" href="<?=$path?>stylesheet/home.css">
		<link href="https://fonts.googleapis.com/css?family=Karma:400,600" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8">
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script type="text/javascript" src = "<?=$path?>JavaScript/management.js"></script>
	</head>

	<body>
		<div class = "content-body">
			<div class = "header">
				<img src = "<?=$path?>images/login-screen.jpeg">
				<?php
					switch ($uri) {
					 	case "":
					 	case "log-out":
					 		include_once('./templates/login-page.php');
					 		break;
					 	
					 	case "register":
					 		include_once('./templates/register-user.php');
					 		break;
					 	case "team-progress":
					 	case "dashboard":
					 	case "register-steps":
					 	case "profile":
					 		include_once("./templates/competition-dash.php");
					 		break;
					 	default:
					 		# code...
					 		break;
					 } 
				?>
			</div>
		</div>
	</body>
</html>
