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
		$attribs_product = $this->MProducts->obtenerAtributos($this->table1, 'a_p.id_product', $product_id);
		
		// Variable que contendrá la nueva data armada que incluirá los atributos relacionados
		$new_data = array();
		
		if(count($attribs_product) > 0){
		
			$i = 0;
			// CICLO DE RECORRIDO DE LOS ATRIBUTOS
			foreach($attribs_product as $attrib){
				
				$new_data[$attrib->public_name][] = $attrib->name;
				
				$i++;
			}
			
			/* Proceso de ordenamiento alfabético de los distintos atributos.
			 * Primero capturamos las claves del arreglo y armamos una lista de claves,
			 * luego usamos esa lista para realizar el ordenamiento por cada clave
			 * */
			$claves = array();
			foreach($new_data as $key => $valor){
				$claves[] = $key;
			}
			
			foreach($claves as $clave){
				if($clave != 'Talla'){  // No reordenamos las tallas
					sort($new_data[$clave]);
				}
			}
			
			//~ print_r($new_data);
			
			//~ Convertimos los datos resultantes a formato JSON
			$jsonencoded = json_encode($new_data, JSON_UNESCAPED_UNICODE);
			echo $jsonencoded;
			
		}
    }
    
	// Generación del reporte de un producto
    function pdf_catalogue($product_id)
    {
		// Consultamos los datos básicos del producto
		$basic_data = $this->MProducts->obtenerById('product p', 'p.id_product', $product_id);
		
        $data['product'] = $basic_data;
		
        // Consultamos los atributos del producto
		$get2 = file_get_contents(base_url()."products/".$product_id);
		$attributes = json_decode($get2, true);
        
        $telas = "";  // Costruiremos una cadena con las telas
        
        $tallas = "";  // Costruiremos una cadena con las tallas
        
        $variables = "";  // Costruiremos una cadena con las variables
        
        $combinaciones = "";  // Costruiremos una cadena con las combinaciones
        
        $extras = "";  // Costruiremos una cadena con los extras
        
        $colores = "";  // Costruiremos una cadena con los colores
        
        $otros = "";
        
        //~ print_r($data['product']);
        
        // Recorremos los atributos para construir las cadenas correspondientes con sus valores
        foreach($attributes as $key => $attr){
			
			foreach($attr as $attr){
				
				if($key == "Talla"){
					$tallas .= $attr."-";
				}else if($key == "Variable"){
					$variables .= $attr."-";
				}else if($key == "Combinación"){
					$combinaciones .= $attr."-";
				}else if($key == "Extra"){
					$extras .= $attr."-";
				}else if($key == "Color"){
					$colores .= $attr."-";
				}else{
					$otros .= $attr."-";
				}
				
			}
			
		}
		
		// Incluimos las cadenas en la data de contexto a enviar al formato
		$data['telas'] = substr($telas, 0, -1);
		$data['tallas'] = substr($tallas, 0, -1);
		$data['variables'] = substr($variables, 0, -1);
		$data['combinaciones'] = substr($combinaciones, 0, -1);
		$data['extras'] = substr($extras, 0, -1);
		$data['colores'] = substr($colores, 0, -1);
		$data['otros'] = substr($otros, 0, -1);
        
        $this->load->view('pdf/catalogue_report', $data);
    }

}
