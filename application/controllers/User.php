<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "libraries/razorpay/Razorpay.php");

use Razorpay\Api\Api;

class User extends User_Controller
{
	public function index()
	{
		if ($this->session->userdata('logged_in')) {
			redirect('');
		} else {
			redirect('login');
		}
	}

	//login function
	public function login()
	{
		if ($this->session->userdata('logged_in')) {
			redirect('/');
		}

		$this->setTabUrl($mod = 'login');

		$data['title'] = "login";

		$this->form_validation->set_rules('uname', 'Username', 'required|trim|html_escape');
		$this->form_validation->set_rules('pwd', 'Password', 'required|trim|html_escape');

		if ($this->form_validation->run() === FALSE) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/login');
			$this->load->view('templates/footer');
		} else {
			$validate = $this->Usermodel->login();

			if ($validate == FALSE) {
				$log = "Failed Login Attempt - Wrong Credentials [ Username: " . htmlentities($this->input->post('uname')) . " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', lang('wrong_pwd_uname'));
				redirect('user');
			}
			if ($validate == "not_active") {
				$log = "Failed Login Attempt - Account deactivated [ Username: " . htmlentities($this->input->post('uname')) . " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', lang('acct_deact'));
				redirect('/');
			}
			if ($validate == "not_verified") {
				$log = "Failed Login Attempt - Account unverified [ Username: " . htmlentities($this->input->post('uname')) . " ]";
				$this->log_act($log);

				$res_login = $this->Usermodel->login_get_key();
				if ($res_login) {
					$this->setFlashMsg('error', 'Your account is not verified');
					redirect('user/emailverify/' . $res_login);
				}
			}
			//if valid, create sessions via user details
			if ($validate) {
				$id = $validate->id;
				$admin = $validate->admin;
				$uname = $validate->uname;
				$email = $validate->email;
				$mobile = $validate->mobile;

				//sessionData
				$user_sess = array(
					'id' => $id,
					'uname' => $uname,
					'email' => $email,
					'mobile' => $mobile,
					'logged_in' => TRUE,
				);
				$this->session->set_userdata($user_sess);

				$this->Usermodel->user_latestact();

				$log = "Logged In [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				redirect('/');
			}
		}
	}

	//reister function
	public function register()
	{
		$data['title'] = "register";

		if ($this->session->userdata('logged_in')) {
			$this->setFlashMsg('error', 'Log out first.');
			redirect('/');
		}

		$this->setTabUrl($mod = 'register');

		//validate input forms
		$this->form_validation->set_rules('fname', 'First Name', 'trim|html_escape');
		$this->form_validation->set_rules('lname', 'Last Name', 'trim|html_escape');
		$this->form_validation->set_rules('email', 'E-mail', 'required|trim|valid_email|html_escape');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|exact_length[10]|html_escape');
		$this->form_validation->set_rules('uname', 'Username', 'required|trim|html_escape|is_unique[users.uname]', array('is_unique' => 'This username is taken'));
		$this->form_validation->set_rules('pwd', 'Password', 'required|trim|html_escape');
		$this->form_validation->set_rules('sms_quota', 'Quota', 'required|trim|html_escape');
		$this->form_validation->set_rules('email_quota', 'Quota', 'required|trim|html_escape');
		$this->form_validation->set_rules('whatsapp_quota', 'Quota', 'trim|html_escape');
		$this->form_validation->set_rules('web_quota', 'Quota', 'trim|html_escape');
		$this->form_validation->set_rules('cmpy', 'Company Name', 'trim|html_escape|is_unique[users.cmpy]', array('is_unique' => 'This Company already exist'));

		if ($this->form_validation->run() === false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/register');
			$this->load->view('templates/footer');
		} else {
			$uname = htmlentities($this->input->post('uname'));
			$uname_form = str_replace([" ", ".", ",", "?", "&"], "_", strtolower(substr($uname, 0, 5)));
			$pwd = $this->input->post('pwd');
			$email = htmlentities($this->input->post('email'));

			$act_key =  mt_rand(0, 1000000);
			$form_key =  $uname_form . mt_rand(0, 100000);
			$link = base_url() . "emailverify/" . $form_key;

			//try sending email before inserting to DB
			$this->load->library('emailconfig');
			$mail_res = $this->emailconfig->send_email_code($email, $uname, $act_key, $link);
			// $mail_res = false;

			if ($mail_res !== TRUE) {
				$log = "Error sending mail - User Registration [ Username: " . htmlentities($this->input->post('uname')) . ", Email: " . htmlentities($this->input->post('email')) . ", MailError: " . $mail_res . " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', 'Error sending mail');
				redirect('register');
				exit();
			} else {
				// for default users who are not a company
				$admin = $iscmpy = 0;

				if (isset($_POST['cmpychkb'])) {
					$admin = $iscmpy = 1;
				}

				//save in DB
				$db_res = $this->Usermodel->register($admin, $iscmpy, $act_key, $form_key);

				if ($db_res !== TRUE) {
					$log = "Error saving to Database - User Registration [ Username: " . htmlentities($this->input->post('uname')) . " ]";
					$this->log_act($log);

					$this->setFlashMsg('error', 'Error saving your details. Please try again');
					redirect('register');
					exit();
				} else {
					$log = "New user registration [ Username: " . htmlentities($this->input->post('uname')) . ", Email: " . htmlentities($this->input->post('email')) . " ]";
					$this->log_act($log);

					$this->setFlashMsg('success', 'Verification code sent to your mail.');
					redirect('emailverify/' . $form_key);
					exit();
				}
			}
		}
	}

	//email verification after registration
	public function emailverify($key)
	{
		$this->setTabUrl($mod = 'login');

		$data['title'] = "Email Verification";

		$check_res = $this->Usermodel->check_verification($key);
		if ($check_res == false) {
			$this->setFlashMsg('error', 'Wrong credentials');
			redirect('login');
		} else {
			$active = $check_res->active;
			if ($active == '1') {
				$this->setFlashMsg('success', 'Your account is verified.');
				redirect('login');
			}

			$this->form_validation->set_rules('sentcode', 'Verification Code', 'required|trim|html_escape');

			if ($this->form_validation->run() == false) {
				$data['key'] = $key;
				$data['email'] = $check_res->email;
				$this->load->view('templates/header', $data);
				$this->load->view('templates/emailverify', $data);
				$this->load->view('templates/footer');
			} else {
				$validate = $this->Usermodel->emailverify($key);

				if ($validate == false) {
					$log = "Invalid verfication code provided [ Username: " . $check_res->uname . " ]";
					$this->log_act($log);

					$this->setFlashMsg('error', 'Invalid code');
					redirect('emailverify/' . $key);
				} else {
					if ($validate->active !== "1") {
						$log = "Error verifying account [ Username: " . $check_res->uname  . " ]";
						$this->log_act($log);

						$this->setFlashMsg('error', 'Error verifying account ');
						redirect('emailverify/' . $key);
					} else if ($validate->active == "1") {
						$log = "Account verified [ Username: " . $check_res->uname  . " ]";
						$this->log_act($log);

						$this->setFlashMsg('success', 'Account verified');
						redirect('login');
					}
				}
			}
		}
	}

	//resend verification email
	public function resendemailverify($key)
	{
		$check_res = $this->Usermodel->check_verification($key);

		if ($check_res == false) {
			$this->setFlashMsg('error', 'Wrong credentials');
			redirect($_SERVER['HTTP_REFERRER']);
		} else {
			$active = $check_res->active;
			if ($active == '1') {
				$this->setFlashMsg('success', 'Your account is already verified and active.');
				redirect('login');
			} else {
				$res = $this->Usermodel->check_verification($key);

				$email = $res->email;
				$uname = $res->uname;
				$link = base_url() . "emailverify/" . $res->form_key;
				$act_key =  mt_rand(0, 1000000);

				$this->load->library('emailconfig');
				$mail_res = $this->emailconfig->send_email_code($email, $uname, $act_key, $link);

				if ($mail_res !== TRUE) {
					$log = "Error sending mail - Verification [ Username: " . $uname . ", Email: " . $email . ", MailError: " . $mail_res . " ]";
					$this->log_act($log);

					$this->setFlashMsg('error', 'Error sending mail');
					redirect($link);
				} else {
					$log = "Mail sent - Verification [ Username: " . $uname . ", Email: " . $email . " ]";
					$this->log_act($log);

					$this->Usermodel->code_verify_update($act_key, $key);

					$this->setFlashMsg('success', 'Verification mail sent');
					redirect($link);
				}
			}
		}
	}


	//
	public function account()
	{
		$this->checklogin();

		$this->setTabUrl($mod = 'account');

		$data['title'] = "account";

		$data['user_info'] = $this->Usermodel->get_info();

		$this->load->view('templates/header');
		$this->load->view('users/account_view', $data);
		$this->load->view('templates/footer');
	}

	public function account_edit()
	{
		$this->checklogin();

		$this->setTabUrl($mod = 'account');

		$this->form_validation->set_rules('fname', 'First Name', 'trim|html_escape');
		$this->form_validation->set_rules('lname', 'Last Name', 'trim|html_escape');
		$this->form_validation->set_rules('email', 'E-mail', 'required|trim|valid_email|html_escape');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|exact_length[10]|html_escape');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|html_escape');
		$this->form_validation->set_rules('dob', 'Date Of Birth', 'trim|html_escape');

		if ($this->form_validation->run() === FALSE) {
			$this->setFlashMsg('error', validation_errors());
		} else {
			$res = $this->Usermodel->personal_edit();
			if ($res !== TRUE) {
				$log = "Error updating profile [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', lang('update_failed'));
			} else {
				$log = "Profile Updated [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);

				$this->setFlashMsg('success', lang('profile_updated'));

				$this->session->set_userdata('email', htmlentities($this->input->post('email')));
				$this->session->set_userdata('mobile', htmlentities($this->input->post('mobile')));
			}
		}

		redirect('account');
	}


	public function password_update()
	{
		$this->checklogin();

		$this->form_validation->set_rules('c_pwd', 'Current Password', 'required|trim');
		$this->form_validation->set_rules('n_pwd', 'New Password', 'required|trim|min_length[6]');
		$this->form_validation->set_rules('rtn_pwd', 'Re-type Password', 'required|trim|min_length[6]|matches[n_pwd]');

		if ($this->form_validation->run() == false) {
			$this->setFlashMsg('error', validation_errors());
		} else {
			$pwd_res = $this->Usermodel->check_pwd();
			if ($pwd_res == false) {
				$log = "Error updating password [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', lang('incorrect_pwd_provided'));
			} else {
				$log = "Password updated [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				$this->setFlashMsg('success', lang('pwd_updated'));
			}
		}

		redirect('account');
	}

	//send verification code to user email
	public function resetpassword_vcode()
	{
		$email = htmlentities($_POST['useremail']);
		$act_key = htmlentities($_POST['vcode_init']);
		$userid = htmlentities($_POST['userid']);

		$this->load->library('emailconfig');
		$eres = $this->emailconfig->resetpassword_vcode($email, $act_key, $userid);

		if ($eres !== true) {
			$log = "Error sending mail - Verification [ Username: " . htmlentities($this->input->post('uname')) . ", Email: " . htmlentities($this->input->post('useremail')) . ", MailError: " . $eres . " ]";
			$this->log_act($log);

			$data['status'] = false;
			$data['msg'] = "Error sending mail";
		} else {
			$log = "Mail sent - Verification [ Username: " . $this->session->userdata('uname') . ", Email: " . htmlentities($this->input->post('useremail')) . " ]";
			$this->log_act($log);

			$res = $this->Usermodel->updateact_key($userid, $act_key, $email);
			if ($res === false) {
				$log = "Error saving to Database - Password Reset [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				$data['status'] = true;
				$data['msg'] = "Error saving to Database";
			} else {
				$data['status'] = true;
				$data['msg'] = "Mail sent";
			}
		}

		$data['token'] = $this->security->get_csrf_hash();
		echo json_encode($data);
	}

	//verify verification code
	public function verifyvcode()
	{
		$vecode = $_POST['vecode'];
		$userid = $_POST['userid'];

		$res = $this->Usermodel->verifyvcode($userid, $vecode);

		if ($res === false) {
			$log = "Invalid verfication code provided - Password Reset [ Username: " . $this->session->userdata('uname') . " ]";
			$this->log_act($log);

			$data['status'] = false;
			$data['msg'] = "Invalid verfication code provided";
		} else {
			$log = "Code verified - Password Reset [ Username: " . $this->session->userdata('uname') . " ]";
			$this->log_act($log);

			$data['status'] = true;
			$data['msg'] = "Code Verified";
		}

		$data['token'] = $this->security->get_csrf_hash();
		echo json_encode($data);
	}

	//after vcode is verified, change password
	public function changepassword()
	{
		$newpwd = $_POST['newpwd'];
		$userid = $_POST['userid'];

		$this->load->library('emailconfig');
		$eres = $this->emailconfig->resetpassword($userid, $newpwd, $user_name = $this->session->userdata('uname'));

		if ($eres === true) {

			$res = $this->Usermodel->changepassword($userid, $newpwd);

			if ($res === false) {
				$log = "Error updating password [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				$data['status'] = false;
				$data['msg'] = "Error updating password";
			} else {
				$log = "Password updated [ Username: " . $this->session->userdata('uname') . " ]";
				$this->log_act($log);

				$data['status'] = true;
				$data['msg'] = "Password updated!";
			}
		} else {
			$log = "Error sending mail - New Password [ Username: " . htmlentities($this->input->post('uname')) . ", Email: " . htmlentities($this->input->post('useremail')) . ", MailError: " . $eres . " ]";
			$this->log_act($log);

			$data['status'] = false;
			$data['msg'] = "Error sending mail";
		}

		$data['token'] = $this->security->get_csrf_hash();
		echo json_encode($data);
	}


	public function fof()
	{
		$data['title'] = "404 | Page Not Found";

		$this->load->view('templates/header', $data);
		$this->load->view('templates/fof');
		$this->load->view('templates/footer');
	}
}
