<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CPrices extends CI_Controller {

	public function __construct() {
        parent::__construct();
        
		// Load database
        $this->load->model('MProducts');
		
    }
	
	// Generación de lista completa de productos con sus combinaciones
	public function index()
	{
		$this->load->view('base');
		$data['ident'] = "M3_Uniformes";
		$data['ident_sub'] = "Precios";
		
		// Listado de productos y sus respectivas combinaciones (Filtrado final)
		$data['listar'] = array();
		
		// Consulta de listado de productos y sus respectivas combinaciones
		$attribs_product = $this->MProducts->obtenerCombinaciones();
		
		// Filtrado de listado de productos y sus respectivas combinaciones
		$list_products = array();
		
		// Construimos la lista del cuerpo si existen combinaciones
		if(count($attribs_product) > 0){
			
			$precio_minimo;
			$i = 0;
			foreach($attribs_product as $combination){
				
				// Búsqueda del precio de cada combinación de producto
				list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				if($i == 0){
					
					$precio_minimo = $precio;
					
				}else{
					
					// Reasignamos el valor del precio costo si el precio calculado es menor que el anterior
					if($precio < $precio_minimo){
						$precio_minimo = $precio;
					}
					
				}
				
				$i++;
				
			}
			
			foreach($attribs_product as $combination){
				
				$precio = 1;
				$precio_iva = 0;
				
				// Búsqueda del precio de cada combinación de producto
				list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				$precio_costo = number_format($precio, 2, ',', '.');
				
				$list_products[] = array(
					"id_product" => $combination->id_product,
					"category_name_parent" => $combination->category_name_parent,
					"category_name" => $combination->category_name,
					"reference" => $combination->reference,
					"product_name" => $combination->product_name,
					"id_product_attribute" => $combination->id_product_attribute,
					"attribute_name" => $combination->attribute_name,
					"price_min" => number_format($precio_minimo, 2, ',', '.'),
					"costs_fixed" => number_format($costos_fijos, 2, ',', '.'),
					"costs_variable" => number_format($costos_variables, 2, ',', '.'),
					"price_cost" => $precio_costo,
					"price_wholesale" => number_format($precio*1.30, 2, ',', '.'),
					"price_retail" => number_format($precio*1.30*1.30, 2, ',', '.')
				);				
				
			}
			
		}
		
		// Transformamos el arreglo resultante en un arreglo de objetos y lo asignamos el arreglo principal
		$data['listar'] = json_decode(json_encode($list_products), false);
		
		// Filtro para cargar las vistas según el perfil del usuario logueado
		$perfil_id = $this->session->userdata('logged_in')['profile_id'];
		$perfil_folder = "";
		if($perfil_id == 1 || $perfil_id == 2){
			$perfil_folder = 'plataforma/';
		}else if($perfil_id == 3){
			$perfil_folder = 'inversor/';
		}else if($perfil_id == 4){
			$perfil_folder = 'asesor/';
		}else if($perfil_id == 5){
			$perfil_folder = 'gestor/';
		}
		$this->load->view($perfil_folder.'prices/prices', $data);
		$this->load->view('footer');
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
		
		// Consultamos el monto del costo variable correspondiente a la combinación
		$resultado3 = $this->MProducts->obtenerCostoVariable($id_combination);
		
		if(count($resultado1) > 0){
			if(count($resultado3) > 0){
				$sub_price1 = $resultado1[0]->resultado * $resultado3[0]->amount;
			}else{
				$sub_price1 = $resultado1[0]->resultado;
			}
		}
		
		if(count($resultado2) > 0){
			$sub_price2 = $resultado2[0]->resultado;
		}
		
		// Sumamos el monto del costo base más el monto del costo de materiales
		$price = $sub_price1 + $sub_price2;
		
		return array($sub_price1, $sub_price2, $price);
		
	}
	
}
