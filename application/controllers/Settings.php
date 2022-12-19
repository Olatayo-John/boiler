<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends Admin_Controller
{
	public function index()
	{
		$this->is_admin();

		$this->setTabUrl($mod = 'settings');

		$data['title'] = "settings";

		$data['settings'] = $this->Settingsmodel->get_settings();

		$this->load->view('templates/header', $data);
		$this->load->view('settings/index');
		$this->load->view('templates/footer');
	}

	//create a new user by a companyAdmin
	public function save()
	{
		$this->is_admin();

		$this->setTabUrl($mod = 'settings');

		$data['title'] = "settings";

		$this->form_validation->set_rules('site_name', 'Site Name', 'trim|required|html_escape');
		$this->form_validation->set_rules('site_title', 'Site Title', 'trim|html_escape');
		$this->form_validation->set_rules('site_desc', 'Site Description', 'trim|html_escape');
		$this->form_validation->set_rules('site_keywords', 'Site Keywords', 'trim|html_escape');
		$this->form_validation->set_rules('site_logo', 'Site Logo', 'trim|html_escape');
		$this->form_validation->set_rules('site_fav_icon', 'Site Fav Icon', 'trim|html_escape');
		$this->form_validation->set_rules('rz_test_key_id', 'Razorpay Key (TEST)', 'trim|html_escape');
		$this->form_validation->set_rules('rz_test_key_secret', 'Razorpay Secret Key (TEST)', 'trim|html_escape');
		$this->form_validation->set_rules('rz_live_key_id', 'Razorpay Key (LIVE)', 'trim|html_escape');
		$this->form_validation->set_rules('rz_live_key_secret', 'Razorpay Secret Key (LIVE)', 'trim|html_escape');
		$this->form_validation->set_rules('captcha_site_key', 'reCAPTCHA Site Key', 'trim|required|html_escape');
		$this->form_validation->set_rules('captcha_secret_key', 'reCAPTCHA Secret Key', 'trim|required|html_escape');
		$this->form_validation->set_rules('protocol', 'Protocol', 'trim|html_escape');
		$this->form_validation->set_rules('smtp_user', 'SMTP User', 'trim|html_escape');
		$this->form_validation->set_rules('smtp_pwd', 'SMTP Password', 'trim|html_escape');
		$this->form_validation->set_rules('smtp_host', 'SMTP Host', 'trim|html_escape');
		$this->form_validation->set_rules('smtp_port', 'SMTP Port', 'trim|html_escape');

		if ($this->form_validation->run() === FALSE) {
			$this->setFlashMsg('error', validation_errors());
			redirect('settings');
		} else {

			//site-logo
			if ($_FILES['site_logo']['name']) {

				$file_name = 'logo';

				$config['upload_path'] = './assets/images';
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size'] = '2048';
				$config['max_height'] = '3000';
				$config['max_width'] = '3000';
				$config['file_name'] = $file_name;
				$config['overwrite'] = true;
				$config['remove_spaces'] = false;

				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('site_logo')) {
					$upload_error = array('error' => $this->upload->display_errors());
					$this->setFlashMsg('error', $this->upload->display_errors());
					redirect('settings');
				} else {
					$logo_uploaded = $_FILES['site_logo']['name'];
					$logo_ext = htmlentities(strtolower(pathinfo($logo_uploaded, PATHINFO_EXTENSION)));
					$upload_data = array('upload_data' => $this->upload->data());
					$site_logo = $file_name . "." . $logo_ext;
				}
			} else {
				$site_logo = $this->input->post('current_site_logo');
			}

			//site-icon
			if ($_FILES['site_fav_icon']['name']) {

				$file_name = 'fav_icon';

				$config['upload_path'] = './assets/images';
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size'] = '2048';
				$config['max_height'] = '3000';
				$config['max_width'] = '3000';
				$config['file_name'] = $file_name;
				$config['overwrite'] = true;
				$config['remove_spaces'] = false;

				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('site_fav_icon')) {
					$upload_error = array('error' => $this->upload->display_errors());
					$this->setFlashMsg('error', $this->upload->display_errors());
					redirect('settings');
				} else {
					$logo_uploaded = $_FILES['site_fav_icon']['name'];
					$logo_ext = htmlentities(strtolower(pathinfo($logo_uploaded, PATHINFO_EXTENSION)));
					$upload_data = array('upload_data' => $this->upload->data());
					$site_fav_icon = $file_name . "." . $logo_ext;
				}
			} else {
				$site_fav_icon = $this->input->post('current_site_fav_icon');
			}

			//all validation done and checked
			$sData = array(
				'site_name' => htmlentities($this->input->post('site_name')),
				'site_title' => htmlentities($this->input->post('site_title')),
				'site_desc' => htmlentities($this->input->post('site_desc')),
				'site_keywords' => htmlentities($this->input->post('site_keywords')),
				'site_logo' => $site_logo,
				'site_fav_icon' => $site_fav_icon,
				'rz_test_key_id' => htmlentities($this->input->post('rz_test_key_id')),
				'rz_test_key_secret' => htmlentities($this->input->post('rz_test_key_secret')),
				'rz_live_key_id' => htmlentities($this->input->post('rz_live_key_id')),
				'rz_live_key_secret' => htmlentities($this->input->post('rz_live_key_secret')),
				'captcha_site_key' => htmlentities($this->input->post('captcha_site_key')),
				'captcha_secret_key' => htmlentities($this->input->post('captcha_secret_key')),
				'protocol' => htmlentities($this->input->post('protocol')),
				'smtp_user' => htmlentities($this->input->post('smtp_user')),
				'smtp_pwd' => htmlentities($this->input->post('smtp_pwd')),
				'smtp_host' => htmlentities($this->input->post('smtp_host')),
				'smtp_port' => htmlentities($this->input->post('smtp_port')),
			);
			// print_r($sData);
			// die;
			$res = $this->Settingsmodel->update_settings($sData);

			if ($res !== TRUE) {
				$log = "Error upating settings [ Username: " . htmlentities($this->input->post('uname')) . " ]";
				$this->log_act($log);

				$this->setFlashMsg('error', 'Error updating settings');
			} else {
				$log = "Updated settings [ Username: " . htmlentities($this->input->post('uname')) . " ]";
				$this->log_act($log);

				$this->setFlashMsg('success', 'Settings updated');
			}
		}

		redirect('settings');
	}
}
