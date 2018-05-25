<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MProducts extends CI_Model {
	
	private $db_psadmin;

    public function __construct() {

        parent::__construct();
        $this->db_psadmin = $this->load->database('psadmin', TRUE);  // Indicamos que use la base de datos 'psadmin' en vez de 'm32018'
    }

    //Public method to obtain the orders
    public function obtener() {
        $query = $db_psadmin->get('attribute_product');

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    //Public method to obtain the orders by id
    public function obtenerById($table, $field, $value) {
		
		$select = "a_g_l.id_attribute_group, a_g_l.public_name, a_l.id_attribute, a_l.name";
		$query = $this->db_psadmin->select($select);
		$query = $this->db_psadmin->from($table);
		$query = $this->db_psadmin->join('attribute_group_lang a_g_l', 'a_p.id_attribute_group=a_g_l.id_attribute_group');
		$query = $this->db_psadmin->join('attribute_lang a_l', 'a_p.id_attribute_lang=a_l.id_attribute');
        $query = $this->db_psadmin->where($field, $value);
        $query = $this->db_psadmin->group_by(array('a_p.id_attribute_group', 'a_l.name'));
        $query = $this->db_psadmin->order_by('a_l.id_attribute');
        $query = $this->db_psadmin->get();
        
        //~ echo $db_psadmin->last_query();

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

}

?>
