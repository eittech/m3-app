<?php
//~ header('Content-Type: text/json; charset=utf-8;');
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");

class CProducts extends CI_Controller {
	
	public $table1;  // Tabla con las relaciones de los atributos del producto
	
	public $table2;  // Tabla con las relaciones de los atributos del producto

    public function __construct() {
        parent::__construct();
        
        /* 
         * Definición de tablas a incluir en el json
         * 
         * */
        $this->table1 = "product_attribute p_a";
        $this->table2 = "attribute_product a_p";
        
        // Load database
        $this->load->model('MProducts');
        
        $this->db->database = "psadmin";
        $this->db->dbprefix = "";
    }

    // Método para cargar los datos de una orden según el id
    public function index($product_id) {
		
		// Listado de telas relacionadas
		$cloth_product = $this->MProducts->obtenerTelas($this->table1, 'p_a.id_product', $product_id);
		
		// Listado de atributos relacionados
		$attribs_product = $this->MProducts->obtenerAtributos($this->table2, 'a_p.id_product', $product_id);
		
		// Variable que contendrá la nueva data armada que incluirá los atributos relacionados
		$new_data = array();
		
		// Construcción y ordenamiento de las telas
		if(count($cloth_product) > 0){
		
			$i = 0;
			// CICLO DE RECORRIDO DE LAS TELAS
			foreach($cloth_product as $attrib){
				
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
				sort($new_data[$clave]);
			}
			
		}
		
		// Construcción y ordenamiento de los demás atributos
		if(count($attribs_product) > 0){
		
			$j = 0;
			// CICLO DE RECORRIDO DE LOS ATRIBUTOS
			foreach($attribs_product as $attrib){
				
				$new_data[$attrib->public_name][] = $attrib->name;
				
				$j++;
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
				if($clave == 'Color'){  // Reordenamos sólo los colores
					sort($new_data[$clave]);
				}
			}
			
		}
		
		//~ Convertimos los datos resultantes a formato JSON
		$jsonencoded = json_encode($new_data, JSON_UNESCAPED_UNICODE);
		echo $jsonencoded;
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
        
        // Recorremos los atributos para construir las cadenas correspondientes con sus valores
        foreach($attributes as $key => $attr){
			
			foreach($attr as $attr){
				
				if($key == "Tela"){
					$telas .= $attr."-";
				}else if($key == "Talla"){
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
    
    
    // Generación de lista completa de productos con sus combinaciones
    function update_price_prestashop()
    {
		$list_products = "";
		
		// Listado de productos y sus respectivas combinaciones
		$attribs_product = $this->MProducts->obtenerCombinaciones();
		
		// Construimos la lista del cuerto si existen combinaciones
		if(count($attribs_product) > 0){
			
			$precio_costo;
			$i = 0;
			foreach($attribs_product as $combination){
				
				// Búsqueda del precio de cada combinación de producto
				$search_price = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				if($i == 0){
					
					$precio_costo = $search_price;
					
				}else{
					
					// Reasignamos el valor del precio costo si el precio calculado es menor que el anterior
					if($search_price < $precio_costo){
						$precio_costo = $search_price;
					}
					
				}
				
				$i++;
				
			}			
			
			foreach($attribs_product as $combination){
				
				$precio = 1;
				$precio_iva = 0;
				
				// Búsqueda del precio de cada combinación de producto
				$search_price = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				$precio = $search_price;
				
				$precio_iva = $precio * 1.12;
				
				$list_products .= "<tr>
									<td>".$combination->id_product."</td>
									<td>".$combination->product_name."</td>
									<td>".$combination->attribute_name."</td>
									<td>".$precio."</td>
									<td>".$precio_costo."</td>
									<td>".$precio_iva."</td>
								</tr>";
				
			}
			
		}
		
		// Imprimimos el listado resultante
		echo "
			<style>
				table, tr, th, td {
					border:1px solid; padding:10px;
				}
			</style>
			
			<table id='list_products'>
				<thead>
					<tr>
						<th>#</th>
						<th>Nombre Producto</th>
						<th>Combinación</th>
						<th>Precio</th>
						<th>Precio Costo</th>
						<th>Precio Iva</th>
					</tr>
				</thead>
				<tbody>
					".$list_products."
				</tbody>
			</table>";
	}
	
	
	// Cálculo del precio de un producto por combinación
	function calculate_price($id_product, $id_attribute, $id_combination){
		
		$price = 1;
		
		$sub_price1 = 0;
		
		$sub_price2 = 0;
		
		// Consultamos el monto del costo base
		$resultado1 = $this->MProducts->obtenerPrecio1();
		
		// Consultamos el monto del costo de materiales
		$resultado2 = $this->MProducts->obtenerPrecio2($id_combination);
		
		if(count($resultado1) > 0){
			$sub_price1 = $resultado1[0]->resultado;
		}
		
		if(count($resultado2) > 0){
			$sub_price2 = $resultado2[0]->resultado;
		}
		
		// Sumamos el monto del costo base más el monto del costo de materiales
		$price = $sub_price1 + $sub_price2;
		
		return $price;
		
	}

}
