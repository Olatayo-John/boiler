<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/login.css'); ?>">

<div class="loginbox col-md-6 col-sm-12">
	<form action="<?php echo base_url('login'); ?>" method="post" id="loginForm">

		<input type="hidden" class="csrf_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

		<div class="form-group">
			<label>Username</label> <span>*</span>
			<input type="text" name="uname" class="form-control uname" placeholder="Your Username" required>
		</div>

		<div class="form-group">
			<label>Password</label> <span>*</span>
			<input type="password" name="pwd" class="form-control pwd" placeholder="Your Password" required>
		</div>

		<!-- <div class="form-group">
			<input type="checkbox">
			<span>Remember me</span>
		</div> -->

		<div class="form-group">
			<button class="btn text-light btn-block loginbtn" type="submit">Login</button>
		</div>
		<div class="signup">
			<a href="<?php echo base_url('register'); ?>" class="signupbtn">
				<i class="fas fa-user-plus mr-1"></i>Create Account
			</a>
		</div>
	</form>
</div>
<script>
	$(document).ready(function() {
		$('button.loginbtn').click(function() {
			// e.preventDefault();

			var name = $('.uname').val();
			var pass = $('.pwd').val();

			if (name == "" || name == null) {
				return false;
			}

			if (pass == "" || pass == null) {
				return false;
			}
		});
	});
</script>