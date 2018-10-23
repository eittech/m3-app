<?php
//~ header('Content-Type: text/json; charset=utf-8;');
defined('BASEPATH') OR exit('No direct script access allowed');

header("Access-Control-Allow-Origin: *");

class COrders extends CI_Controller {
	
	public $tables;  // Tablas con relación directa a las ordenes
	
	public $tables2;  // Tablas secundarias

    public function __construct() {
        parent::__construct();
        
        /* Definición de tablas a incluir en el json, incluyendo sus campos de relación a detallar.
         * Cada tabla tiene un arreglo de relaciones a detallar, pero el primer elemento de dicho arreglo es un
         * definidor de tabla, para identificar si la tabla de relación directa o indirecta.
         * */
        $this->tables = array(
			'orders' => array(
				'id_shop_group','id_shop','id_carrier','id_lang','id_customer','id_cart','id_currency',
				'id_address_delivery','id_address_invoice'
			),
			'order_carrier' => array(1,'id_carrier'),
			'order_cart_rule' => array(1,'id_cart_rule'),
			'order_detail' => array(
				'id_warehouse','id_shop','product_id','product_attribute_id','id_customization','id_tax_rules_group'
			),
			'order_history' => array(
				'id_employee','id_order_state'
			),
			'order_invoice' => array(),
			'order_invoice_payment' => array(),
			'order_payment' => array('id_currency'),
			'order_return' => array('id_customer'),
			'order_slip' => array('id_customer')
		);
		
		$this->tables2 = array(
			'order_detail_tax' => array(
				'id_tax'
			),
			'order_invoice_tax' => array(
				'id_tax'
			),
			'order_return_detail' => array('id_order_detail','id_customization'),
			'order_slip_detail' => array('id_order_detail')
		);
		
		$this->tables3 = array(
			'order_slip_detail_tax' => array(
				'id_tax'
			)
		);
        
        // Load database
        $this->load->model('MOrders');
        $this->load->model('MOrdersTerms','ter');
    }

    // Método para cargar los datos de una orden según el id
    public function index($order_id) {
		
		// Consultamos los datos de la orden correspondiente para disponer de los mismos más adelante
		$data_order = $this->MOrders->obtenerById('orders', 'id_order', $order_id);
		
		// Variable que contendrá la nueva data armada que incluirá los datos relacionados de primer nivel
		$new_data = array();
		
		if(count($data_order) > 0){
			
			// Ids de las tablas con relaciones a terceras tablas
			$id_order_detail = array();
			$id_order_invoice = array();
			$id_order_return = array();
			$id_order_slip = array();
		
			$i = 0;
			// CICLO DE RECORRIDO DE TABLAS PRIMARIAS
			foreach($this->tables as $table => $relations){
				
				$field;  // Campo Identificador a usar para filtrar la orden, puede ser el campo 'id_order' o el campo 'reference'.
				
				$value;  // Identificador a usar para filtrar la orden, cuyo valor puede venir del campo 'id_order' o del campo 'reference'.
				
				// Si el campo id_order existe en la tabla actual, usamos dicho campo y su valor como identificadores para filtrar
				if($this->db->field_exists('id_order', $table)){
					$field = 'id_order';
					$value = $order_id;
				}else{
					$field = 'order_reference';
					$value = $data_order[0]->reference;
				}
				
				// Consultamos la tabla correspondiente
				$data = $this->MOrders->obtenerById($table, $field, $value);
				
				// Contador de registros
				$j = 0;
				
				$reg_data = array();
				
				foreach($data as $registro){
					
					// Contador de campos
					$k = 0;
					
					$field_data = array();
					
					foreach($registro as $key => $valor){
						
						// Guardamos los ids de las tablas con relaciones secundarias
						if($table == "order_detail" && $key == "id_order_detail"){
							$id_order_detail[] = $valor;
						}
						if($table == "order_invoice" && $key == "id_order_invoice"){
							$id_order_invoice[] = $valor;
						}
						if($table == "order_return" && $key == "id_order_return"){
							$id_order_return[] = $valor;
						}
						if($table == "order_slip" && $key == "id_order_slip"){
							$id_order_slip[] = $valor;
						}
						
						// Cargamos cada campo-valor del registro
						$field_data[$key] = $valor;
						
						// Si la clave actual está presente en la lista de ids asociativos
						if(in_array($key, $relations)){
							
							// Separamos el nombre de la tabla a consultar
							$table2 = explode("_", $key);
							
							// Armamos el nombre de la tabla a consultar
							if(count($table2) > 3){
								$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
							}else if(count($table2) > 2){
								// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
								if($key == "id_address_delivery" || $key == "id_address_invoice"){
									$table2 = $table2[1];
								}
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
								else if($table2[2] == "id"){
									$table2 = $table2[0]."_".$table2[1];
								}else{
									$table2 = $table2[1]."_".$table2[2];
								}
							}else if(count($table2) == 2){
								// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
								if($table2[1] == "id"){
									$table2 = $table2[0];
								}else{
									$table2 = $table2[1];
								}
							}
							
							$clave = $key;
							// Si es una clave de dirección entonces fijamos la misma en 'id_address'
							if($key == "id_address_delivery" || $key == "id_address_invoice"){
								$clave = 'id_address';
							}
							// Si es una clave de producto entonces fijamos la misma en 'id_product'
							else if($key == "product_id"){
								$clave = 'id_product';
							}
							// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
							else if($key == "product_attribute_id"){
								$clave = 'id_product_attribute';
							}
							
							// Consultamos los detalles de la asociación a la tabla resultante
							$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);
							
							// Cargamos un nuevo campo-valor con los detalles del campo asociado
							// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
							$new_key = explode("_", $key, 2);
							// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
							if($key == "product_id"){
								$new_key = $new_key[0];
							}
							// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
							else if($key == "product_attribute_id"){
								$new_key = substr($key, 0, -3);
							}
							// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
							else{
								$new_key = $new_key[1];
							}
							$field_data[$new_key] = $data_detalle;
							
							// foreach($data_detalle as $reg){
								// 
								// foreach($reg as $key_sub => $valor_sub){
									// 
								// }
							// 
							// }
						}
						
					}
					
					/*
					 * Proceso de agrupación de los productos repetidos.
					 * Se suman las cantidades de los productos duplicados Oficial.
					 * */
					if(count($reg_data) == 0){
						$reg_data[] = $field_data;
					}else{
						// Buscamos
						$encontrado = 0;
						$i = 0;
						foreach($reg_data as $reg){
							if(isset($field_data['product_id']) && $reg['product_id'] == $field_data['product_id'] && $reg['unit_price_tax_excl'] == $field_data['unit_price_tax_excl']){
								$reg_data[$i]['product_quantity'] += (int)$field_data['product_quantity'];
								$encontrado += 1;
							}
							$i += 1;
						}
						// Incluimos el nuevo producto si no lo encontramos
						if($encontrado == 0){
							$reg_data[] = $field_data;
						}
					}
					
				}
				
				// Convertimos la tabla orders a su singular para dar más sentido al conjunto de datos
				if($table == "orders"){
					$table = "order";
				}
				
				// Creamos un indice por cada tabla y le asignamos los registros correspondientes
				$new_data[$table] = $reg_data;
				
				$i++;
			}
			
			// Ids de las tablas con relaciones a cuartas tablas
			$id_order_slip_detail = array();
			
			// Valores de los ids de las tablas a recorrer
			$values_ids = array(
				"order_detail_tax" => $id_order_detail, 
				"order_invoice_tax" => $id_order_invoice, 
				"order_return_detail" => $id_order_return,
				"order_slip_detail" => $id_order_slip
			);
			
			// Añadimos los ids a consultar en las tablas secundarias
			array_unshift($this->tables2["order_detail_tax"], $id_order_detail);
			array_unshift($this->tables2["order_invoice_tax"], $id_order_invoice);
			array_unshift($this->tables2["order_return_detail"], $id_order_return);
			array_unshift($this->tables2["order_slip_detail"], $id_order_slip);
		
			$i = 0;
			// CICLO DE RECORRIDO DE TABLAS SECUNDARIAS
			foreach($this->tables2 as $table => $relations){
				
				$field;  // Campo Identificador a usar para filtrar la orden.
				
				$value;  // Identificador a usar para filtrar la orden.
				
				// Recorremos la lista de ids a consultar
				foreach($relations[0] as $id_value){
				
					// Construcción del campo identificador y el valor correspondiente
					$field = explode("_", $table);
					$field = "id_".$field[0]."_".$field[1];
					
					$value = $id_value;
					
					// Consultamos la tabla correspondiente
					$data = $this->MOrders->obtenerById($table, $field, $value);
					
					// Contador de registros
					$j = 0;
					
					$reg_data = array();
					
					foreach($data as $registro){
						
						// Contador de campos
						$k = 0;
						
						$field_data = array();
						
						foreach($registro as $key => $valor){
							
							// Guardamos los ids de las tablas con relaciones terciarias
							if($table == "order_slip_detail" && $key == "id_order_slip_detail"){
								$id_order_slip_detail[] = $valor;
							}
							
							// Cargamos cada campo-valor del registro
							$field_data[$key] = $valor;
							
							// Si la clave actual está presente en la lista de ids asociativos
							if(in_array($key, $relations)){
								
								// Separamos el nombre de la tabla a consultar
								$table2 = explode("_", $key);
								
								// Armamos el nombre de la tabla a consultar
								if(count($table2) > 3){
									$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
								}else if(count($table2) > 2){
									// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
									if($key == "id_address_delivery" || $key == "id_address_invoice"){
										$table2 = $table2[1];
									}
									// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos las dos primeras palabras
									else if($table2[2] == "id"){
										$table2 = $table2[0]."_".$table2[1];
									}else{
										$table2 = $table2[1]."_".$table2[2];
									}
								}else if(count($table2) == 2){
									// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
									if($table2[1] == "id"){
										$table2 = $table2[0];
									}else{
										$table2 = $table2[1];
									}
								}
								
								$clave = $key;
								// Si es una clave de dirección entonces fijamos la misma en 'id_address'
								if($key == "id_address_delivery" || $key == "id_address_invoice"){
									$clave = 'id_address';
								}
								// Si es una clave de producto entonces fijamos la misma en 'id_product'
								else if($key == "product_id"){
									$clave = 'id_product';
								}
								// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
								else if($key == "product_attribute_id"){
									$clave = 'id_product_attribute';
								}
								
								// Consultamos los detalles de la asociación a la tabla resultante
								$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);
								
								// Cargamos un nuevo campo-valor con los detalles del campo asociado
								// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
								$new_key = explode("_", $key, 2);
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
								if($key == "product_id"){
									$new_key = $new_key[0];
								}
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
								else if($key == "product_attribute_id"){
									$new_key = substr($key, 0, -3);
								}
								// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
								else{
									$new_key = $new_key[1];
								}
								$field_data[$new_key] = $data_detalle;
								
							}
							
						}
						
						$reg_data[] = $field_data;
						
					}
					
				}
				
				// Creamos un indice por cada tabla y le asignamos los registros correspondientes
				$new_data[$table] = $reg_data;
				
				$i++;
			}
			
			
			//~ Convertimos los datos resultantes a formato JSON
			$jsonencoded = json_encode($new_data, JSON_UNESCAPED_UNICODE);
			echo $jsonencoded;
			
		}
    }

    // Método para cargar los datos de una orden de forma detallada según el id
    public function details($order_id) {
		
		// Consultamos los datos de la orden correspondiente para disponer de los mismos más adelante
		$data_order = $this->MOrders->obtenerById('orders', 'id_order', $order_id);
		
		// Variable que contendrá la nueva data armada que incluirá los datos relacionados de primer nivel
		$new_data = array();
		
		if(count($data_order) > 0){
			
			// Ids de las tablas con relaciones a terceras tablas
			$id_order_detail = array();
			$id_order_invoice = array();
			$id_order_return = array();
			$id_order_slip = array();
		
			$i = 0;
			// CICLO DE RECORRIDO DE TABLAS PRIMARIAS
			foreach($this->tables as $table => $relations){
				
				$field;  // Campo Identificador a usar para filtrar la orden, puede ser el campo 'id_order' o el campo 'reference'.
				
				$value;  // Identificador a usar para filtrar la orden, cuyo valor puede venir del campo 'id_order' o del campo 'reference'.
				
				// Si el campo id_order existe en la tabla actual, usamos dicho campo y su valor como identificadores para filtrar
				if($this->db->field_exists('id_order', $table)){
					$field = 'id_order';
					$value = $order_id;
				}else{
					$field = 'order_reference';
					$value = $data_order[0]->reference;
				}
				
				// Consultamos la tabla correspondiente
				$data = $this->MOrders->obtenerById($table, $field, $value);
				
				// Contador de registros
				$j = 0;
				
				$reg_data = array();
				
				foreach($data as $registro){
					
					// Contador de campos
					$k = 0;
					
					$field_data = array();
					
					// Si la tabla iterada es la de detalles de productos
					if($table == "order_detail" && count($registro) > 0){
						
						// Consultamos las personalizaciones del detalle(producto) 
						$customizations = $this->MOrders->getCustomizations($data_order[0]->id_cart, $registro->product_attribute_id, $registro->product_id);

						
						$customs = array();  // Agrupará los atributos de las personalizaciones por id_customization
						
						// Si hay customizaciones(personalizaciones) del detalle(producto)
						if(count($customizations) > 0){
							
							// Proceso de agrupación de personalizaciones
							foreach($customizations as $customization){
								
								$customs[$customization->id_customization][$customization->name] = $customization->value;
								//$customs[$customization->id_customization]['product_quantity'] = $customization->quantity;
								
							}
							
							//  Generamos un registro por cada agrupación(personalización) del detalle(producto)
							foreach($customs as $key_custom => $attr){
								
								// Recorrido de los campos del registro
								foreach($registro as $key => $valor){
								
									// Guardamos los ids de las tablas con relaciones secundarias
									if($key == "id_order_detail"){
										$id_order_detail[] = $valor;
									}
									
									// Cargamos cada campo-valor del registro
									$field_data[$key] = $valor;
									
									// Si la clave actual está presente en la lista de ids asociativos
									if(in_array($key, $relations)){
										
										// Separamos el nombre de la tabla a consultar
										$table2 = explode("_", $key);
										
										// Armamos el nombre de la tabla a consultar
										if(count($table2) > 3){
											$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
										}else if(count($table2) > 2){
											// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
											if($key == "id_address_delivery" || $key == "id_address_invoice"){
												$table2 = $table2[1];
											}
											// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
											else if($table2[2] == "id"){
												$table2 = $table2[0]."_".$table2[1];
											}else{
												$table2 = $table2[1]."_".$table2[2];
											}
										}else if(count($table2) == 2){
											// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
											if($table2[1] == "id"){
												$table2 = $table2[0];
											}else{
												$table2 = $table2[1];
											}
										}
										
										$clave = $key;
										// Si es una clave de dirección entonces fijamos la misma en 'id_address'
										if($key == "id_address_delivery" || $key == "id_address_invoice"){
											$clave = 'id_address';
										}
										// Si es una clave de producto entonces fijamos la misma en 'id_product'
										else if($key == "product_id"){
											$clave = 'id_product';
										}
										// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
										else if($key == "product_attribute_id"){
											$clave = 'id_product_attribute';
										}
										
										// Consultamos los detalles de la asociación a la tabla resultante
										$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);

										
										// Cargamos un nuevo campo-valor con los detalles del campo asociado
										// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
										$new_key = explode("_", $key, 2);
										// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
										if($key == "product_id"){
											$new_key = $new_key[0];
										}
										// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos las dos primeras palabras
										else if($key == "product_attribute_id"){
											$new_key = substr($key, 0, -3);
										}
										// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
										else{
											$new_key = $new_key[1];
										}
										$field_data[$new_key] = $data_detalle;
										
									}
									
									/* Consultamos el nombre del producto directamente a la tabla 'product_lang', 
									 * teniendo en cuenta su id y la tienda a la que pertenece.
									 * */
									if($key == "product_id"){
										
										$datalle_producto = $this->MOrders->obtenerByIds('product_lang', 'id_shop', 'id_product', $data_order[0]->id_shop, $valor);
										
										// Cargamos el campo-valor del nombre del producto
										$field_data['product_short_name'] = $datalle_producto[0]->name;
										
									}
									/* Consulta el value si contiene customizationes relacionadas a la tabla (customized_data) */
									/*if($key == "id_customization"){

										$detalle_personalizacion = $this->MOrders->get_customization($valor);

                                        if(count($detalle_personalizacion) > 0){
                                            // Cargamos el campo-valor del nombre del la customizacion
                                            $replace_text = $this->MOrders->replace_text("Observaciones","Bordado",$detalle_personalizacion[0]->value);
                                            $field_data['customized_data'] = $replace_text;
                                        }
									}*/
									
								}
								
								// Añadimos los campos de personalización
								foreach($attr as $key_attr => $value_attr){
									$field_data[$key_attr] = $value_attr;
								}
								
								/*
								 * Proceso de agrupación de los productos repetidos.
								 * No se suman las cantidades de los productos duplicados.
								 * */

								if(count($reg_data) == 0){
									
									$reg_data[] = $field_data;
								}else{
									
									// Buscamos
									$encontrado = 0;
									$i = 0;
									/*echo "<pre>";
									print_r($reg_data);
									echo "</pre>";
									exit;*/

									foreach($reg_data as $reg){


										// Validamos que coincida el id del producto pero no el id de su personalización (customization)
										if($reg['product_id'] == $field_data['product_id'] && $reg['id_customization'] == $field_data['id_customization']){
											//$reg_data[$i]['product_quantity'] += (int)$reg['product_quantity'];
											$encontrado += 1;
										}
										$i += 1;
									}
									// Incluimos el nuevo producto si no lo encontramos
									if($encontrado == 0){
										$reg_data[] = $field_data;
									}
								}
								
								//~ $reg_data[] = $field_data;  // Forma anterior
							
							}
							
							
						}else{  // Si no hay customizaciones(personalizaciones) del detalle(producto
							
							// Recorrido de los campos del registro
							foreach($registro as $key => $valor){
							
								// Guardamos los ids de las tablas con relaciones secundarias
								if($key == "id_order_detail"){
									$id_order_detail[] = $valor;
								}
								
								// Cargamos cada campo-valor del registro
								$field_data[$key] = $valor;
								
								// Si la clave actual está presente en la lista de ids asociativos
								if(in_array($key, $relations)){
									
									// Separamos el nombre de la tabla a consultar
									$table2 = explode("_", $key);
									
									// Armamos el nombre de la tabla a consultar
									if(count($table2) > 3){
										$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
									}else if(count($table2) > 2){
										// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
										if($key == "id_address_delivery" || $key == "id_address_invoice"){
											$table2 = $table2[1];
										}
										// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
										else if($table2[2] == "id"){
											$table2 = $table2[0]."_".$table2[1];
										}else{
											$table2 = $table2[1]."_".$table2[2];
										}
									}else if(count($table2) == 2){
										// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
										if($table2[1] == "id"){
											$table2 = $table2[0];
										}else{
											$table2 = $table2[1];
										}
									}
									
									$clave = $key;
									// Si es una clave de dirección entonces fijamos la misma en 'id_address'
									if($key == "id_address_delivery" || $key == "id_address_invoice"){
										$clave = 'id_address';
									}
									// Si es una clave de producto entonces fijamos la misma en 'id_product'
									else if($key == "product_id"){
										$clave = 'id_product';
									}
									// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
									else if($key == "product_attribute_id"){
										$clave = 'id_product_attribute';
									}
									
									// Consultamos los detalles de la asociación a la tabla resultante
									$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);
									
									// Cargamos un nuevo campo-valor con los detalles del campo asociado
									// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
									$new_key = explode("_", $key, 2);
									// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
									if($key == "product_id"){
										$new_key = $new_key[0];
									}
									// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos las dos primeras palabras
									else if($key == "product_attribute_id"){
										$new_key = substr($key, 0, -3);
									}
									// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
									else{
										$new_key = $new_key[1];
									}
									$field_data[$new_key] = $data_detalle;
									
								}
								
								/* Consultamos el nombre del producto directamente a la tabla 'product_lang', 
								 * teniendo en cuenta su id y la tienda a la que pertenece.
								 * */
								if($key == "product_id"){
									
									$datalle_producto = $this->MOrders->obtenerByIds('product_lang', 'id_shop', 'id_product', $data_order[0]->id_shop, $valor);
									
									// Cargamos el campo-valor del nombre del producto
									$field_data['product_short_name'] = $datalle_producto[0]->name;
									
								}
								
							}
							
							/*
							 * Proceso de agrupación de los productos repetidos.
							 * Se suman las cantidades de los productos duplicados.
							 * */
							if(count($reg_data) == 0){
								$reg_data[] = $field_data;
							}else{
								// Buscamos
								$encontrado = 0;
								$i = 0;
								foreach($reg_data as $reg){
									if($reg['product_id'] == $field_data['product_id']){
										$reg_data[$i]['product_quantity'] += (int)$field_data['product_quantity'];
										$encontrado += 1;
									}
									$i += 1;
								}
								// Incluimos el nuevo producto si no lo encontramos
								if($encontrado == 0){
									$reg_data[] = $field_data;
								}
							}
							
							//~ $reg_data[] = $field_data;  // Forma anterior
						}
						
					}else{
						
						// Recorrido de los campos del registro
						foreach($registro as $key => $valor){
							
							// Guardamos los ids de las tablas con relaciones secundarias
							if($table == "order_invoice" && $key == "id_order_invoice"){
								$id_order_invoice[] = $valor;
							}
							if($table == "order_return" && $key == "id_order_return"){
								$id_order_return[] = $valor;
							}
							if($table == "order_slip" && $key == "id_order_slip"){
								$id_order_slip[] = $valor;
							}
							
							// Cargamos cada campo-valor del registro
							$field_data[$key] = $valor;
							
							// Si la clave actual está presente en la lista de ids asociativos
							if(in_array($key, $relations)){
								
								// Separamos el nombre de la tabla a consultar
								$table2 = explode("_", $key);
								
								// Armamos el nombre de la tabla a consultar
								if(count($table2) > 3){
									$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
								}else if(count($table2) > 2){
									// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
									if($key == "id_address_delivery" || $key == "id_address_invoice"){
										$table2 = $table2[1];
									}
									// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
									else if($table2[2] == "id"){
										$table2 = $table2[0]."_".$table2[1];
									}else{
										$table2 = $table2[1]."_".$table2[2];
									}
								}else if(count($table2) == 2){
									// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
									if($table2[1] == "id"){
										$table2 = $table2[0];
									}else{
										$table2 = $table2[1];
									}
								}
								
								$clave = $key;
								// Si es una clave de dirección entonces fijamos la misma en 'id_address'
								if($key == "id_address_delivery" || $key == "id_address_invoice"){
									$clave = 'id_address';
								}
								// Si es una clave de producto entonces fijamos la misma en 'id_product'
								else if($key == "product_id"){
									$clave = 'id_product';
								}
								// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
								else if($key == "product_attribute_id"){
									$clave = 'id_product_attribute';
								}
								
								// Consultamos los detalles de la asociación a la tabla resultante
								$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);
								
								// Cargamos un nuevo campo-valor con los detalles del campo asociado
								// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
								$new_key = explode("_", $key, 2);
								// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
								if($key == "product_id"){
									$new_key = $new_key[0];
								}
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos las dos primeras palabras
								else if($key == "product_attribute_id"){
									$new_key = substr($key, 0, -3);
								}
								// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
								else{
									$new_key = $new_key[1];
								}
								$field_data[$new_key] = $data_detalle;
								
							}
							
							/* Consultamos el nombre del producto directamente a la tabla 'product_lang', 
							 * teniendo en cuenta su id y la tienda a la que pertenece.
							 * */
							if($table == "order_detail" && $key == "product_id"){
								
								$datalle_producto = $this->MOrders->obtenerByIds('product_lang', 'id_shop', 'id_product', $data_order[0]->id_shop, $valor);
								
								// Cargamos el campo-valor del nombre del producto
								$field_data['product_short_name'] = $datalle_producto[0]->name;
								
							}
							
						}
						
						$reg_data[] = $field_data;
					}
					
				}
				
				// Convertimos la tabla orders a su singular para dar más sentido al conjunto de datos
				if($table == "orders"){
					$table = "order";
				}
				
				// Creamos un indice por cada tabla y le asignamos los registros correspondientes
				$new_data[$table] = $reg_data;
				
				$i++;
			}
			
			// Ids de las tablas con relaciones a cuartas tablas
			$id_order_slip_detail = array();
			
			// Valores de los ids de las tablas a recorrer
			$values_ids = array(
				"order_detail_tax" => $id_order_detail, 
				"order_invoice_tax" => $id_order_invoice, 
				"order_return_detail" => $id_order_return,
				"order_slip_detail" => $id_order_slip
			);
			
			// Añadimos los ids a consultar en las tablas secundarias
			array_unshift($this->tables2["order_detail_tax"], $id_order_detail);
			array_unshift($this->tables2["order_invoice_tax"], $id_order_invoice);
			array_unshift($this->tables2["order_return_detail"], $id_order_return);
			array_unshift($this->tables2["order_slip_detail"], $id_order_slip);
		
			$i = 0;
			// CICLO DE RECORRIDO DE TABLAS SECUNDARIAS
			foreach($this->tables2 as $table => $relations){
				
				$field;  // Campo Identificador a usar para filtrar la orden.
				
				$value;  // Identificador a usar para filtrar la orden.
				
				// Recorremos la lista de ids a consultar
				foreach($relations[0] as $id_value){
				
					// Construcción del campo identificador y el valor correspondiente
					$field = explode("_", $table);
					$field = "id_".$field[0]."_".$field[1];
					
					$value = $id_value;
					
					// Consultamos la tabla correspondiente
					$data = $this->MOrders->obtenerById($table, $field, $value);
					
					// Contador de registros
					$j = 0;
					
					$reg_data = array();
					
					foreach($data as $registro){
						
						// Contador de campos
						$k = 0;
						
						$field_data = array();
						
						foreach($registro as $key => $valor){
							
							// Guardamos los ids de las tablas con relaciones terciarias
							if($table == "order_slip_detail" && $key == "id_order_slip_detail"){
								$id_order_slip_detail[] = $valor;
							}
							
							// Cargamos cada campo-valor del registro
							$field_data[$key] = $valor;
							
							// Si la clave actual está presente en la lista de ids asociativos
							if(in_array($key, $relations)){
								
								// Separamos el nombre de la tabla a consultar
								$table2 = explode("_", $key);
								
								// Armamos el nombre de la tabla a consultar
								if(count($table2) > 3){
									$table2 = $table2[1]."_".$table2[2]."_".$table2[3];
								}else if(count($table2) > 2){
									// Si es una clave de dirección entonces tomamos sólo la segunda palabra y obviamos la tercera
									if($key == "id_address_delivery" || $key == "id_address_invoice"){
										$table2 = $table2[1];
									}
									// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos las dos primeras palabras
									else if($table2[2] == "id"){
										$table2 = $table2[0]."_".$table2[1];
									}else{
										$table2 = $table2[1]."_".$table2[2];
									}
								}else if(count($table2) == 2){
									// Si es una clave con nomenclatura invertida 'xxxx_id' tomamos la primera palabra
									if($table2[1] == "id"){
										$table2 = $table2[0];
									}else{
										$table2 = $table2[1];
									}
								}
								
								$clave = $key;
								// Si es una clave de dirección entonces fijamos la misma en 'id_address'
								if($key == "id_address_delivery" || $key == "id_address_invoice"){
									$clave = 'id_address';
								}
								// Si es una clave de producto entonces fijamos la misma en 'id_product'
								else if($key == "product_id"){
									$clave = 'id_product';
								}
								// Si es una clave de atributo de producto entonces fijamos la misma en 'id_product_attribute'|
								else if($key == "product_attribute_id"){
									$clave = 'id_product_attribute';
								}
								
								// Consultamos los detalles de la asociación a la tabla resultante
								$data_detalle = $this->MOrders->obtenerDetalle($table2, $clave, $valor);
								
								// Cargamos un nuevo campo-valor con los detalles del campo asociado
								// En este caso la clave será simplificada y recortada para eliminar el segmento 'id_'
								$new_key = explode("_", $key, 2);
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
								if($key == "product_id"){
									$new_key = $new_key[0];
								}
								// Si es una clave con nomenclatura invertida 'xxxx_xxxx_id' tomamos la primera palabra
								else if($key == "product_attribute_id"){
									$new_key = substr($key, 0, -3);
								}
								// Si es una clave con nomenclatura normal 'id_xxxx' tomamos la seguda palabra
								else{
									$new_key = $new_key[1];
								}
								$field_data[$new_key] = $data_detalle;
								
							}
							
						}
						
						$reg_data[] = $field_data;
						
					}
					
				}
				
				// Creamos un indice por cada tabla y le asignamos los registros correspondientes
				$new_data[$table] = $reg_data;
				
				$i++;
			}
			
			
			//~ Convertimos los datos resultantes a formato JSON
			$jsonencoded = json_encode($new_data, JSON_UNESCAPED_UNICODE);
			echo $jsonencoded;
			
		}
    }
    
    // Actualización del número de factura de un pedido
    public function update_order() {
		
		$invoice_number = $this->input->get('invoice_number');
		
		if($invoice_number != '' && is_numeric($invoice_number)){
			$datos = array(
				'id_order' => $this->input->get('id_order'),
				'invoice_number' => $this->input->get('invoice_number')
			);
			
			$datos2 = array(
				'id_order' => $this->input->get('id_order'),
				'number' => $this->input->get('invoice_number')
			);
			
			$result = $this->MOrders->update('orders',$datos);
			
			$result2 = $this->MOrders->update('order_invoice',$datos2);
			
			if ($result) {
					
				echo '{"response":"ok"}';
				
			}else{
				
				echo '{"response":"error"}';
				
			}
		}else{
			echo '{"response":"invalid number invoice field"}';
		}
		
    }
    
	// Generación del reporte de la orden
    function pdf_invoice($order_id)
    {
        // Consultamos los datos de la orden
		$get2 = file_get_contents(base_url()."orders/".$order_id);
		$exchangeRates2 = json_decode($get2, true);
        
        $data['order'] = $exchangeRates2;
        
        $this->load->view('pdf/invoice_report', $data);
    }
    
	// Generación del reporte de la orden
    function pdf_order($order_id)
    {
        // Consultamos los datos de la orden
		$get2 = file_get_contents(base_url()."orders/details/".$order_id);
		$exchangeRates2 = json_decode($get2, true);
        $data['order'] = $exchangeRates2;
        $this->load->view('pdf/order_report', $data);
    }

    // Generación del reporte de la orden Cotizacion
    function pdf_order_cotization($order_id)
    {
        // Consultamos los datos de la orden
		$get3 = file_get_contents(base_url()."orders/details/".$order_id);
		$exchangeRates3 = json_decode($get3, true);

		$data['order_terms'] = $this->ter->row_order_terms($order_id);
        $data['order'] = $exchangeRates3;
		$data['order_detail_where'] = $this->MOrders->order_detail_where($order_id);
		//echo $this->db->last_query(); exit;
        
        $this->load->view('pdf/order_cotization_report', $data);
    }

    // Generación del reporte de la orden Payment
    function pdf_payment($year, $month)
    {
        // Consultamos los datos de la orden
		//$get3 = file_get_contents(base_url()."orders/details/".$order_id);
		//$exchangeRates3 = json_decode($get3, true);

		$data['order_payment'] = $this->MOrders->order_payment($year, $month);
        $data['order'] = $exchangeRates3;
        $this->load->view('pdf/order_payment_report', $data);
    }

}
