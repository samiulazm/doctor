<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Queue_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function nextSerial($doctor_id, $chamber_id, $queue_date)
    {
        $this->db->select_max('serial_number');
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('chamber_id', $chamber_id);
        $this->db->where('queue_date', $queue_date);
        $row = $this->db->get('chamber_serial_queue')->row();
        $max = $row && $row->serial_number ? (int) $row->serial_number : 0;
        return $max + 1;
    }

    public function nextSortPosition($doctor_id, $chamber_id, $queue_date)
    {
        $this->db->select_max('sort_position');
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('chamber_id', $chamber_id);
        $this->db->where('queue_date', $queue_date);
        $row = $this->db->get('chamber_serial_queue')->row();
        $max = $row && $row->sort_position !== null ? (float) $row->sort_position : 0;
        return $max + 1.0;
    }

    public function insertQueueRow($data)
    {
        $this->db->insert('chamber_serial_queue', $data);
        return $this->db->insert_id();
    }

    public function getQueueForDay($doctor_id, $chamber_id, $queue_date)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('chamber_id', $chamber_id);
        $this->db->where('queue_date', $queue_date);
        $this->db->where_not_in('status', array('cancelled', 'done'));
        $this->db->order_by('sort_position', 'asc');
        return $this->db->get('chamber_serial_queue')->result();
    }

    public function getRow($id, $hospital_id)
    {
        $this->db->where('id', $id);
        $this->db->where('hospital_id', $hospital_id);
        return $this->db->get('chamber_serial_queue')->row();
    }

    public function getRowById($id)
    {
        $this->db->where('id', (int) $id);
        return $this->db->get('chamber_serial_queue')->row();
    }

    public function updateRow($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('chamber_serial_queue', $data);
    }

    public function setTicker($hospital_id, $doctor_id, $chamber_id, $queue_date, $queue_id)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('chamber_id', $chamber_id);
        $this->db->where('queue_date', $queue_date);
        $ex = $this->db->get('chamber_queue_ticker')->row();
        if ($ex) {
            $this->db->where('id', $ex->id);
            $this->db->update('chamber_queue_ticker', array(
                'current_queue_id' => $queue_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ));
            return;
        }
        $this->db->insert('chamber_queue_ticker', array(
            'hospital_id' => $hospital_id,
            'doctor_id' => $doctor_id,
            'chamber_id' => $chamber_id,
            'queue_date' => $queue_date,
            'current_queue_id' => $queue_id,
        ));
    }

    public function getTicker($doctor_id, $chamber_id, $queue_date)
    {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('chamber_id', $chamber_id);
        $this->db->where('queue_date', $queue_date);
        $t = $this->db->get('chamber_queue_ticker')->row();
        if (!$t || empty($t->current_queue_id)) {
            return null;
        }
        return $this->getRow($t->current_queue_id, $t->hospital_id);
    }
}
