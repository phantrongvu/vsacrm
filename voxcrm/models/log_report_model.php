<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log_report_model extends CI_Model
{
	function insertLogData($data)
	{
		$this->db->insert('log_reports', $data);
		return true;
	}
	function updateLogData($data,$date)
	{
		$this->db->where("log_date = '$date'");
		$this->db->update('log_reports', $data);
		return true;
	}
	function checkLogData($date)
	{
		$this->db->where("log_date = '$date'");		
		return $this->db->get('log_reports')->num_rows();
	}
	function selectLogData()
	{
		$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
  		$finish = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');
		$this->db->where("log_date between '$start' and '$finish'");		
		return $this->db->get('log_reports')->result();
	}
}

/* End of file log_report_model.php */
/* Location: ./application/models/log_report_model.php */
