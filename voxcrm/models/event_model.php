<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	class Event_model extends CI_Model
	{
		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;
		const STATUS_DELETED = 2;
		const STATUS_UNPAID = 3;
		const STATUS_PAID_GREEN = 4;
		
		function events($eid = null)
		{
			if (empty($eid))
			{
				return $this->db->get('events')->result();
			}
			else
			{
				return $this->db->where('eid', $eid)->get('events')->row();
			}
		}
		
		function prepare_empty_event()
		{
			return (object)array(
			'eid' => '',
			'name' => '',
			'student_id' => '',
			'studio_id' => '',
			'product_id' => '',
			'description' => '',
			'status' => self::STATUS_INACTIVE,
			'created' => time(),
			);
		}
		
		function save($data)
		{
			$eid = 0;
			if(isset($data->eid))
			{
				$this->db->where('eid', $data->eid);
				$this->db->update('events', $data);
				$eid = $data->eid;
			}
			else
			{
				$this->db->insert('events', $data);
				$eid = $this->db->insert_id();
				
				// now creating schedules
				$this->load->model('schedule_model');
				foreach($data->schedules as $i => $schedule)
				{
					$schedule->eid = $eid;
					$data->schedules[$i]->cid = $this->schedule_model->save($schedule);
				}
			}
			
			return $eid;
		}
		
		function delete($eid, $description)
		{
			$this->db->where('eid', $eid);
			$this->db->update('events', array(
			'status' => self::STATUS_DELETED,
			'description' => $description
			));
			
			$this->db->where('eid', $eid);
			$this->db->update('schedules', array(
			'status' => Schedule_model::STATUS_DELETED
			));
		}
	}
	
	/* End of file event_model.php */
	/* Location: ./application/models/event_model.php */
