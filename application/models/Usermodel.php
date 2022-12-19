<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usermodel extends CI_Model
{
	public function login()
	{
		$uname = htmlentities($this->input->post('uname'));
		$pwd = $this->input->post('pwd');

		$user = $this->db->get_where('user', array('uname' => $uname))->row();
		if (!$user) {
			return false;
			exit();
		}
		if ($user->active == "0") {
			return "not_verified";
			exit();
		}
		if ($user->active == "2") {
			return "not_active";

			exit();
		}
		//verify passwords
		if (password_verify($pwd, $user->password)) {
			$userinfo = $this->db->get('user')->row();

			return $userinfo;
		} else {
			return false;
		}
	}

	public function login_get_key()
	{
		$uname = htmlentities($this->input->post('uname'));

		$user = $this->db->get_where('user', array('uname' => $uname))->row();
		if ($user->active == "0") {
			return $user->form_key;
			exit();
		}
	}

	public function register($admin, $iscmpy, $act_key, $form_key)
	{
		$data = array(
			'uname' => htmlentities($this->input->post('uname')),
			'email' => htmlentities($this->input->post('email')),
			'active' => "0",
			'act_key' => password_hash($act_key, PASSWORD_DEFAULT),
			'password' => password_hash($this->input->post('pwd'), PASSWORD_DEFAULT),
		);
		$this->db->insert('user', $data);
		$lastid = $this->db->insert_id();
		// 
		return TRUE;
	}

	public function check_verification($key)
	{
		$this->db->select('active,email,form_key,uname');
		$this->db->where(array('form_key' => $key));
		$query = $this->db->from('user');
		if (!$query) {
			return false;
		} else if ($query) {
			return $query->get()->row();
		}
	}

	public function emailverify($key)
	{
		$data = array(
			'sentcode' => htmlentities($this->input->post('sentcode')),
		);
		$info = $this->db->get_where('user', array('form_key' => $key))->row();
		$act_keyDB = password_verify($this->input->post('sentcode'), $info->act_key);
		if ($act_keyDB == 0) {
			return false;
			exit();
		} elseif ($act_keyDB == 1) {
			$this->db->set('active', '1');
			$this->db->where('form_key', $key);
			$this->db->update('user');

			$this->db->select('u.id,u.sadmin,u.admin,u.iscmpy,u.cmpyid,u.cmpy,u.uname,u.email,u.mobile,u.active,u.sub,u.website_form,u.form_key');
			$this->db->from('user u');
			$this->db->where('u.form_key', $key);
			$userinfo = $this->db->get()->row();
			return $userinfo;
		}
	}

	public function code_verify_update($act_key, $key)
	{
		$this->db->set('act_key', password_hash($act_key, PASSWORD_DEFAULT));
		$this->db->where('form_key', $key);
		$this->db->update("user");
		return true;
		exit;
	}

	public function get_info()
	{
		$query = $this->db->get_where('user', array('id' => $this->session->userdata('id')))->row();
		if (!$query) {
			return false;
			exit();
		} else {
			return $query;
		}
	}

	public function personal_edit()
	{
		$data = array(
			'fname' => htmlentities($this->input->post('fname')),
			'lname' => htmlentities($this->input->post('lname')),
			'email' => htmlentities($this->input->post('email')),
			'mobile' => htmlentities($this->input->post('mobile')),
			'gender' => htmlentities($this->input->post('gender')),
			'dob' => htmlentities($this->input->post('dob')),
		);
		$this->db->where('id', $this->session->userdata('id'));
		$this->db->update('user', $data);
		return TRUE;
	}

	public function check_pwd()
	{
		$c_pwd = $this->input->post('c_pwd');
		$n_pwd = $this->input->post('n_pwd');
		$query = $this->db->get_where('user', array('id' => $this->session->userdata('id')))->row();
		if (!$query) {
			return false;
			exit;
			//if user, crossheck password in DB with user input
		} else {
			if (password_verify($c_pwd, $query->password)) {
				//if crosscheck is true, hash and save the new password
				$this->db->set('password', password_hash($n_pwd, PASSWORD_DEFAULT));
				$this->db->where('id', $this->session->userdata('id'));
				$this->db->update('user');
				return true;
				exit;
			} else {
				return false;
				exit;
			}
		}
	}

	public function updateact_key($userid, $act_key, $email)
	{
		$hashkey = password_hash($act_key, PASSWORD_DEFAULT);
		$this->db->set('act_key', $hashkey);
		$this->db->where(array('id' => $userid, 'email' => $email));
		$q = $this->db->update('user');

		if ($q) {
			return true;
			exit;
		} else {
			return false;
			exit;
		}
	}

	public function verifyvcode($userid, $vecode)
	{
		$info = $this->db->get_where('user', array('id' => $userid))->row();
		if (password_verify($vecode, $info->act_key)) {
			return true;
			exit();
		} else {
			return false;
			exit();
		}
	}

	public function changepassword($userid, $newpwd)
	{
		$this->db->set('password', password_hash($newpwd, PASSWORD_DEFAULT));
		$this->db->where('id', $userid);
		$q = $this->db->update('user');

		if ($q) {
			return true;
			exit;
		} else {
			return false;
			exit;
		}
	}
}
