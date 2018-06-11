<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MOrders extends CI_Model {

    public function __construct() {

        parent::__construct();
        $this->load->database();
    }

    // Public method to obtain the orders
    public function obtener() {
        $query = $this->db->get('orders');

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    // Public method to obtain the orders by id
    public function obtenerById($table, $field, $value) {
        $query = $this->db->where($field, $value);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    // Public method to obtain the orders by ids
    public function obtenerByIds($table, $field1, $field2, $value1, $value2) {
        $query = $this->db->where($field1, $value1);
        $query = $this->db->where($field2, $value2);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }

    // Public method to obtain the specific field detail
    public function obtenerDetalle($table, $key, $id) {
        $query = $this->db->where($key, $id);
        $query = $this->db->get($table);

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
    }
    
    // Public method to obtain the specific detail of customizations
	public function getCustomizations($id_cart, $id_product_attribute, $id_product){
		
		$select = "customization.* , customized_data.*,customization_field_lang.*";
		$query = $this->db->select($select);
		$query = $this->db->from('customization');
		$query = $this->db->join('customized_data', 'customization.id_customization = customized_data.id_customization');
		$query = $this->db->join('customization_field_lang', 'customization_field_lang.id_customization_field = customized_data.index');
        $query = $this->db->where('id_cart', $id_cart);
        $query = $this->db->where('id_product_attribute', $id_product_attribute);
        $query = $this->db->where('id_product', $id_product);
        $query = $this->db->order_by('customization.id_customization');
        $query = $this->db->get();
        
        //~ echo $this->db->last_query();

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return $query->result();
		
	}
	
	// Public method to update a order  
    public function update($table, $datos) {
		
		$result = $this->db->where('id_order', $datos['id_order']);
		$result = $this->db->update($table, $datos);
		return $result;
        
    }
}

?>
