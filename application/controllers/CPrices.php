<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CPrices extends CI_Controller {

	public function __construct() {
        parent::__construct();
        
		// Load database
        $this->load->model('MProducts');
        $this->load->model('MPrices');
		
    }
	
	// Generación de lista completa de productos con sus combinaciones
	public function index()
	{
		$this->load->view('base');
		$data['ident'] = "M3_Uniformes";
		$data['ident_sub'] = "Precios";
		
		// Listado de categorías asociadas al id_parent 2
		$data['categories'] = $this->MPrices->categories_list();
		
		//~ // Listado de productos y sus respectivas combinaciones (Filtrado final)
		//~ $data['listar'] = array();
		//~ 
		//~ // Consulta de listado de productos y sus respectivas combinaciones
		//~ $attribs_product = $this->MProducts->obtenerCombinaciones();
		//~ 
		//~ // Filtrado de listado de productos y sus respectivas combinaciones
		//~ $list_products = array();
		//~ 
		//~ // Construimos la lista del cuerpo si existen combinaciones
		//~ if(count($attribs_product) > 0){
			//~ 
			//~ $precio_minimo;
			//~ $i = 0;
			//~ foreach($attribs_product as $combination){
				//~ 
				//~ // Búsqueda del precio de cada combinación de producto
				//~ list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				//~ 
				//~ if($i == 0){
					//~ 
					//~ $precio_minimo = $precio;
					//~ 
				//~ }else{
					//~ 
					//~ // Reasignamos el valor del precio costo si el precio calculado es menor que el anterior
					//~ if($precio < $precio_minimo){
						//~ $precio_minimo = $precio;
					//~ }
					//~ 
				//~ }
				//~ 
				//~ $i++;
				//~ 
			//~ }
			//~ 
			//~ foreach($attribs_product as $combination){
				//~ 
				//~ $precio = 1;
				//~ $precio_iva = 0;
				//~ 
				//~ // Búsqueda del precio de cada combinación de producto
				//~ list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				//~ 
				//~ $precio_costo = number_format($precio, 2, ',', '.');
				//~ 
				//~ $list_products[] = array(
					//~ "id_product" => $combination->id_product,
					//~ "category_name_parent" => $combination->category_name_parent,
					//~ "category_name" => $combination->category_name,
					//~ "reference" => $combination->reference,
					//~ "product_name" => $combination->product_name,
					//~ "id_product_attribute" => $combination->id_product_attribute,
					//~ "attribute_name" => $combination->attribute_name,
					//~ "price_min" => number_format($precio_minimo, 2, ',', '.'),
					//~ "costs_fixed" => number_format($costos_fijos, 2, ',', '.'),
					//~ "costs_variable" => number_format($costos_variables, 2, ',', '.'),
					//~ "price_cost" => $precio_costo,
					//~ "price_wholesale" => number_format($precio*1.30, 2, ',', '.'),
					//~ "price_retail" => number_format($precio*1.30*1.30, 2, ',', '.')
				//~ );				
				//~ 
			//~ }
			//~ 
		//~ }
		//~ 
		//~ // Transformamos el arreglo resultante en un arreglo de objetos y lo asignamos el arreglo principal
		//~ $data['listar'] = json_decode(json_encode($list_products), false);
		
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
	
/**
 * ------------------------------------------------------
 * Método alternativo para cargar los datos de la tabla de 
 * transacciones usando ajax.
 * ------------------------------------------------------
 * 
 * Este método permite construir un listado de precios adaptado 
 * a la solicitud realizada con ajax desde la vista por el plugin datatable.
 */
    public function ajax_prices()
	{
		// Listado de combinaciones de productos y sus respectivos precios
		$fetch_data = $this->MPrices->make_datatables();
		
		// Cálculo del precio mínimo
		$precio_minimo;
		$i = 0;
		foreach($fetch_data as $row){
			
			// Búsqueda del precio de cada combinación de producto
			list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($row->id_product, $row->id_attribute, $row->id_product_attribute);
			
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
		
		// Armado del nuevo listado
		$data = array();
		$i = 1;
		foreach($fetch_data as $row){
			
			$sub_array = array();
			
			$precio = 1;
			$precio_iva = 0;
			
			// Búsqueda del precio de cada combinación de producto
			list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($row->id_product, $row->id_attribute, $row->id_product_attribute);
			
			$precio_costo = number_format($precio, 2, ',', '.');
			
			$edit;
			
			// Validación de botón de edición
			if($this->session->userdata('logged_in')['profile_id'] == 1){
				$edit = "<a href='".base_url()."prices/edit/".$row->id_product."' title='".$this->lang->line('list_edit_prices')."'><i class='fa fa-edit fa-2x'></i></a>";
			}else{
				$edit = "<a ><i class='fa fa-ban fa-2x' style='color:#D33333;'></i></a>";
			}
			
			// Mostramos los datos ya filtrados
			$sub_array[] = $row->id_product;
			$sub_array[] = $row->category_name_parent;
			$sub_array[] = $row->category_name;
			$sub_array[] = $row->reference;
			$sub_array[] = $row->product_name;
			$sub_array[] = $row->id_product_attribute;
			$sub_array[] = $row->attribute_name;
			$sub_array[] = number_format($precio_minimo, 2, ',', '.');
			$sub_array[] = number_format($costos_fijos, 2, ',', '.');
			$sub_array[] = number_format($costos_variables, 2, ',', '.');
			$sub_array[] = $precio_costo;
			$sub_array[] = number_format($precio*1.30, 2, ',', '.');
			$sub_array[] = number_format($precio*1.30*1.30, 2, ',', '.');
			$sub_array[] = "<a target='_blank' href='".base_url()."products/catalogue/".$row->id_product."'><i class='fa fa-search fa-2x'></i></a>";
			$sub_array[] = $edit;
			$sub_array[] = "<a target='_blank' href='".base_url()."products/catalogue/".$row->id_product."'><i class='fa fa-search fa-2x'></i></a>";
			
			$data[] = $sub_array;
			
			$i++;
		}
		
		$output = array(
			"draw" => intval($_POST["draw"]),
			"recordsTotal" => $this->MPrices->get_all_data(),
			"recordsFiltered" => $this->MPrices->get_filtered_data(),
			"data" => $data
		);
		
		echo json_encode($output);
	}
	
	
/**
 * ------------------------------------------------------
 * Método para guardar los datos del listado de precios
 * en la tabla 'pricelist'.
 * ------------------------------------------------------
 * 
 * Este método permite construir un listado de precios adaptado 
 * a la solicitud realizada con ajax desde la vista por el plugin datatable.
 */
    public function save_prices()
	{
		// Id de categoría mediante post
		$id_category = $this->input->post('id_category');
		
		// Consulta de listado de productos y sus respectivas combinaciones
		$attribs_product = $this->MProducts->obtenerCombinacionesByCategory($id_category);
		
		// Para contar errores
		$errors = 0;
		
		// Para contar registros
		$records = 0;
		
		// Construimos la lista del cuerpo si existen combinaciones
		if(count($attribs_product) > 0){
			
			// Cálculo del precio mínimo
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
			
			// Construcción del número identificador del listado
			$list_number = $this->MPrices->next_number_list();
			
			if(count($list_number) > 0){
				$list_number = $list_number[0]->list_number+1;
			}else{
				$list_number = 1;
			}
			
			$j = 1;
			foreach($attribs_product as $combination){
				
				$precio = 1;
				$precio_iva = 0;
				
				// Búsqueda del precio de cada combinación de producto
				list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				$precio_costo = $precio;
				
				$combination_price = array(
					"list_number" => $list_number,
					"list_type" => "".$id_category,
					"date" => date('Y-m-d H:i:s'),
					"position" => $j,
					"category" => $combination->category_name_parent,
					"subcategory" => $combination->category_name,
					"reference" => $combination->reference,
					"product" => $combination->product_name,
					"id_combination" => $combination->id_product_attribute,
					"material" => $combination->attribute_name,
					"price_minimal" => $precio_minimo,
					"price_cost" => $precio_costo,
					"price_wholesaler" => $precio*1.30,
					"price_retail" => $precio*1.30*1.30
				);
				
				// Si el prodcuto de la combinación está activo entonces la podemos guardar
				if($combination->product_status == 1){
					
					if(!$this->MPrices->insert($combination_price)){
						$errors += 1;
					}else{
						$records += 1;
					}
					
				}
				
				$j++;			
				
			}
			
		}
		
		// Impresión de mensaje en formato json para validar con jquery
		if($errors > 0){
			
			echo '{"response":"error"}';
			
		}else{
			
			if($records > 0){
				echo '{"response":"Se ha generado la lista '.$list_number.' con '.$records.' registros"}';
			}else{
				echo '{"response":"La categoría seleccionada no produjo registros"}';
			}
			
		}
		
	}
	
}
