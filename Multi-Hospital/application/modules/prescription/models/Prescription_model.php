<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Prescription_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function insertPrescription($data) {
        $data1 = array('hospital_id' => $this->session->userdata('hospital_id'));
        if ($this->db->field_exists('rx_content_version', 'prescription')) {
            $data1['rx_content_version'] = 1;
            $data1['rx_last_updated_at'] = time();
        }
        $data2 = array_merge($data, $data1);
        $this->db->insert('prescription', $data2);
    }

    function getPrescription() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('prescription');
        return $query->row();
    }

    function getPrescriptionByPatientId($patient_id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('patient', $patient_id);
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionByDoctorId($doctor_id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('doctor', $doctor_id);
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function updatePrescription($id, $data) {
        $this->db->where('id', $id);
        if ($this->db->field_exists('rx_content_version', 'prescription')) {
            $data['rx_last_updated_at'] = time();
            $this->db->set('rx_content_version', 'rx_content_version+1', false);
        }
        $this->db->update('prescription', $data);
    }

    function deletePrescription($id) {
        $this->db->where('id', $id);
        $this->db->delete('prescription');
    }
    
    function getPrescriptionWithoutSearch($order, $dir) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionBySearch($search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
                ->from('prescription')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getPrescriptionByLimit($limit, $start, $order, $dir) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionByLimitBySearch($limit, $start, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('prescription')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getPrescriptionByDoctor($doctor_id) {
        $this->db->order_by('id', 'desc');
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor_id);
        $query = $this->db->get('prescription');
        return $query->result();
    }
    
    function getPrescriptionByDoctorWithoutSearch($doctor_id, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor_id);
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionBySearchByDoctor($doctor, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->select('*')
                ->from('prescription')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('doctor', $doctor)
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    function getPrescriptionByLimitByDoctor($doctor, $limit, $start, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('doctor', $doctor);
        $this->db->limit($limit, $start);
        $query = $this->db->get('prescription');
        return $query->result();
    }

    function getPrescriptionByLimitBySearchByDoctor($doctor, $limit, $start, $search, $order, $dir) {
        if ($order != null) {
            $this->db->order_by($order, $dir);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $this->db->limit($limit, $start);
        $query = $this->db->select('*')
                ->from('prescription')
                ->where('hospital_id', $this->session->userdata('hospital_id'))
                ->where('doctor', $doctor)
                ->where("(id LIKE '%" . $search . "%' OR patientname LIKE '%" . $search . "%' OR doctorname LIKE '%" . $search . "%')", NULL, FALSE)
                ->get();
        ;
        return $query->result();
    }

    /**
     * Lightweight interaction hints based on optional medicine.interaction_rules_json.
     * JSON examples (array of rules):
     * [{"type":"avoid_generic_substring","values":["ibuprofen","ketorolac"],"severity":"major","message":"NSAID overlap"}]
     */
    function buildMedicineInteractionAlerts($medicine_ids)
    {
        if (!$this->db->field_exists('interaction_rules_json', 'medicine')) {
            return array();
        }
        $medicine_ids = array_values(array_unique(array_filter(array_map('intval', (array) $medicine_ids))));
        if (count($medicine_ids) < 2) {
            return array();
        }

        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where_in('id', $medicine_ids);
        $rows = $this->db->get('medicine')->result();
        $byId = array();
        foreach ($rows as $row) {
            $byId[(int) $row->id] = $row;
        }

        $alerts = array();
        $ids = array_keys($byId);
        $n = count($ids);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $a = $byId[$ids[$i]];
                $b = $byId[$ids[$j]];
                $pairAlerts = $this->_interactionAlertsForPair($a, $b);
                foreach ($pairAlerts as $alert) {
                    $alerts[] = $alert;
                }
            }
        }
        return $alerts;
    }

    private function _interactionAlertsForPair($medA, $medB)
    {
        $out = array();
        $hayB = strtolower(trim(($medB->generic ? $medB->generic : '') . ' ' . ($medB->name ? $medB->name : '')));
        $hayA = strtolower(trim(($medA->generic ? $medA->generic : '') . ' ' . ($medA->name ? $medA->name : '')));

        foreach (array($medA, $medB) as $idx => $source) {
            $other = ($idx === 0) ? $medB : $medA;
            $hayOther = ($idx === 0) ? $hayB : $hayA;
            if (empty($source->interaction_rules_json)) {
                continue;
            }
            $rules = json_decode($source->interaction_rules_json, true);
            if (!is_array($rules)) {
                continue;
            }
            foreach ($rules as $rule) {
                if (!is_array($rule) || empty($rule['type'])) {
                    continue;
                }
                if ($rule['type'] === 'avoid_generic_substring' && !empty($rule['values']) && is_array($rule['values'])) {
                    foreach ($rule['values'] as $needle) {
                        $needle = strtolower(trim((string) $needle));
                        if ($needle === '') {
                            continue;
                        }
                        if (strpos($hayOther, $needle) !== false) {
                            $out[] = array(
                                'severity' => !empty($rule['severity']) ? $rule['severity'] : 'moderate',
                                'message' => !empty($rule['message']) ? $rule['message'] : 'Potential interaction between ' . $source->name . ' and ' . $other->name,
                                'medicines' => array($source->name, $other->name),
                            );
                        }
                    }
                }
            }
        }
        return $out;
    }

}
