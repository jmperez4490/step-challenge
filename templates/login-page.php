<div class = "login">
	<div id = "message"></div>
	<div class = "label-group">
		<label>Email:</label>
		<div>
			<input type = "text" name = "user-name">
		</div>
	</div>
	<div class = "label-group">
		<label>Password:</label>
		<div>
			<input type="password" name="user-password">
		</div>
	</div>
	<div class = "login-btn">
		Login
	</div>
	<a href = "<?=$path?>register">Register</a>
</div>
<script type="text/javascript">
	login_registration.login();
</script>

