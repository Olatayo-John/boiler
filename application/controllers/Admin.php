<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . "libraries/razorpay/Razorpay.php");

use Razorpay\Api\Api;

class Admin extends Admin_Controller
{
	public function index()
	{
		$this->is_admin();

		redirect('');
	}


	//activity logs
	public function logs()
	{
		$this->is_admin();

		$this->setTabUrl($mod = 'activity');

		$data['title'] = "activity logs";

		$data['activityLogs'] = $this->Adminmodel->get_activityLogs();

		$this->load->view('templates/header', $data);
		$this->load->view('admin/activityLogs');
		$this->load->view('templates/footer');
	}

	public function clear_activityLogs()
	{
		if ($this->ajax_is_admin() === true) {

			$res = $this->Adminmodel->clear_activityLogs();

			if ($res !== true) {
				$this->setFlashMsg('error', 'Error clearing data');

				$log = "Error clearing data - Activity Logs [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);
			} else {
				$this->setFlashMsg('success', 'Data cleared ');

				$log = "Data cleared - Activity Logs [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);
			}
		}

		redirect('activity');
	}

	//feedbacks from contact us form
	public function feedbacks()
	{
		$this->is_admin();

		$this->setTabUrl($mod = 'feedbacks');

		$data['title'] = "feedbacks";

		$data['feedbacks'] = $this->Adminmodel->get_feedbacks();

		$this->load->view('templates/header', $data);
		$this->load->view('admin/feedbacks', $data);
		$this->load->view('templates/footer');
	}

	public function clearfeedbacks()
	{
		if ($this->ajax_is_admin() === true) {

			$res = $this->Adminmodel->clear_feedbacks();

			if ($res !== true) {
				$log = "Error clearing data - Support [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', 'Error clearing data');
			} else {
				$log = "Data cleared - Support [ Username: " . $this->session->userdata('uname') .  " ]";
				$this->log_act($log);

				$this->setFlashMsg('success', 'Data cleared!');
			}
		}

		redirect('feedbacks');
	}

	public function support()
	{
		$data['title'] = "support";

		$this->setTabUrl($mod = 'support');

		$this->form_validation->set_rules('name', 'Full Name', 'required|trim|html_escape');
		$this->form_validation->set_rules('email', 'E-mail', 'required|trim|valid_email|html_escape');
		$this->form_validation->set_rules('msg', 'Message', 'required|trim|html_escape');

		if ($this->form_validation->run() === FALSE) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/contactus');
			$this->load->view('templates/footer');
		} else {
			$recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
			$userIp = $this->input->ip_address();
			$secret = $this->st->captcha_secret_key;

			$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptchaResponse . "&remoteip=" . $userIp;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			$status = json_decode($output, true);

			if ($status['success']) {
				$name = htmlentities($this->input->post('name'));
				$user_mail = htmlentities($this->input->post('email'));
				$bdy = htmlentities($this->input->post('msg'));

				$this->load->library('emailconfig');
				$mail_res = $this->emailconfig->support_mail($name, $user_mail, $bdy);

				if ($mail_res !== true) {
					$log = "Error sending mail - Contact Us [ Name: " . htmlentities($this->input->post('name')) . ", Email: " . htmlentities($this->input->post('email')) . ", MailError: " . $mail_res . " ]";
					$this->log_act($log);

					$this->setFlashMsg('error', 'Error sending your message');
				} else {
					$res = $this->Adminmodel->contact();

					$log = "Mail sent - Contact Us [ Name: " . htmlentities($this->input->post('name')) . ", Email: " . htmlentities($this->input->post('email')) . " ]";
					$this->log_act($log);

					$this->setFlashMsg('success', 'Message sent. We will get back to you as soon as possible');
				}
			} else {
				$this->setFlashMsg('error', 'Google Recaptcha Unsuccessfull');
			}

			redirect('support');
		}
	}

}
