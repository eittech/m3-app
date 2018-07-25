<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_pricelist extends CI_Migration
{
	public function up(){
		
		// Creamos la estructura de la nueva tabla usando la clase dbforge de Codeigniter
		$this->dbforge->add_field(
			array(
				"id" => array(
					"type" => "INT",
					"constraint" => 11,
					"unsigned" => TRUE,
					"auto_increment" => TRUE,
					"null" => FALSE
				),
				"list_number" => array(
					"type" => "INT",
					"constraint" => 11,
					"null" => TRUE
				),
				"list_type" => array(
					"type" => "VARCHAR",
					"constraint" => 20,
					"null" => TRUE
				),
				"date" => array(
					"type" => "TIMESTAMP",
					"null" => TRUE
				),
				"position" => array(
					"type" => "INT",
					"constraint" => 11,
					"null" => TRUE
				),
				"category" => array(
					"type" => "VARCHAR",
					"constraint" => 100,
					"null" => FALSE
				),
				"subcategory" => array(
					"type" => "VARCHAR",
					"constraint" => 200
				),
				"reference" => array(
					"type" => "VARCHAR",
					"constraint" => 100
				),
				"product" => array(
					"type" => "VARCHAR",
					"constraint" => 100,
					"null" => TRUE
				),
				"id_combination" => array(
					"type" => "INT",
					"constraint" => 11,
					"null" => TRUE
				),
				"material" => array(
					"type" => "VARCHAR",
					"constraint" => 100,
					"null" => TRUE
				),
				"price_minimal" => array(
					"type" => "FLOAT",
					"null" => TRUE
				),
				"price_cost" => array(
					"type" => "FLOAT",
					"null" => TRUE
				),
				"price_wholesaler" => array(
					"type" => "FLOAT",
					"null" => TRUE
				),
				"price_retail" => array(
					"type" => "FLOAT",
					"null" => TRUE
				)
			)
		);
		
		$this->dbforge->add_key('id', TRUE);  // Establecemos el id como primary_key
		
		$this->dbforge->create_table('pricelist', TRUE);
		
	}
	
	public function down(){
		
		// Eliminamos la tabla 'pricelist'
		$this->dbforge->drop_table('pricelist', TRUE);
		
	}
	
}
