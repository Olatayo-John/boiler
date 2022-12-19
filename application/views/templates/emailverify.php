<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/email_verify.css'); ?>">

<div class="wrapper">
	<form action="<?php echo base_url('verifyemail/' . $key); ?>" method="POST" id="emailVerifyForm" class="bg-light-custom p-3">
		<input type="hidden" class="csrf_token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

		<div class="form-group">
			<label>
				<span>* </span>
				<span>Enter the verification code sent to your mail</span>
				<span>(<?php echo $email ?>)</span>
			</label>
			<input type="text" name="sentcode" class="form-control sentcode" style="border-radius: 0" autofocus required>
			<div class="err codeerr">Invalid code length</div>
		</div>

		<div class="form-group verifybtndiv">
			<button class="btn btn-block text-light verifycodebtn" type="submit" style="background:#294a63">Verify</button>
		</div>

		<div class="text-right font-weight-bolder rsendcodediv">
			<div class="spinner-border rsend_spinner" style="width:20px;height:20px;top:unset;left:unset;display:none;">
				<span class="sr-only">Loading...</span>
			</div>
			<a href="<?php echo base_url('user/resendemailverify/' . $key); ?>" class="text-danger rsendcodebtn" id="rsendcodebtn">
				<i class="fas fa-redo-alt mr-2"></i>Resend code?</a>
		</div>
	</form>
</div>


<script>
	$(document).ready(function() {
		$('form#emailVerifyForm').submit(function(e) {
			//e.preventDefault();

			$.ajax({
				beforeSend: function() {
					$('button.verifycodebtn').addClass('bg-danger').html('Verifying...').attr('disabled', 'disabled').css({
						'cursor': 'not-allowed',
					})
				}
			});
		});

		$(document).on("click", 'a.rsendcodebtn', function() {
			//e.preventDefault();
			$.ajax({
				beforeSend: function() {
					$('#rsendcodebtn').html('Sending...').attr('disabled', 'disabled').css({
						'cursor': 'not-allowed',
						'visibility': 'hidden'
					});

					$(".rsend_spinner").fadeIn();
				}
			});
		});
	})
</script>