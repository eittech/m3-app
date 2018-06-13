<?php

$this->pdf = new PdfInventario($orientation = 'L', $unit = 'mm', $format = 'A4');
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetMargins(15,15,10); # MÁRGENES DEL DOCUMENTO

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','B',14);
$this->pdf->Ln(20);
$this->pdf->SetFont('Arial','',8);

// Preparación de la fecha actual
$fecha = date("d/m/Y");

// Fecha y número de factura
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(12,4,"Fecha: ",'',0,'L',1);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(18,4,"$fecha",'',0,'L',1);
$this->pdf->Cell(125,4,"",'',0,'L',1);
$this->pdf->SetFont('Arial','B',8);
if(isset($order['order_invoice']) && count($order['order_invoice']) > 0){
	$this->pdf->Cell(30,4,"FACTURA: ".$order['order_invoice'][0]['delivery_number'],'',1,'R',1);
}else{
	$this->pdf->Cell(30,4,"FACTURA: ",'',1,'R',1);
}
// Razón social
$this->pdf->Cell(32,4,utf8_decode("Nombre o razón social: "),'',0,'L',1);
$this->pdf->SetFont('Arial','',8);
if(isset($order['order'][0]['customer']) && count($order['order'][0]['customer']) > 0){
	$width_business_name = strlen($order['order'][0]['customer'][0]['company'])+15;  // De esta forma calculamos el espacio a asignarle a la celda (longitud de la cadena + 15)
	$this->pdf->Cell($width_business_name,4,utf8_decode($order['order'][0]['customer'][0]['company']),'',0,'L',1);
}else{
	$width_business_name = 0;
	$this->pdf->Cell($width_business_name,4,"",'',0,'L',1);
}
// Rif
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(13,4,"CI o RIF: ",'',0,'L',1);
$this->pdf->SetFont('Arial','',8);
if(isset($order['order'][0]['customer']) && count($order['order'][0]['customer']) > 0){
	$width_rif = strlen($order['order'][0]['customer'][0]['siret']);  // De esta forma calculamos el espacio a asignarle a la celda (longitud de la cadena + 15)
	$this->pdf->Cell($width_rif,4,$order['order'][0]['customer'][0]['siret'],'',1,'L',1);
}else{
	$width_rif = 0;
	$this->pdf->Cell($width_rif,4,"",'',1,'L',1);
}
// Dirección fiscal
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(23,4,utf8_decode("Dirección fiscal: "),'',0,'L',1);
$this->pdf->SetFont('Arial','',8);
$width_address = strlen($order['order'][0]['address_invoice'][0]['address1'])+30;  // De esta forma calculamos el espacio a asignarle a la celda (longitud de la cadena + 15)
$this->pdf->Cell($width_address,4,utf8_decode($order['order'][0]['address_invoice'][0]['address1']),'',0,'L',1);
// Número de teléfono
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(15,4,utf8_decode(" Teléfono: "),'',0,'L',1);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(15,4,$order['order'][0]['address_invoice'][0]['phone'],'',1,'L',1);

$this->pdf->Ln(10);

// Títulos
$this->pdf->Cell(30,4,utf8_decode(""),'',0,'C',1);
$this->pdf->SetFillColor(77,77,77);
$this->pdf->SetTextColor(255,255,255); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(20,4,utf8_decode("Cant."),'B',0,'C',1);
$this->pdf->Cell(85,4,"Producto / Referencia",'B',0,'L',1);
$this->pdf->Cell(25,4,"Precio unitario",'B',0,'R',1);
$this->pdf->Cell(25,4,"Total",'B',1,'R',1);

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',8);
$j = 1;  // Contador de registros
$subtotal = 0;  // Acumulador para el subtotal
$tasa_iva_decimals = explode(".", (string)number_format($order['order'][0]['carrier_tax_rate'], 2));
$tasa_iva_decimals = $tasa_iva_decimals[1];
if((int)$tasa_iva_decimals > 0){
	$tasa_iva = number_format($order['order'][0]['carrier_tax_rate'], 2);  // Tasa de impuesto de la orden
}else{
	$tasa_iva = number_format($order['order'][0]['carrier_tax_rate'], 0);  // Tasa de impuesto de la orden
}
$iva = 0;  // Monto en impuestos
$total = 0;  // Monto total

if(isset($order['order_detail']) && count($order['order_detail']) > 0){
	
	foreach($order['order_detail'] as $order_detail){
		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->Cell(30,5,"",'',0,'C',1);
		// Aplicamos una variación de color de fondo a las filas
		if($j >= 2 && $j%2 == 0){
			$this->pdf->SetFillColor(221,221,221);
		}else{
			$this->pdf->SetFillColor(255,255,255);
		}
		$this->pdf->Cell(20,6,"".$order_detail['product_quantity'],'',0,'C',1);
		if(strlen($order_detail['product_name']) > 57){
			$this->pdf->Cell(85,6,utf8_decode(substr($order_detail['product_name'], 0, 55)."..."),'',0,'L',1);
		}else{
			$this->pdf->Cell(85,6,utf8_decode($order_detail['product_name']),'',0,'L',1);
		}
		$this->pdf->Cell(25,6,"".number_format($order_detail['unit_price_tax_excl'], 2, ',', ' ')." Bs",'',0,'R',1);
		$this->pdf->Cell(25,6,"".number_format($order_detail['unit_price_tax_excl']*$order_detail['product_quantity'], 2, ',', ' ')." Bs",'',1,'R',1);
		
		$subtotal += ($order_detail['unit_price_tax_excl']*$order_detail['product_quantity']);
		
		$j++;
	}
	
}
// Subtotal
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(135,6,"",'',0,'C',1);
$this->pdf->Cell(25,6,"Subtotal",'',0,'R',1);
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(25,6,"".number_format($subtotal, 2, ',', ' ')." Bs",'',1,'R',1);
// IVA
$iva = $subtotal * (float)$tasa_iva / 100;
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(135,6,"",'',0,'C',1);
$this->pdf->Cell(25,6,"IVA(".$tasa_iva."%)",'',0,'R',1);
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(25,6,"".number_format($iva, 2, ',', ' ')." Bs",'',1,'R',1);
// Total
//~ $total = $subtotal + $iva;  // Monto anterior calculado desde el documento
$total = $order['order'][0]['total_paid_tax_incl'];
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(135,6,"",'',0,'C',1);
$this->pdf->Cell(25,6,"Total",'',0,'R',1);
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(25,6,"".number_format($total, 2, ',', ' ')." Bs",'',1,'R',1);


// Número de pedido
$this->pdf->SetY(51);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(5,utf8_decode("Número de pedido:"),'',1,'C',0);
$this->pdf->SetY(55);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,$order['order'][0]['reference'],'',1,'C',0);

// Conversión de la fecha del pedido
if(isset($order['order_invoice']) && count($order['order_invoice']) > 0){
	$fecha_pedido = explode(" ", $order['order_invoice'][0]['date_add']);
	$fecha_pedido = explode("-", $fecha_pedido[0]);
	$fecha_pedido = $fecha_pedido[2]."/".$fecha_pedido[1]."/".$fecha_pedido[0];
}else{
	$fecha_pedido = "";
}

// Fecha de pedido
$this->pdf->SetY(62);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(6,utf8_decode("Fecha de pedido:"),'',1,'R',0);
$this->pdf->SetY(66);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,$fecha_pedido,'',1,'R',0);

// Método de pago
$this->pdf->SetY(73);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(5,utf8_decode("Método de pago:"),'',1,'C',0);
$this->pdf->SetY(77);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,utf8_decode($order['order'][0]['payment']),'',1,'C',0);

//~ $this->pdf->Cell(125,1,"",'',1,'R',1);  // Cierre de bloque de productos

// Salida del Formato PDF
$this->pdf->Output("invoice.pdf", 'I');
