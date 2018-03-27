<?php
$info = $api -> getProfileInfo();
?>
<div id = "message"></div>
<div class = "profile-update">
	<div class = "content-holder">
		<div class = "label">
			Name:
		</div>
		<div class = "form-field">
			<input type="text" name="first_name" value = '<?=$info['first_name']?>'>
		</div>
	</div>
	<div class = "content-holder">
		<div class = "label">
			Team Name:
		</div>
		<div class = "form-field">
			<input type="text" value = '<?=$info['team_name']?>' disabled>
		</div>
	</div>
	<div class = "content-holder">
		<div class = "label">
			Captain:
		</div>
		<div class = "form-field">
			<input type="text" value = '<?=$info['team_captain']?>' disabled>
		</div>
	</div>
	<div class = "content-holder">
		<div class = "label">
			Email:
		</div>
		<div class = "form-field">
			<input type="email" name="user-email" value = '<?=$info['username']?>'>
		</div>
	</div>
	<div class = "content-holder">
		<div class = "label">
			Password:
		</div>
		<div class = "form-field">
			<input type = "password" name = "user-password" >
		</div>
	</div>
	<div class = "content-holder">
		<div class = "btn update-info">
			Update Profile
		</div>
	</div>
</div>
<script type="text/javascript">
	update_profile._init();
</script>
