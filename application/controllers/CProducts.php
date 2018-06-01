<?php
//~ header('Content-Type: text/json; charset=utf-8;');
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");

class CProducts extends CI_Controller {
	
	public $table1;  // Tabla con las relaciones de los atributos del producto

    public function __construct() {
        parent::__construct();
        
        /* 
         * Definición de tablas a incluir en el json
         * 
         * */
        $this->table1 = "attribute_product a_p";
        
        // Load database
        $this->load->model('MProducts');
        
        $this->db->database = "psadmin";
        $this->db->dbprefix = "";
    }

    // Método para cargar los datos de una orden según el id
    public function index($product_id) {
		
		// Listado de atributos relacionados
		$attribs_product = $this->MProducts->obtenerById($this->table1, 'a_p.id_product', $product_id);
		
		// Variable que contendrá la nueva data armada que incluirá los atributos relacionados
		$new_data = array();
		
		if(count($attribs_product) > 0){
		
			$i = 0;
			// CICLO DE RECORRIDO DE LOS ATRIBUTOS
			foreach($attribs_product as $attrib){
				
				//~ echo $attrib->id_attribute_group." - ".$attrib->public_name." - ".$attrib->name;
				//~ 
				//~ echo "<br>";
				
				$new_data[$attrib->public_name][] = $attrib->name;
				
				$i++;
			}
			
			//~ Convertimos los datos resultantes a formato JSON
			$jsonencoded = json_encode($new_data, JSON_UNESCAPED_UNICODE);
			echo $jsonencoded;
			
		}
    }

}
