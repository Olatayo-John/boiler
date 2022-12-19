<!DOCTYPE html>
<html>

<head>
	<title>
		<?php echo (isset($title) && !empty($title)) ? ucwords($title) . ' - ' . $this->st->site_name : $this->st->site_name; ?>
	</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="title" content="<?php echo $this->st->site_title ?>">
	<meta name="description" content="<?php echo $this->st->site_desc ?>">
	<meta name="keywords" content="<?php echo $this->st->site_keywords ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/header.css'); ?>">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://kit.fontawesome.com/ca92620e44.js" crossorigin="anonymous"></script>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.5.0/gsap.min.js"></script>

	<link href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
	<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>

	<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/extensions/mobile/bootstrap-table-mobile.min.js"></script>

	<link href="https://unpkg.com/ionicons@4.5.5/dist/css/ionicons.min.css" rel="stylesheet">

	<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
	<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/extensions/export/bootstrap-table-export.min.js"></script>

	<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/extensions/print/bootstrap-table-print.min.js"></script>

	<link rel="icon" href="<?php echo base_url('assets/images/') . $this->st->site_fav_icon ?>">
	<script type="text/javascript">
		document.onreadystatechange = function() {
			if (document.readyState !== "complete") {
				$(".spinnerdiv").show();
			} else {
				$(".spinnerdiv").fadeOut();
			}
		};
	</script>
</head>

<body>
	<div class="spinnerdiv">
		<div class="spinner-border" style="color:cornflowerblue"></div>
	</div>

	<nav class="navbar navbar-expand-lg navbar-light fixed-top p-0">

		<button class="btn menubtn" onclick="opennav()">&#9776;</button>

		<div class="logoimg mr-auto m-1">
			<img src="<?php echo base_url("assets/images/") . $this->st->site_logo ?>" class="navbar-label">
		</div>

		<?php if ($this->session->userdata('logged_in')) : ?>
			<div class="navbar-brand text-uppercase font-weight-bolder" style="display: nonee;">
				<a href="<?php echo base_url('account') ?>" style="color:#fff">
					<?php echo ($this->session->userdata('uname') ? $this->session->userdata('uname') : 'Profile') ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="side-nav" id="side-nav">
			<?php $url = $this->session->userdata('url') ?>
			<ul>
				<?php if (!$this->session->userdata('logged_in')) : ?>
					<!-- login -->
					<li class="nav-item">
						<a href="<?php echo base_url('login') ?>" class="nav-link" style="<?php echo ($url == 'login' || $url == 'user') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fas fa-user"></i>
							<b>Login</b>
						</a>
					</li>

					<!-- register -->
					<li class="nav-item">
						<a href="<?php echo base_url('register') ?>" class="nav-link" style="<?php echo ($url == 'register') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fas fa-user-plus"></i>
							<b>Register</b>
						</a>
					</li>
				<?php endif; ?>

				<?php if ($this->session->userdata('logged_in')) : ?>
					<!-- myAccount -->
					<li class="nav-item">
						<a href="<?php echo base_url('account') ?>" class="nav-link" style="<?php echo ($url == 'account' || $url == 'account-edit') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fas fa-user"></i><b>Profile</b>
						</a>
					</li>
				<?php endif; ?>


				<?php if ($this->session->userdata('logged_in') && $this->session->userdata('sadmin') == "1") : ?>

					<!-- actvity logs -->
					<li class="nav-item">
						<a href="<?php echo base_url('activity') ?>" class="nav-link" style="<?php echo ($url == 'activity') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fas fa-clipboard-check"></i><b>Activity Log</b>
						</a>
					</li>

					<!-- feedbacksFromContactUs -->
					<li class="nav-item">
						<a href="<?php echo base_url('feedbacks') ?>" class="nav-link" style="<?php echo ($url == 'feedbacks') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fas fa-comment"></i><b>Feedbacks</b>
						</a>
					</li>

					<!-- settings -->
					<li class="nav-item">
						<a href="<?php echo base_url('settings') ?>" class="nav-link" style="<?php echo ($url == 'settings') ? 'background:white;color:#294a63' : '' ?>">
							<i class="fa-solid fa-gear"></i><b>Settings</b>
						</a>
					</li>
				<?php endif; ?>

				<!-- support -->
				<li class="nav-item">
					<a href="<?php echo base_url('support') ?>" class="nav-link" style="<?php echo ($url == 'support') ? 'background:white;color:#294a63' : '' ?>">
						<i class="fas fa-question-circle"></i><b>Support</b>
					</a>
				</li>

				<!-- logOUT -->
				<?php if ($this->session->userdata('logged_in')) : ?>
					<li class="nav-item logoutli">
						<a href="<?php echo base_url('logout') ?>" class="nav-link text-danger">
							<i class="fas fa-sign-out-alt"></i><b>Logout</b>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

	</nav>

	<div class="container">
		<!-- testing div -->
		<!-- <div class="alerterror alertWrapper">
			<strong>Test notification Lorem, ipsum dolor sit amet consectetur adipisicing elit. Consequatur, ratione repudiandae esse repellendus est expedita, quod aut at odio odit ipsam vel! Lorem, ipsum dolor sitss amet consectetur adipisicing elit. Consequatur, ratione repudiandae esse repellendus est expedita, quod aut at odio odit ipsam vel! Lorem, ipsum dolor sit amet consectetur adipisicing elit. Consequatur, ratione repudiandae esse repellendus est expedita, quod aut at odio odit ipsam vel!</strong>
		</div> -->

		<!-- ajax-failed -->
		<div class="alertWrapper ajax_alert_div ajax_err_div" style="padding:8px;display:none;z-index: 9999;">
			<strong class="ajax_res_err text-dark"></strong>
		</div>

		<!-- ajax-success -->
		<div class="alertWrapper ajax_alert_div ajax_succ_div" style="padding:8px;display:none;z-index: 9999;">
			<strong class="ajax_res_succ text-dark"></strong>
		</div>

		<!-- session-flashMsg-function -->
		<?php if ($this->session->userdata('FlashMsg')) : ?>
			<div class="alertWrapper alert<?php echo $this->session->userdata('FlashMsg')['status'] ?>">
				<strong><?php echo $this->session->userdata('FlashMsg')['msg'] ?></strong>
			</div>
		<?php endif; ?>

		<?php if (validation_errors()) : ?>
			<div class="alerterror alertWrapper">
				<strong><?php echo validation_errors(); ?></strong>
			</div>
		<?php endif; ?>
	</div>

	<div id="content">