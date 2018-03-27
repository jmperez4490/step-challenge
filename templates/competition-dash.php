<div class = "dash">
	<div class = "dash-header">
		Welcome <?=$_SESSION['first_name']?>,
	</div>
	<div class = "data-calendar">
		<div class = "menu-list">
			<a href = "./register-steps">
				Record Steps
			</a>
			<a href = "./profile">
				Profile
			</a>
			<a href = "./log-out">
				Sign Out
			</a>
		</div>
		<div class = "content">
			<?php
				if($uri == "register-steps" || $uri == "dashboard") include_once("./templates/register-steps.php");
				if($uri == "profile") include_once("./templates/user-profile.php");
			?>
		</div>
	</div>
</div>
