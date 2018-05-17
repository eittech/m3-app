<?php

$this->pdf = new PdfInventario($orientation = 'L', $unit = 'mm', $format = 'A4');
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetMargins(8,8,8); # MÁRGENES DEL DOCUMENTO

// SECCIÓN DE CABECERAS DE PROVEEDOR Y CLIENTE
// Títulos
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','B',20);
$this->pdf->Ln(5);
$customer_name = utf8_decode($order['order'][0]['customer'][0]['firstname']." ".$order['order'][0]['customer'][0]['lastname']);
$this->pdf->Cell(63,5,$customer_name,0,0,'L',1);
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(63,5,utf8_decode("Dirección de Entrega"),0,0,'L',1);
$this->pdf->Cell(63,5,utf8_decode("Dirección de Facturación"),0,1,'L',1);

// Razón social
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',5);
$this->pdf->Cell(63,6,utf8_decode("Nombre o razón social: M3 Uniformes C.A."),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Nombre o razón social: ".$order['order'][0]['address_delivery'][0]['company']." CI o RIF: ".$order['order'][0]['address_delivery'][0]['dni']),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Nombre o razón social: Ibiza Venezuela, C.A. CI o RIF: J-40358416-8"),0,1,'L',1);

//~ $this->pdf->SetFont('Arial','',6);
//~ $texto = 'Av. Ppal El Castaño - # 131 - Maracay, Aragua. Municipio Girardot. 2101.';
//~ $this->pdf->SetY(25);
//~ $this->pdf->SetX(15);
//~ $this->pdf->MultiCell(63,3,utf8_decode($texto),0,'L',0);
//~ $texto = 'Av. Ppal El Castaño - # 131 - Maracay, Aragua. Municipio Girardot. 2101.';
//~ $this->pdf->SetY(20);
//~ $this->pdf->SetX(78);
//~ $this->pdf->MultiCell(63,3,utf8_decode($texto),0,'L',0);
//~ $texto = 'Av. Ppal El Castaño - # 131 - Maracay, Aragua. Municipio Girardot. 2101.';
//~ $this->pdf->SetY(20);
//~ $this->pdf->SetX(141);
//~ $this->pdf->MultiCell(63,3,utf8_decode($texto),0,'L',0);

// Dirección fiscal
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',5);
$this->pdf->Cell(63,4,utf8_decode("Dirección fiscal: Los Samanes, Maracay, estado Aragua, Maracay,"),0,0,'L',1);

// Recortamos la dirección de entrega si es muy larga
if(strlen($order['order'][0]['address_delivery'][0]['address1']) > 80){
	$direccion_entrega = substr($order['order'][0]['address_delivery'][0]['address1'], 0, 55);
}else{
	$direccion_entrega = $order['order'][0]['address_delivery'][0]['address1'];
}
$this->pdf->Cell(63,4,utf8_decode("Dirección fiscal: ".$direccion_entrega),0,0,'L',1);

// Recortamos la dirección de facturación si es muy larga
if(strlen($order['order'][0]['address_invoice'][0]['address1']) > 80){
	$direccion_factura = substr($order['order'][0]['address_invoice'][0]['address1'], 0, 55);
}else{
	$direccion_factura = $order['order'][0]['address_invoice'][0]['address1'];
}
$this->pdf->Cell(63,4,utf8_decode("Dirección fiscal: ".$direccion_factura),0,1,'L',1);

// Teléfono
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',6);
$this->pdf->Cell(63,4,utf8_decode("Teléfono: , 0412 311.23.08"),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Teléfono: ".$order['order'][0]['address_delivery'][0]['phone'].", ".$order['order'][0]['address_delivery'][0]['phone_mobile']),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Teléfono: ".$order['order'][0]['address_invoice'][0]['phone'].", ".$order['order'][0]['address_invoice'][0]['phone_mobile']),0,1,'L',1);


// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

$this->pdf->Ln(10);

// Preparación de las fechas de recepción y entrega
$fecha_re = date("d/m/Y");

// Títulos
$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(30,4,"REFERENCIA",'LT',0,'C',1);
$this->pdf->Cell(30,4,utf8_decode("FECHA DE RECEPCIÓN"),'T',0,'C',1);
$this->pdf->Cell(30,4,"FECHA DE ENTREGA",'T',0,'C',1);
$this->pdf->Cell(69,4,"TRANSPORTISTA",'T',0,'C',1);
$this->pdf->Cell(30,4,utf8_decode("Método de Pago"),'TR',1,'C',1);
// Contenido
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',6);
$this->pdf->Cell(30,4,$order['order'][0]['id_order']." - ".$order['order'][0]['reference'],'LB',0,'C',1);
$this->pdf->Cell(30,4,$order['order'][0]['invoice_date'],'B',0,'C',1);
$this->pdf->Cell(30,4,$order['order'][0]['delivery_date'],'B',0,'C',1);
$this->pdf->Cell(69,4,$order['order_carrier'][0]['carrier'][0]['name'],'B',0,'C',1);
$pay_method = $order['order_payment'][0]['payment_method'].': '.number_format((float)$order['order_payment'][0]['amount'], 2, ',', '.');
$this->pdf->Cell(30,4,$pay_method,'RB',1,'C',1);

// SECCIÓN DE LISTADO DE PRODUCTOS
$this->pdf->Ln(10);

$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(10,4,"ID.",'LTB',0,'C',1);
$this->pdf->Cell(20,4,"Referencia",'TB',0,'C',1);
$this->pdf->Cell(149,4,"Producto",'TB',0,'C',1);
$this->pdf->Cell(10,4,"Cant.",'TRB',1,'C',1);

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',6);
$j = 1;  // Contador de registros
$total = 0;  // Cantidad total

//~ // Registros de prueba
//~ $productos = array();
//~ $productos[0] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[1] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[2] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[3] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[4] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[5] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[6] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[7] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[8] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[9] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[10] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[11] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[12] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[13] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[14] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Clasica Corta Dama 3A- Mini Matt- 2XS","cant"=>7);
//~ $productos[15] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
//~ $productos[16] = array("id"=>1998,"referencia"=>"BA04-0","producto"=>"Bata Cuello Mao Bot- Microfibra, XL,XS,M,","cant"=>7);
	//~ 
//~ foreach($productos as $producto){
	//~ $this->pdf->Cell(10,4,"".$producto['id'],'LT',0,'C',1);
	//~ $this->pdf->Cell(20,4,utf8_decode("".$producto['referencia']),'T',0,'C',1);
	//~ $this->pdf->Cell(149,4,"".$producto['producto'],'T',0,'L',1);
	//~ $this->pdf->SetFont('Arial','B',6);
	//~ $this->pdf->Cell(10,4,"".$producto['cant'],'TR',1,'C',1);
	//~ $this->pdf->SetFont('Arial','',6);
	//~ 
	//~ $total += ($producto['cant']);
	//~ 
	//~ $j++;
//~ }

if(isset($order['order_detail']) && count($order['order_detail']) > 0){
	
	foreach($order['order_detail'] as $order_detail){
		$this->pdf->Cell(10,4,"".$order_detail['product_id'],'LT',0,'C',1);
		$this->pdf->Cell(20,4,utf8_decode("".$order_detail['product_reference']),'T',0,'C',1);
		$this->pdf->Cell(149,4,$order_detail['product_name'],'T',0,'L',1);
		$this->pdf->Cell(10,4,"".$order_detail['product_quantity'],'TR',1,'C',1);
		
		$total += ($order_detail['product_quantity']);
		
		$j++;
	}
	
}

// Total
$this->pdf->SetFillColor(204,204,204);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(30,6,"",'LB',0,'C',1);
$this->pdf->Cell(149,6,"Total de Productos",'B',0,'L',1);
$this->pdf->Cell(10,6,"".$total,'RB',1,'C',1);

// Salida del Formato PDF
$this->pdf->Output("order.pdf", 'I');
