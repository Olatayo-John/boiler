<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/settings.css'); ?>">

<div class="wrapper">
	<div class="bg-light-custom p-3">

		<form action="<?php echo base_url('save-settings') ?>" method="post" id="settingsForm" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token">

			<h5>General Settings</h5>
			<div class="site bg-light-custom p-3 mb-4">

				<div class="row">
					<div class="form-group col-md-6">
						<label>Site Name</label>
						<input type="text" name="site_name" class="form-control site_name" required value="<?php echo $settings->site_name ?>">
					</div>
					<div class="form-group col-md-6">
						<label>Site Title</label>
						<input type="text" name="site_title" class="form-control site_title" value="<?php echo $settings->site_title ?>">
					</div>
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>Site Description</label>
						<textarea name="site_desc" class="form-control site_desc" cols="30" rows="3"><?php echo $settings->site_desc ?></textarea>
					</div>
					<div class="form-group col-md-6">
						<label>Site Keywords</label>
						<textarea name="site_keywords" class="form-control site_keywords" cols="30" rows="3"><?php echo $settings->site_keywords ?></textarea>
					</div>
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>Site Logo</label>
						<span>Max size: 2MB</span>
						<input type="file" name="site_logo" class="form-control site_logo">
						<input type="hidden" name="current_site_logo" value="<?php echo $settings->site_logo ?>">
					</div>
					<div class="form-group col-md-6">
						<label>Site Fav. Icon</label>
						<span>Max size: 2MB</span>
						<input type="file" name="site_fav_icon" class="form-control site_fav_icon">
						<input type="hidden" name="current_site_fav_icon" value="<?php echo $settings->site_fav_icon ?>">
					</div>
				</div>

			</div>

			<h5>Payment</h5>
			<div class="payment bg-light-custom p-3 mb-4">

				<div class="row">
					<div class="form-group col-md-6">
						<label>Razorpay Key ID</label> <span>(TEST)</span>
						<input type="password" name="rz_test_key_id" class="form-control rz_test_key_id" value="<?php echo $settings->rz_test_key_id ?>">
					</div>
					<div class="form-group col-md-6">
						<label>Razorpay Secret Key</label> <span>(TEST)</span>
						<input type="password" name="rz_test_key_secret" class="form-control rz_test_key_secret" value="<?php echo $settings->rz_test_key_secret ?>">
					</div>
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>Razorpay Key ID</label>
						<input type="password" name="rz_live_key_id" class="form-control rz_live_key_id" value="<?php echo $settings->rz_live_key_id ?>">
					</div>
					<div class="form-group col-md-6">
						<label>Razorpay Secret Key</label>
						<input type="password" name="rz_live_key_secret" class="form-control rz_live_key_secret" value="<?php echo $settings->rz_live_key_secret ?>">
					</div>
				</div>

			</div>

			<h5>Google Captcha</h5>
			<div class="gCaptcha bg-light-custom p-3 mb-4">

				<div class="row">
					<div class="form-group col-md-6">
						<label>reCAPTCHA Site Key</label>
						<input type="password" name="captcha_site_key" class="form-control captcha_site_key" value="<?php echo $settings->captcha_site_key ?>">
					</div>
					<div class="form-group col-md-6">
						<label>reCAPTCHA Secret Key</label>
						<input type="password" name="captcha_secret_key" class="form-control captcha_secret_key" value="<?php echo $settings->captcha_secret_key ?>">
					</div>
				</div>

			</div>

			<h5>SMTP</h5>
			<div class="email_smtp bg-light-custom p-3 mb-4">

				<div class="form-group">
					<label>Protocol</label>
					<input type="text" name="protocol" class="form-control protocol" required value="<?php echo $settings->protocol ?>">
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>SMTP User</label>
						<input type="text" name="smtp_user" class="form-control smtp_user" value="<?php echo $settings->smtp_user ?>">
					</div>
					<div class="form-group col-md-6">
						<label>SMTP Password</label>
						<input type="password" name="smtp_pwd" class="form-control smtp_pwd" value="<?php echo $settings->smtp_pwd ?>">
					</div>
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>SMTP Host</label>
						<input type="text" name="smtp_host" class="form-control smtp_host" value="<?php echo $settings->smtp_host ?>">
					</div>
					<div class="form-group col-md-6">
						<label>SMTP Port</label>
						<input type="text" name="smtp_port" class="form-control smtp_port" value="<?php echo $settings->smtp_port ?>">
					</div>
				</div>

			</div>

			<hr>
			<div class="form-group text-right">
				<button class="btn text-light saveSettingsBtn" type="submit" style="background:#294a63">Update</button>
			</div>

		</form>
	</div>
</div>



<script type="text/javascript" src="<?php echo base_url('assets/js/settings.js'); ?>"></script>
<script type="text/javascript">
	var csrfName = $('.csrf_token').attr('name');
	var csrfHash = $('.csrf_token').val();

	$(document).ready(function() {

		//save settings
		$('#settingsForssm').submit(function(e) {
			// e.preventDefault();

			var site_name = $('.site_name').val();

			console.log(site_name);

			if (site_name == "" || site_name == null) {
				return false;
			}

			$.ajax({
				beforeSend: function() {
					$('.saveSettingsBtn').attr('disabled', 'disabled').html('Saving...').css('cursor', 'not-allowed');

					clearAlert();
				}
			});
		});

	});
</script>