<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Prescription_version_and_medicine_interactions extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('prescription') && !$this->db->field_exists('rx_content_version', 'prescription')) {
            $this->load->dbforge();
            $this->dbforge->add_column(
                'prescription',
                array(
                    'rx_content_version' => array(
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'rx_last_updated_at' => array(
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'null' => true,
                    ),
                )
            );
        }

        if ($this->db->table_exists('medicine') && !$this->db->field_exists('interaction_rules_json', 'medicine')) {
            $this->load->dbforge();
            $this->dbforge->add_column(
                'medicine',
                array(
                    'interaction_rules_json' => array(
                        'type' => 'TEXT',
                        'null' => true,
                    ),
                )
            );
        }
    }

    public function down()
    {
        $this->load->dbforge();
        if ($this->db->table_exists('prescription') && $this->db->field_exists('rx_content_version', 'prescription')) {
            $this->dbforge->drop_column('prescription', 'rx_content_version');
        }
        if ($this->db->table_exists('prescription') && $this->db->field_exists('rx_last_updated_at', 'prescription')) {
            $this->dbforge->drop_column('prescription', 'rx_last_updated_at');
        }
        if ($this->db->table_exists('medicine') && $this->db->field_exists('interaction_rules_json', 'medicine')) {
            $this->dbforge->drop_column('medicine', 'interaction_rules_json');
        }
    }
}
