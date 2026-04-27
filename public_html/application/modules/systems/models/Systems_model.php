<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Systems_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    function getDoctor() {
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorBySearch($search) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('doctor')
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('phone', $search)
                ->or_like('address', $search)
                ->or_like('email', $search)
                ->or_like('department', $search)
                ->group_end()
                ->get();
        return $query->result();
    }

    function getDoctorByLimit($limit, $start) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('doctor');
        return $query->result();
    }

    function getDoctorByLimitBySearch($limit, $start, $search) {
        $this->db->like('id', $search);
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('doctor')
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('phone', $search)
                ->or_like('address', $search)
                ->or_like('email', $search)
                ->or_like('department', $search)
                ->group_end()
                ->get();

        return $query->result();
    }
    
    function getPatient() {
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('patient');
        return $query->result();
    }

    function getPatientBySearch($search) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('patient')
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('phone', $search)
                ->or_like('address', $search)
                ->group_end()
                ->get();
        ;
        return $query->result();
    }

    function getPatientByLimit($limit, $start) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get('patient');
        return $query->result();
    }

    function getPatientByLimitBySearch($limit, $start, $search) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('patient')
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('phone', $search)
                ->or_like('address', $search)
                ->group_end()
                ->get();
        ;
        return $query->result();
    }
}

