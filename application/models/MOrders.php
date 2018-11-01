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

    // Public method orden Payment
    public function order_payment($year, $month) {
        $this->db->select("*");
        $this->db->from('orders AS a');
        $this->db->join('order_payment AS b', 'a.reference = b.order_reference');
        if($year !="" && $month !=""){
            $this->db->where("YEAR(b.date_add)", $year);
            $this->db->where("MONTH(b.date_add)", $month);
        }
        $query = $this->db->get();
        return $query->result();
    }


    // Public method orden Payment
    public function order_detail_where($order_id) {
        $this->db->select("*");
        $this->db->from('order_detail AS a');
        $this->db->where("a.id_order", $order_id);
        $query = $this->db->get();
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

    // Public get detail customization value

    public function replace_text($search, $replace_text, $text_real){
        
        $pos = strrpos($text_real, $search);
        
        if($pos !== false){
            $text_real = substr_replace($text_real, $replace_text, $pos, strlen($search));
        }
        
        return $text_real;
    }

    public function get_customization($id_customization) {

        $obj = $this->db->query("SELECT a.value FROM customized_data AS a WHERE a.id_customization = $id_customization ORDER BY a.id_customization ASC LIMIT 5,6");
        return $obj->result();
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
    
    // Public method to obtain the specific detail of customizations
	public function getCustomization($id_cart, $id_product_attribute, $id_customization, $id_product){
		
		$select = "customization.* , customized_data.*,customization_field_lang.*";
		$query = $this->db->select($select);
		$query = $this->db->from('customization');
		$query = $this->db->join('customized_data', 'customization.id_customization = customized_data.id_customization');
		$query = $this->db->join('customization_field_lang', 'customization_field_lang.id_customization_field = customized_data.index');
        $query = $this->db->where('id_cart', $id_cart);
        $query = $this->db->where('id_product_attribute', $id_product_attribute);
        $query = $this->db->where('customization.id_customization', $id_customization);
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

    // Public method to update a order  
    public function save($table, $datos) {

		return $this->db->insert($table, $datos);

    }
}

?>
