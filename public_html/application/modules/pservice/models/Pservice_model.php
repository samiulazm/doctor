<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pservice_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertPservice($data) {
       
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        $data2 = array_merge($data, $data1);
        $this->db->insert('pservice', $data2);
    }

    function getPservice() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('pservice');
        return $query->result();
    }

    function getPserviceById($id) {
       
        $this->db->where('id', $id);
        $query = $this->db->get('pservice');
        return $query->row();
    }

    function updatePservice($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('pservice', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('pservice');
    }

    function updateIonUser($username, $email, $password, $ion_user_id) {
        $uptade_ion_user = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $this->db->where('id', $ion_user_id);
        $this->db->update('users', $uptade_ion_user);
    }

    function getPserviceBySearch($search) {
        $this->db->order_by('id', 'desc');
        $query = $this->db->select('*')
                ->from('pservice')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('alpha_code', $search)
                ->or_like('code', $search)
                ->group_end()
                ->get();
        return $query->result();
    }

    function getPserviceByLimit($limit, $start) {
       
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('pservice');
        return $query->result();
    }

    function getPserviceByLimitBySearch($limit, $start, $search) {
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('pservice')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->group_start()
                ->like('id', $search)
                ->or_like('name', $search)
                ->or_like('alpha_code', $search)
                ->or_like('code', $search)
                ->group_end()
                ->get();
        return $query->result();
    }

    function getPserviceByActive() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('active', "1");
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('pservice');
        return $query->result();
    }

}
