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
		
		// Construimos la lista del cuerpo si existen combinaciones
		if(count($attribs_product) > 0){
			
			// Generamos la lista de precios mínimos de cada producto
			$minimum_prices = $this->minimum_prices($attribs_product);
			
			foreach($attribs_product as $combination){
				
				$precio = 1;
				$precio_iva = 0;
				
				// Búsqueda del precio de cada combinación de producto
				list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				$precio_costo = number_format($precio, 2, ',', '.');
				
				$list_products .= "<tr>
									<td>".$combination->id_product."</td>
									<td>".$combination->category_name_parent."</td>
									<td>".$combination->category_name."</td>
									<td>".$combination->reference."</td>
									<td>".$combination->product_name."</td>
									<td class='id-combination'>".$combination->id_product_attribute."</td>
									<td>".$combination->attribute_name."</td>
									<td>".number_format($minimum_prices[$combination->id_product], 2, ',', '.')."</td>
									<td>".number_format($costos_fijos, 2, ',', '.')."</td>
									<td>".number_format($costos_variables, 2, ',', '.')."</td>
									<td>".$precio_costo."</td>
									<td>".number_format($precio*1.30, 2, ',', '.')."</td>
									<td>".number_format(($precio*1.30)*1.12, 2, ',', '.')."</td>
									<td>".number_format($precio*1.30*1.30, 2, ',', '.')."</td>
									<td>".number_format(($precio*1.30*1.30)*1.12, 2, ',', '.')."</td>
								</tr>";
				
			}
			
		}
		
		// Imprimimos el listado resultante
		echo "
		
			<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
			
			<style>
				table, tr, th, td {
					border:1px solid; padding:10px;
				}
				.id-combination {
					text-align: center;
				}
			</style>
			
			<table id='list_products'>
				<thead>
					<tr>
						<th>#</th>
						<th>Categoría Padre</th>
						<th>Categoría</th>
						<th>Referencia</th>
						<th>Nombre Producto</th>
						<th>Id Combinación</th>
						<th>Combinación</th>
						<th>Precio Mínimo</th>
						<th>Costos Fijos</th>
						<th>Costos Variables</th>
						<th>Precio de Costo</th>
						<th>Precio Mayor</th>
						<th>Precio Mayor + IVA</th>
						<th>Precio Detal</th>
						<th>Precio Detal + IVA</th>
					</tr>
				</thead>
				<tbody>
					".$list_products."
				</tbody>
			</table>";
	}
    
    
    // Generación de lista completa de productos con sus combinaciones
    function update_public_price_prestashop()
    {
		$list_products = "";
		
		// Listado de productos y sus respectivas combinaciones
		$attribs_product = $this->MProducts->obtenerCombinaciones();
		
		// Construimos la lista del cuerpo si existen combinaciones
		if(count($attribs_product) > 0){
			
			$i = 1;
			foreach($attribs_product as $combination){
				
				$precio = 1;
				$precio_iva = 0;
				
				// Búsqueda del precio de cada combinación de producto
				list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
				
				$precio_costo = number_format($precio, 2, ',', '.');
				
				$list_products .= "<tr>
									<td>".$i."</td>
									<td>".$combination->category_name_parent."</td>
									<td>".$combination->category_name."</td>
									<td>".$combination->product_name."</td>
									<td>".$combination->attribute_name."</td>
									<td>".number_format($precio*1.30, 2, ',', '.')."</td>
									<td>".number_format($precio*1.30*1.30, 2, ',', '.')."</td>
								</tr>";
								
				$i++;
				
			}
			
		}
		
		// Imprimimos el listado resultante
		echo "
		
			<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
			
			<style>
				table, tr, th, td {
					border:1px solid; padding:10px;
				}
				.id-combination {
					text-align: center;
				}
			</style>
			
			<table id='list_products'>
				<thead>
					<tr>
						<th>#</th>
						<th>Categoría</th>
						<th>Sub Categoría</th>
						<th>Producto</th>
						<th>Tela</th>
						<th>Precio Mayor</th>
						<th>Precio Detal</th>
					</tr>
				</thead>
				<tbody>
					".$list_products."
				</tbody>
			</table>";
	}
    
    
    // Generación de lista completa de productos con sus combinaciones
    function update_alternative_price_prestashop()
    {
		$list_products = "";  // Almacenará el listado de registros
		
		$list_number = $this->MProducts->latest_list_m3();  // Obtenemos el número de la última lista de precios guardada
		
		if(count($list_number) > 0){
		
			// Listado de productos y sus respectivas combinaciones
			$attribs_product = $this->MProducts->obtenerCombinacionesM3($list_number[0]->list_number);
			
			// Construimos la lista del cuerpo si existen combinaciones
			if(count($attribs_product) > 0){
				
				$i = 1;
				foreach($attribs_product as $combination){
					
					$list_products .= "<tr>
										<td>".$combination->position."</td>
										<td>".$combination->category."</td>
										<td>".$combination->subcategory."</td>
										<td>".$combination->product."</td>
										<td>".$combination->material."</td>
										<td>".number_format($combination->price_wholesaler, 2, ',', '.')."</td>
										<td>".number_format($combination->price_retail, 2, ',', '.')."</td>
									</tr>";
									
					$i++;
					
				}
				
			}
		
		}
		
		// Imprimimos el listado resultante
		echo "
		
			<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
			
			<style>
				table, tr, th, td {
					border:1px solid; padding:10px;
				}
				.id-combination {
					text-align: center;
				}
			</style>
			
			<a href='".base_url()."export_csv' class='btn btn-success pull-right'>Exportar</a>
			
			<br>
			<br>
			
			<table id='list_products'>
				<thead>
					<tr>
						<th>#</th>
						<th>Categoría</th>
						<th>Sub Categoría</th>
						<th>Producto</th>
						<th>Tela</th>
						<th>Precio Mayor</th>
						<th>Precio Detal</th>
					</tr>
				</thead>
				<tbody>
					".$list_products."
				</tbody>
			</table>
			
			<br>
			
			<a href='".base_url()."export_csv' class='btn btn-success pull-right'>Exportar</a>
			";
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
 * Método para genarar un arreglo con los precios mínimos de cada producto.
 * ------------------------------------------------------
 * 
 * Este método permite construir un listado de precios mínimos de todos 
 * los productos que estén incluidos en el listado de combinaciones recibido.
 */
    public function minimum_prices($combinations)
	{
		$ids = array();  // Colector de ids de productos de la lista de combinaciones
		
		$prices = array();  // Colector de precios de cada combinación de cada producto
		
		$minimum_prices = array();  // Almacenador de precios mínimos de cada producto
		
		// Colectamos los ids de productos de la lista de combinaciones, para producir un arreglo con la estructura:
		// array([0] => id_producto_1, [2] => id_producto_2, [3] => id_producto_3, ...)
		foreach($combinations as $combination){
		
			if(!in_array($combination->id_product, $ids)){
				
				$ids[] = $combination->id_product;
				
			}
			
		}
		
		// Colectamos los precios al detal de cada combinación de cada producto, para producir un arreglo con la estructura:
		// array([id_producto] => array(precio1, preico2, precio3...))
		foreach($ids as $id_product){
			
			foreach($combinations as $combination){
				
				if($id_product == $combination->id_product){
					
					// Búsqueda del precio de cada combinación de producto
					list($costos_fijos, $costos_variables, $precio) = $this->calculate_price($combination->id_product, $combination->id_attribute, $combination->id_product_attribute);
					$prices["".$id_product][] = $precio*1.30*1.30;
			
				}
				
			}
			
		}
		
		// Colectamos los precios mínimos de cada producto haciendo un recorrido con foreach múltiple, para producir un arreglo con la estructura:
		// array([id_producto_1] => precio1, [id_producto_2] => precio2, [id_producto_3] => precio3, ...))
		foreach($prices as $key => $product_prices){
			
			// Inicialización del precio mínimo del producto con el primer precio de la lista
			$minimum_prices["".$key] = $product_prices[0];
			
			// Captura del precio mínimo 
			foreach($product_prices as $product_price){
					
				if($product_price < $minimum_prices["".$key]){
					
					$minimum_prices["".$key] = $product_price;
					
				}
				
			}
		}
		
		return $minimum_prices;
	}
	
	
/**
 * ------------------------------------------------------
 * Método para exportar un archivo csv con el último 
 * listado de precios guardado.
 * ------------------------------------------------------
 * 
 * Este método permite construir un archivo en foramto csv con la última
 * lista de precios guardada.
 */
    function export_csv()
    {
		
		$list_number = $this->MProducts->latest_list_m3();  // Obtenemos el número de la última lista de precios guardada
		
		if(count($list_number) > 0){
			
			$delimiter = ";";
			$filename = "prices_m3_" . date('Y-m-d') . ".csv";  // Nombre del archivo
			
			// Crea un puntero de archivo
			$f = fopen('php://memory', 'w');
		
			// Listado de productos y sus respectivas combinaciones
			$attribs_product = $this->MProducts->obtenerCombinacionesM3($list_number[0]->list_number);
			
			// Construimos la lista del cuerpo si existen combinaciones
			if(count($attribs_product) > 0){
				
				// Establecemos el encabezado delas columnas
				$fields = array('Posición', 'Categoría', 'Sub Categoría', 'Producto', 'Tela', 'Precio Mayor', 'Precio Detal');
				fputcsv($f, $fields, $delimiter);
				
				// Construimos una fila con cada registro y lo vamos escribiendo en el csv
				foreach($attribs_product as $combination){
					
					$lineData = array(
						$combination->position, 
						$combination->category, 
						$combination->subcategory, 
						$combination->product, 
						$combination->material, 
						number_format($combination->price_wholesaler, 2, ',', '.'),
						number_format($combination->price_retail, 2, ',', '.')
					);
					
					fputcsv($f, $lineData, $delimiter);
					
				}
				
				// Colocamos el puntero al inicio del archivo
				fseek($f, 0);
				
				// Establecemos los encabezados para descargar el archivo en lugar de mostrarlo
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="' . $filename . '";');
				
				// Generamos todos los datos restantes en un puntero de archivo
				fpassthru($f);
				
			}
		
		}
		
		exit;
	
	}

}
