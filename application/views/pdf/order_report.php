<?php

#echo "<pre>";
#echo count($order['order_detail']);
#print_r($order['order_detail']);
#echo "</pre>";

#$array = array("Observaciones", "Bordado");
#var_export ($array);
#$indice = array_search(4,$array,true);
#echo "El número 5 está en el indice: " . $indice;

#exit;
$this->load->model('MOrders');



$this->pdf = new FPDF($orientation = 'L', $unit = 'mm', $format = 'A4');  // Instancando la clase FPDF original SÍ toma la horientación
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetMargins(8,8,8); # MÁRGENES DEL DOCUMENTO


$this->pdf->Image(base_url().'assets/img/logos/logotipo_320x130.png',255,7,35);

// SECCIÓN DE CABECERAS DE PROVEEDOR Y CLIENTE
// Nombre cliente
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','B',20);
$this->pdf->Ln(5);
$customer_name = utf8_decode($order['order'][0]['customer'][0]['firstname']." ".$order['order'][0]['customer'][0]['lastname']);
$this->pdf->Cell(189,5,$customer_name,0,1,'L',1);
$this->pdf->Ln(3);
//Títulos
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(90,5,"",0,0,'L',1);
$this->pdf->Cell(90,5,utf8_decode("Dirección de Entrega"),0,0,'L',1);
$this->pdf->Cell(90,5,utf8_decode("Dirección de Facturación"),0,1,'L',1);

// Razón social
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(90,6,utf8_decode(""),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("Nombre o razón social: ".$order['order'][0]['address_delivery'][0]['company']),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("Nombre o razón social: ".$order['order'][0]['address_invoice'][0]['company']),0,1,'L',1);

// RIF
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(90,4,utf8_decode(""),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("CI o RIF: ".$order['order'][0]['address_delivery'][0]['dni']),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("CI o RIF: ".$order['order'][0]['address_invoice'][0]['dni']),0,1,'L',1);

// Dirección fiscal
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(90,4,utf8_decode("Nombre o razón social: M3 Uniformes C.A."),0,0,'L',1);

// Recortamos la dirección de entrega si es muy larga
if(strlen($order['order'][0]['address_delivery'][0]['address1']) > 90){
	$direccion_entrega = substr($order['order'][0]['address_delivery'][0]['address1'], 0, 55);
}else{
	$direccion_entrega = $order['order'][0]['address_delivery'][0]['address1'];
}
$this->pdf->Cell(90,4,utf8_decode("Dirección fiscal: ".$direccion_entrega),0,0,'L',1);

// Recortamos la dirección de facturación si es muy larga
if(strlen($order['order'][0]['address_invoice'][0]['address1']) > 90){
	$direccion_factura = substr($order['order'][0]['address_invoice'][0]['address1'], 0, 55);
}else{
	$direccion_factura = $order['order'][0]['address_invoice'][0]['address1'];
}
$this->pdf->Cell(100,4,utf8_decode("Dirección fiscal: ".$direccion_factura),0,1,'L',1);
//$this->pdf->MultiCell(90, 4, utf8_decode("Dirección fiscal: ".$direccion_factura), 0, 'C', 1);


// Generamos una línea más para las direcciones principales si éstas superan el límite de 55 caracteres
if(strlen($order['order'][0]['address_delivery'][0]['address1']) > 55 || strlen($order['order'][0]['address_invoice'][0]['address1']) > 55){
	$this->pdf->Cell(90,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(substr($order['order'][0]['address_delivery'][0]['address1'], 55)),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(substr($order['order'][0]['address_invoice'][0]['address1'], 55)),0,1,'L',1);
}

// Imprimimos las direcciones secundarias si existe alguna
if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 0 || strlen($order['order'][0]['address_invoice'][0]['address2']) > 0){
	
	$this->pdf->Cell(90,4,utf8_decode("Dirección fiscal: Los Samanes, Maracay, estado Aragua, Maracay,"),0,0,'L',1);
	
	// Recortamos la dirección de entrega secundaria si es muy larga
	if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 90){
		$direccion_entrega = substr($order['order'][0]['address_delivery'][0]['address2'], 0, 90);
	}else{
		$direccion_entrega = $order['order'][0]['address_delivery'][0]['address2'];
	}
	$this->pdf->Cell(90,4,utf8_decode($direccion_entrega),0,0,'L',1);	
	
	// Recortamos la dirección de facturación secundaria si es muy larga
	if(strlen($order['order'][0]['address_invoice'][0]['address2']) > 90){
		$direccion_factura = substr($order['order'][0]['address_invoice'][0]['address2'], 0, 90);
	}else{
		$direccion_factura = $order['order'][0]['address_invoice'][0]['address2'];
	}
	$this->pdf->Cell(90,4,utf8_decode($direccion_factura),0,1,'L',1);
	
}else{
	$this->pdf->Cell(90,4,utf8_decode("Dirección fiscal: Los Samanes, Maracay, estado Aragua, Maracay,"),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(""),0,1,'L',1);
}


// Generamos una línea más para las direcciones secundarias si éstas superan el límite de 90 caracteres
if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 90 || strlen($order['order'][0]['address_invoice'][0]['address2']) > 90){
	$this->pdf->Cell(90,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(substr($order['order'][0]['address_delivery'][0]['address2'], 90)),0,0,'L',1);
	$this->pdf->Cell(90,4,utf8_decode(substr($order['order'][0]['address_invoice'][0]['address2'], 90)),0,1,'L',1);
}

// Teléfono
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(90,4,utf8_decode("Teléfono: , 0412 311.23.08"),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("Teléfono: ".$order['order'][0]['address_delivery'][0]['phone'].", ".$order['order'][0]['address_delivery'][0]['phone_mobile']),0,0,'L',1);
$this->pdf->Cell(90,4,utf8_decode("Teléfono: ".$order['order'][0]['address_invoice'][0]['phone'].", ".$order['order'][0]['address_invoice'][0]['phone_mobile']),0,1,'L',1);


// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

$this->pdf->Ln(10);

// Preparación de las fechas de recepción y entrega
$fecha_re = date("d/m/Y");

$invoice_date_all;
$delivery_date_all;

if (($timestamp_one = strtotime($order['order'][0]['date_add'])) === false) {
   $invoice_date_all = "";
} else {
    $invoice_date_all = date("d/m/Y", strtotime($order['order'][0]['date_add']));
}

if (($timestamp_one = strtotime($order['order'][0]['delivery_date'])) === false) {
   $delivery_date_all = "";
} else {
    $delivery_date_all = date("d/m/Y", strtotime($order['order'][0]['delivery_date']));
}

// Títulos
$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetFont('Arial','B',10);
$this->pdf->Cell(50,4,"REFERENCIA",'LT',0,'C',1);
$this->pdf->Cell(50,4,utf8_decode("FECHA DE RECEPCIÓN"),'T',0,'C',1);
$this->pdf->Cell(50,4,"FECHA DE ENTREGA",'T',0,'C',1);
$this->pdf->Cell(80,4,"TRANSPORTISTA",'T',0,'C',1);
$this->pdf->Cell(50,4,utf8_decode("Método de Pago"),'TR',1,'C',1);
// Contenido
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',10);
$this->pdf->Cell(50,4,$order['order'][0]['id_order']." - ".$order['order'][0]['reference'],'LB',0,'C',1);
$this->pdf->Cell(50,4,$invoice_date_all,'B',0,'C',1);
$this->pdf->Cell(50,4,$delivery_date_all,'B',0,'C',1);
$this->pdf->Cell(80,4,utf8_decode($order['order'][0]['carrier'][0]['name']),'B',0,'C',1);
$pay_method = "";
if(isset($order['order'][0]['payment']) && count($order['order'][0]['payment']) > 0){
	#$pay_method = $order['order'][0]['payment'].': '.number_format((float)$order['order'][0]['amount'], 2, ',', '.');
	$pay_method = $order['order'][0]['payment'];
}
$this->pdf->Cell(50,4,$pay_method,'RB',1,'C',1);

// SECCIÓN DE LISTADO DE PRODUCTOS
$this->pdf->Ln(10);

$total_cant = 0;  // Cantidad total
$subtotal_price = 0;  // Precio total

// Tasa de impuesto
$tasa_iva_decimals = explode(".", (string)number_format($order['order'][0]['carrier_tax_rate'], 2));
$tasa_iva_decimals = $tasa_iva_decimals[1];
if((int)$tasa_iva_decimals > 0){
	$tasa_iva = number_format($order['order'][0]['carrier_tax_rate'], 2);  // Tasa de impuesto de la orden
}else{
	$tasa_iva = number_format($order['order'][0]['carrier_tax_rate'], 0);  // Tasa de impuesto de la orden
}
$iva = 0;  // Monto en impuestos

$total_price = 0;  // Precio total

// Comienzo de impresion de datos para los productos y sus detalles

$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',9);

$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',9);

$this->pdf->Cell(15,4,"ID.",'LTB',0,'C',1);
$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
$this->pdf->Cell(110,4,"Producto",'TB',0,'C',1);
$this->pdf->Cell(15,4,"Tela",'TB',0,'C',1);
$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
$this->pdf->Cell(10,4,"Talla",'TB',0,'C',1);
$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
$this->pdf->Cell(30,4,"Extra",'TBR',1,'C',1);

if(isset($order['order_detail']) && count($order['order_detail']) > 0){
	$j = 0;  # Contador para el salto de página
	
	$i = 1;  # Contador de productos
	foreach($order['order_detail'] as $order_detail){

		$total_cant += ($order_detail['product_quantity']);

		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
		$this->pdf->SetFont('Arial','',9);

		$product_long_name = explode(",", $order_detail['product_name']);

		$tela = "No Aplica";
		$pos1 = strpos($product_long_name[0], "Tela");
		if(!$pos1 === false){
			$tela = explode(" - ", $product_long_name[0]);
			$tela = explode(" : ", $tela[1]);
			$tela = $tela[1];
		}

		$color = "";
		if(isset($order_detail->color)){
			$color = $order_detail->color;
		}else{
			$color = "No Aplica";
		}

		$talla = "";
		if($order_detail['Talla'] !=""){
			$talla = $order_detail['Talla'];
		}else{
			$talla = "No Aplica";
		}

		$Observaciones = "";
		$Bordado = "";
		if(isset($order_detail['Observaciones'])){
			$Observaciones = "Observaciones: ".TRIM($order_detail['Observaciones']);

		}if(isset($order_detail['Bordado'])){
			$Bordado = "Bordado: ".TRIM($order_detail['Bordado']);
		}

		$variable = "";
		if(isset($order_detail['Variable'])){
			$variable = $order_detail['Variable'];
		}else{
			$variable = "No Aplica";
		}

		$combinacion = "";
		if(isset($order_detail['Combinación'])){
			$combinacion = $order_detail['Combinación'];
		}else{
			$combinacion = "No Aplica";
		}

		$extra = "";
		if(isset($order_detail['Extra'])){
			$extra = $order_detail['Extra'];
		}else{
			$extra = "No Aplica";
		}

		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
		$this->pdf->SetFont('Arial','',9);

		// Impresion de datos
		$this->pdf->Cell(15,4,$i,'LTB',0,'C',1);
		$this->pdf->Cell(10,4,$order_detail['product_quantity'],'TB',0,'C',1);
		$this->pdf->Cell(110,4,utf8_decode(explode("-",$order_detail['product_name'])[0]),'TB',0,'C',1);
		$this->pdf->Cell(15,4,utf8_decode($tela),'TB',0,'C',1);
		$this->pdf->Cell(30,4,utf8_decode($color),'TB',0,'C',1);
		$this->pdf->Cell(10,4,utf8_decode($talla),'TB',0,'C',1);
		$this->pdf->Cell(30,4,utf8_decode($variable),'TB',0,'C',1);
		$this->pdf->Cell(30,4,utf8_decode($combinacion),'TB',0,'C',1);
		$this->pdf->Cell(30,4,utf8_decode($extra),'TBR',1,'C',1);
		$this->pdf->Ln(0);
		$this->pdf->SetFont('Arial','',9);

		$id_customization = $order_detail['id_customization']; # ID de id_customization

		$cus_obj = $this->MOrders->get_customization($id_customization);

		$string_customized = "";
		foreach ($cus_obj as $key => $value) {

			if($key == 0){
				$etxt = "Observaciones:";
			}if($key == 1){
				$etxt = "Bordado:";
			}

			$string_customized .= $etxt." ".$value->value."\n";
		}
		$replace_string = str_replace("Observaciones: .", "", $string_customized);

		if(count($cus_obj) > 0){
			$this->pdf->MultiCell(280, 4, utf8_decode($replace_string),"LTBR",1, "L", 1);
		}

		/*$obj_custom = $this->db->query("select a.value from customized_data AS a where a.id_customization = $id_customization order by a.id_customization ASC LIMIT 5,6");
		$return_customized = $obj_custom->result();

		$string_customized = "";
		$replace_text = "";
		foreach ($return_customized as $key => $value) {
			$string_customized .= "Observaciones: ".$value->value."\n";
		}

		$replace_text = $this->MOrders->replace_text("Observaciones","Bordado",$string_customized);
		$replace_string = str_replace("Bordado: .", "", $replace_text);
		$this->pdf->MultiCell(280, 4, utf8_decode($replace_string),"LTBR",1, "L", 1);
		/*$string_customized = "";
		$replace_text = "";
		foreach ($return_customized as $key => $value) {
			$string_customized .= $value->value." |";
		}
		$customized_data = "Observaciones: ".$string_customized;
		$replace_string = str_replace("Observaciones: . |", "", $customized_data);
		$this->pdf->MultiCell(280, 4, utf8_decode($replace_string),"LTBR",1, "L", 1);*/

		$j++;
		
		$i++;
	}
	/*foreach(range(1, 30) as $order_detail){
		$this->pdf->Cell(15,4,"ID.",'LTB',0,'C',1);
		$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
		$this->pdf->Cell(110,4,"Producto",'TB',0,'C',1);
		$this->pdf->Cell(15,4,"Tela",'TB',0,'C',1);
		$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
		$this->pdf->Cell(10,4,"Talla",'TB',0,'C',1);
		$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
		$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
		$this->pdf->Cell(30,4,"Extra",'TBR',1,'C',1);
		$this->pdf->Cell(280,4,"Observaciones",'LTBR',1,'C',1);
		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->MultiCell(280, 4, utf8_decode("Texto de Observaciones"),"LTBR",1, "L", 1);
	}*/
}

// Descuento
$iva = $subtotal_price * (float)$tasa_iva / 100;
$total_discounts_tax_excl = $order['order'][0]['total_discounts_tax_excl'];
$sub_total_desc = (float)$subtotal_price - (float)$total_discounts_tax_excl;
$mount_discounts = $sub_total_desc * (float)$tasa_iva / 100;
$total_price = $order['order'][0]['total_paid_tax_incl'];  // Monto anterior calculado desde el documento

// Subtotal
$this->pdf->SetFillColor(204,204,204);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',9);

//$this->pdf->Cell(220,6,"Cant. Total ".$total_cant,'LRTB',0,'L',1);
//$this->pdf->Cell(60,6,"Total   ".number_format((float)$total_price, 2, ',', '.')." Bs",'RTB',0,'R',1);

$this->pdf->Cell(220,6,"Cant. Total ".$total_cant,'LTBR',0,'L',1);
$this->pdf->Cell(60,6,"Total ".number_format((float)$total_price, 2, ',', '.')." Bs ",'TBR',0,'R',1);


// Iva
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',9);
$this->pdf->Cell(25,6,"",'',0,'C',1);


$name_doc = "Pedido_".str_pad($order['order'][0]['id_order'], 6, "0", STR_PAD_LEFT).".pdf";

// Salida del Formato PDF
$this->pdf->Output($name_doc, 'I');
