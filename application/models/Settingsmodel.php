<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settingsmodel extends CI_Model
{
	public function get_settings()
	{
		$this->db->where(array("id" => '1'));
		$q = $this->db->get('settings');
		return $q->row();
	}

	public function update_settings($sData)
	{
		$this->db->where(array("id" => '1'));
		$this->db->update('settings', $sData);
		return true;
	}
}
