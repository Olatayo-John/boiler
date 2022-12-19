<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adminmodel extends CI_Model
{

	public function get_activityLogs()
	{
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('activity');
		return $query;
	}

	public function clear_activityLogs()
	{
		$this->db->truncate('activity');
		return true;
	}

	public function get_feedbacks()
	{
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('contact');
		return $query;
	}

	public function clear_feedbacks()
	{
		$this->db->truncate('contact');
		return true;
	}

	public function contact()
	{
		$data = array(
			'name' => htmlentities($this->input->post('name')),
			'user_mail' => htmlentities($this->input->post('email')),
			'bdy' => htmlentities($this->input->post('msg')),
		);
		$this->db->insert('contact', $data);
		return true;
	}
}
