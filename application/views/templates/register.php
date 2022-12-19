<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/register.css'); ?>">

<div class="wrapper">
	<form action="<?php echo base_url('register'); ?>" method="post" class="bg-light-custom p-3" id="regForm">
		<input type="hidden" class="csrf_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

		<div class="row">
			<div class="form-group col">
				<label>First Name</label>
				<input type="text" name="fname" class="form-control fname" placeholder="Your First Name" value="<?php echo set_value('fname'); ?>">
			</div>
			<div class="form-group col">
				<label>Last Name</label>
				<input type="text" name="lname" class="form-control lname" placeholder="Your Last Name" value="<?php echo set_value('lname'); ?>">
			</div>
		</div>

		<div class="form-group">
			<label>E-mail</label> <span>*</span>
			<input type="email" name="email" class="form-control email" placeholder="example@domain.com" id="email" value="<?php echo set_value('email'); ?>" required>
		</div>

		<div class="form-group">
			<label>Mobile</label> <span>*</span>
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">+91</span>
				</div>

				<input type="number" name="mobile" class="form-control mobile" placeholder="0123456789" id="mobile" value="<?php echo set_value('mobile'); ?>" required>
			</div>
			<div class="err mobileerr">Invalid mobile length</div>
		</div>

		<div class="form-group">
			<label>Username</label> <span>*</span>
			<input type="text" name="uname" class="form-control uname" placeholder="Pick a username" id="uname" required>
			<span class="unameerr err">Username already exist</span>
		</div>

		<div class="form-group">
			<label>Password</label> <span>*</span>

			<div class="input-group">
				<input type="text" name="pwd" class="form-control pwd" placeholder="Password must be over 6 characters long" id="pwd" minlength="6">
				<div class="input-group-prepend">
					<button class="input-group-text btn genpwdbtn" type="button" name="genpwdbtn">Generate Password</button>
				</div>
			</div>

			<span class="err pwderr">Password is too short</span>
		</div>

		<hr>


		<div class="btngrp pt-3 pb-3">
			<button class="btn text-light registerbtn" type="submit" style="background:#294a63">Create Account</button>
			<a href="<?php echo base_url('login'); ?>" class="loginbtn text-danger" style="colosr:#294a63">
				Already a user?</a>
		</div>
	</form>
</div>





<script>
	$(document).ready(function() {

		//check for duplicate username
		$(".unaddme").keyup(function() {
			var uname_val = $(".uname").val();
			var csrfName = $(".csrf_token").attr("name");
			var csrfHash = $(".csrf_token").val();

			$.ajax({
				url: "<?php echo base_url("duplicateusername") ?>",
				method: "post",
				dataType: "json",
				data: {
					[csrfName]: csrfHash,
					uname_val: uname_val
				},
				success: function(data) {
					$(".csrf_token").val(data.token);
					if (data.user_data > 0) {
						$('.unameerr').show();
						$(".registerbtn").attr("type", "button");
					} else {
						$('.unameerr').hide();
						$(".registerbtn").attr("type", "submit");
					}
				},
				error: function(data) {
					alert('error filtering. Please refresh and try again');
				}
			});
		});

		//generate random password
		$("button.genpwdbtn").click(function() {
			$('.pwd').val(returnPassword());
		});

		//check all validation on registering
		$('form#regForm').submit(function(e) {
			// e.preventDefault();

			var email = $('.email').val();
			var mobile = $('.mobile').val();
			var uname = $('.uname').val();
			var pwd = $('.pwd').val();

			clearAlert();

			if (email == "" || email == null) {
				document.getElementById("email").scrollIntoView(false);
				return false;
			}

			if (mobile == "" || mobile == null || mobile.length < 10 || mobile.length > 10) {
				document.getElementById("mobile").scrollIntoView(false);
				$('.mobileerr').show();
				return false;
			} else {
				$('.mobileerr').hide();
			}

			if (uname == "" || uname == null) {
				document.getElementById("uname").scrollIntoView(false);
				return false;
			}

			if (pwd == "" || pwd == null || pwd.length < 6) {
				document.getElementById("pwd").scrollIntoView(false);
				$('.pwderr').show();
				return false;
			} else {
				$('.pwderr').hide();
			}


			$.ajax({
				beforeSend: function() {
					$('.registerbtn').attr('disabled', 'disabled').html('Processing...').css('cursor', 'not-allowed');
				}
			});
		});
	});
</script>