<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MOrders extends CI_Model {

    public function __construct() {

        parent::__construct();
        $this->load->database();
    }

    //Public method to obtain the orders
    public function obtener() {
        $query = $this->db->get('orders');

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    //Public method to obtain the orders by id
    public function obtenerById($table, $field, $value) {
        $query = $this->db->where($field, $value);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    //Public method to obtain the orders by ids
    public function obtenerByIds($table, $field1, $field2, $value1, $value2) {
        $query = $this->db->where($field1, $value1);
        $query = $this->db->where($field2, $value2);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    //Public method to obtain the specific field detail
    public function obtenerDetalle($table, $key, $id) {
        $query = $this->db->where($key, $id);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

}

?>
