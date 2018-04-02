<div class = "dash">
	<div class = "dash-header">
		Welcome <?=$_SESSION['first_name']?>,
	</div>
	<div class = "data-calendar">
		<div class = "menu">
			<div class = "menu-list">
				<input type = "checkbox">
				<span></span>
				<span></span>
				<span></span>
				<ul id = "menu">
					<li>
						<a href = "./register-steps">
							Record Steps
						</a>
					</li>
					<li>
						<a href = "./team-progress">
							Team Progress
						</a>
					</li>
					<li>
						<a href = "./profile">
							Profile
						</a>
					</li>
					<li>
						<a href = "./log-out">
							Sign Out
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class = "content">
			<?php
				if($uri == "register-steps" || $uri == "dashboard") include_once("./templates/register-steps.php");
				if($uri == "profile") include_once("./templates/user-profile.php");
				if($uri == "team-progress") include_once("./templates/team-steps.php");
			?>
		</div>
	</div>
</div>
