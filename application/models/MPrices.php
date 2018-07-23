<?php
/**
 * MPrices class than extends of CI_Model
 *
 * An class than search data in the table 'product' and their associated tables.
 * 
 * Se encarga de realizar las consultas CRUD y las envía al controlador 'CPrices' principalmente.
 * 
 * @author	@jsolorzano18 (twitter)
 */

defined('BASEPATH') OR exit('No direct script access allowed');


class MPrices extends CI_Model {
/**
 * Tabla principal de consulta
 *
 * @var	array
 *
 */
	var $table = "product p";
	
/**
 * Campos a seleccionar
 *
 * @var	array
 *
 */
	var $select_column = array(
		"p.id_product", 
		"p.reference", 
		"c.position as category_position", 
		"c_l.id_category", 
		"c_l_parent.name as category_name_parent", 
		"c_l.name as category_name",
		"c_p.position as category_position_product", 
		"p_l.name as product_name", 
		"a_l.id_attribute", 
		"a_l.name as attribute_name", 
		"p_a.id_product_attribute"
	);

/**
 * Campos permitidos para ordenamiento
 *
 * @var	array
 *
 */
	var $order_column = array(
		"p.id_product",
		"c_l_parent.name", 
		"c_l.name", 
		"p.reference",
		"p_l.name",
		"a_l.id_attribute",
		"a_l.name"
	);

/**
 * Initialization class
 *
 * Loads the database connection.
 *
 */
    public function __construct() {
       
        parent::__construct();
        $this->load->database();
    }

/**
 * ------------------------------------------------------
 * MÉTODOS PARA RETORNO DE DATOS REQUERIDOS POR DATATABLE
 * ------------------------------------------------------
 */
 
/*
 * ------------------------------------------------------
 *  Método público para construir la consulta solicitada mediante ajax
 * ------------------------------------------------------
 * 
 * Se utilizan los atributos de la clase referentes a la tabla y los campos.
 *
 * Nota: Luego de la validación del perfil se valida si se
 * envió una búsqueda y si ésta no está vacía, de ser así,
 * se realiza un filtro para traducir las búsquedas en español
 * referentes a los campos 'type' y 'status'. Por último se aplica el 
 * ordenamiento solicitado.
 *
 */
    public function make_query() {
		
        $this->db->select($this->select_column);
        $this->db->distinct();
		$this->db->from($this->table);
		$this->db->join('category_product c_p', 'c_p.id_product=p.id_product');
		$this->db->join('category c', 'c.id_category=p.id_category_default');
		$this->db->join('category_lang c_l', 'c_l.id_category=c.id_category');
		$this->db->join('category_lang c_l_parent', 'c_l_parent.id_category=c.id_parent');
		$this->db->join('product_lang p_l', 'p_l.id_product=p.id_product');
		$this->db->join('product_attribute p_a', 'p_a.id_product=p.id_product');
		$this->db->join('product_attribute_combination p_a_c', 'p_a_c.id_product_attribute=p_a.id_product_attribute');
		$this->db->join('attribute a', 'a.id_attribute=p_a_c.id_attribute');
		$this->db->join('attribute_group_lang a_g_l', 'a_g_l.id_attribute_group=a.id_attribute_group');
		$this->db->join('attribute_lang a_l', 'a_l.id_attribute=p_a_c.id_attribute');
		$this->db->where('c_l.id_lang', 1);
		$this->db->where('p_l.id_lang', 1);
        $this->db->group_by(array('p_l.name', 'a_l.id_attribute'));
        
        // Filtros de búsqueda con ajax
		if(isset($_POST["search"]["value"]) && $_POST["search"]["value"] != ""){
			$condicionales_like = "(p.id_product LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "c_l_parent.name LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "c_l.name LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "p.reference LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "p_l.name LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "a_l.id_attribute LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "a_l.name LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "c_l_parent.name LIKE '%".$_POST["search"]["value"]."%' OR ";
			$condicionales_like .= "c_l.name LIKE '%".$_POST["search"]["value"]."%')";
			$this->db->where($condicionales_like);
		}
		
		// Ordenamiento con ajax
		if(isset($_POST["order"])){
			$this->db->order_by($this->order_column[$_POST["order"]["0"]["column"]], $_POST["order"]["0"]["dir"]);
		}else{
			$this->db->order_by('c.position, c_l.id_category, c_p.position, a.position');
		}
		
    }

/**
 * ------------------------------------------------------
 * Método público para ejecutar la consulta construida arriba
 * y aplicar los límites solicitados.
 * ------------------------------------------------------
 */
    public function make_datatables(){
		
		$this->make_query();
		if($_POST["length"] != -1){
			$this->db->limit($_POST["length"], $_POST["start"]);
		}
		$query = $this->db->get();
		return $query->result();
				
	}

/**
 * ------------------------------------------------------
 * Método público para obtener el número de registros 
 * resultantes de make_query().
 * ------------------------------------------------------
 */
	public function get_filtered_data(){
		
		$this->make_query();
		$query = $this->db->get();
		return $query->num_rows();
		
	}

/**
 * ------------------------------------------------------
 * Método público para obtener el número total de registros 
 * de transacciones del usuario.
 * ------------------------------------------------------
 */
	public function get_all_data(){
		
		$this->db->select($this->select_column);
		$this->db->from($this->table);
		$this->db->join('category_product c_p', 'c_p.id_product=p.id_product');
		$this->db->join('category c', 'c.id_category=p.id_category_default');
		$this->db->join('category_lang c_l', 'c_l.id_category=c.id_category');
		$this->db->join('category_lang c_l_parent', 'c_l_parent.id_category=c.id_parent');
		$this->db->join('product_lang p_l', 'p_l.id_product=p.id_product');
		$this->db->join('product_attribute p_a', 'p_a.id_product=p.id_product');
		$this->db->join('product_attribute_combination p_a_c', 'p_a_c.id_product_attribute=p_a.id_product_attribute');
		$this->db->join('attribute a', 'a.id_attribute=p_a_c.id_attribute');
		$this->db->join('attribute_group_lang a_g_l', 'a_g_l.id_attribute_group=a.id_attribute_group');
		$this->db->join('attribute_lang a_l', 'a_l.id_attribute=p_a_c.id_attribute');
		$this->db->where('c_l.id_lang', 1);
		$this->db->where('p_l.id_lang', 1);
        $this->db->group_by(array('p_l.name', 'a_l.id_attribute'));
        
		return $this->db->count_all_results();
		
	}
}
?>
