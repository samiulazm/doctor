<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Insurance_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertInsurance($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('insurance_company', $data2);
    }

    function getInsurance() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('insurance_company');
        return $query->result();
    }

    function getInsuranceByName($name) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('name', $name);
        $query = $this->db->get('insurance_company');
        return $query->row();
    }

    function getInsuranceById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('insurance_company');
        return $query->row();
    }

    function updateInsurance($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('insurance_company', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('insurance_company');
    }


   


    function getInsuranceCompanyBySearch($search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
                ->from('insurance_company')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('description', $search)
                ->group_end()
                ->get();
        return $query->result();
    }
    function getInsuranceCompanyWithoutSearch($order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('insurance_company');
        return $query->result();
    }
    function getInsuranceCompanyByLimitBySearch($limit, $start, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('insurance_company')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('description', $search)
                ->group_end()
                ->get();

        return $query->result();
    }
    function getInsuranceCompanyByLimit($limit, $start, $order, $dir) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('insurance_company');
        return $query->result();
    }



}
