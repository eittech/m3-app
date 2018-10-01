<?php

#echo "<pre>";
#print_r($order_detail_where);
#echo "</pre>";
#exit;

$this->pdf = new PDF_HTML($orientation = 'P', $unit = 'mm', $format = 'A4');  // Instancando la clase FPDF original SÍ toma la horientación
$this->html2 = new PDF_HTML();  // Instancando la clase FPDF original SÍ toma la horientación
$this->pdf->SetFont('Arial','B',14);
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetMargins(8,8,8); # MÁRGENES DEL DOCUMENTO

// Logo tipo de empresa                                             # DI  AA AN
$this->pdf->Image(base_url().'assets/img/logos/logotipo_320x130.png',0,7,70);
$this->pdf->Ln(30);


// Info de la empresa
$this->pdf->SetFont('Arial','',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetMargins(8,8,8); # MÁRGENES DEL DOCUMENTO


$this->pdf->Ln(5);
$this->pdf->SetFont('Arial','B',14);
$this->pdf->Cell(180,4,utf8_decode("COTIZACIÓN"),0,0,'C',0);
// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

$this->pdf->Ln(15);

// Preparación de las fechas de recepción y entrega
$fecha_re = date("d/m/Y");

// Títulos
$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(61.5,7,"REFERENCIA",'LT',0,'C',1);
$this->pdf->Cell(61.5,7,utf8_decode("FECHA DE SOLICITUD"),'T',0,'C',1);
$this->pdf->Cell(61.5,7,"FECHA DE ENTREGA",'TR',1,'C',1);
//$this->pdf->Cell(100,4,"TRANSPORTISTA",'T',0,'C',1);
//$this->pdf->Cell(60,4,utf8_decode("Método de Pago"),'TR',1,'C',1);
// Contenido

$invoice_date = $order['order'][0]['date_add'];
$delivery_date = $order['order'][0]['delivery_date'];

if (($timestamp_one = strtotime($invoice_date)) === false) {
    $invoice_date_all = "";
} else {
	$invoice_date_all = date("d/m/Y", strtotime($order['order'][0]['date_add']));
}

if (($timestamp_one = strtotime($delivery_date)) === false) {
    $delivery_date_all = "";
} else {
	$delivery_date_all = date("d/m/Y", strtotime($order['order'][0]['delivery_date']));
}



$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Cell(61.5,4,$order['order'][0]['id_order']." - ".$order['order'][0]['reference'],'LB',0,'C',1);
$this->pdf->Cell(61.5,4,$invoice_date_all,'B',0,'C',1);
$this->pdf->Cell(61.5,4,$delivery_date_all,'RB',0,'C',1);
//$this->pdf->Cell(100,4,utf8_decode($order['order'][0]['carrier'][0]['name']),'B',0,'C',1);
$pay_method = "";
if(isset($order['order_payment'][0]['payment_method']) && count($order['order_payment'][0]['payment_method']) > 0){
	$pay_method = $order['order_payment'][0]['payment_method'].': '.number_format((float)$order['order_payment'][0]['amount'], 2, ',', '.');
}
//$this->pdf->Cell(60,4,$pay_method,'RB',1,'C',1);

// SECCIÓN DE LISTADO DE PRODUCTOS
$this->pdf->Ln(10);

$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(19,4,"Ref Prod.",'LTB',0,'C',1);
$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
//$this->pdf->Cell(20,4,"Referencia",'TB',0,'C',1);
$this->pdf->Cell(80,4,"Producto",'TB',0,'L',1);
$this->pdf->Cell(25,4,"Tela",'TB',0,'L',1);
/*
$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
$this->pdf->Cell(15,4,"Talla",'TB',0,'C',1);
$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
$this->pdf->Cell(30,4,"Extra",'TB',0,'C',1);*/
$this->pdf->Cell(25,4,"Precio Unitario",'TB',0,'C',1);
$this->pdf->Cell(25,4,"Precio Total",'TRB',1,'C',1);

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',8);
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

if(isset($order_detail_where) && count($order_detail_where) > 0){
	
	$j = 0;  # Contador para el salto de página
	
	foreach($order_detail_where as $order_detail){
		
		#SECCIÓN PARA EL SALTO DE PÁGINA CADA VEZ QUE IMPRIMA 25 REGISTROS (IMPRIMIMOS LAS REFERENCIAS Y LOS TÍTULOS)
		if ($j == 25){
		
			$this->pdf->AddPage();

			// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

			$this->pdf->Ln(10);

			// Preparación de las fechas de recepción y entrega
			$fecha_re = date("d/m/Y");

			// Títulos
			$this->pdf->SetFillColor(240,240,240);
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,4,"REFERENCIA",'LT',0,'C',1);
			$this->pdf->Cell(40,4,utf8_decode("FECHA DE RECEPCIÓN"),'T',0,'C',1);
			$this->pdf->Cell(40,4,"FECHA DE ENTREGA",'T',0,'C',1);
			$this->pdf->Cell(100,4,"TRANSPORTISTA",'T',0,'C',1);
			$this->pdf->Cell(60,4,utf8_decode("Método de Pago"),'TR',1,'C',1);
			// Contenido
			$this->pdf->SetFillColor(255,255,255);
			$this->pdf->SetFont('Arial','',6);
			$this->pdf->Cell(40,4,$order['order'][0]['id_order']." - ".$order['order'][0]['reference'],'LB',0,'C',1);
			$this->pdf->Cell(40,4,$order['order'][0]['invoice_date'],'B',0,'C',1);
			$this->pdf->Cell(40,4,$order['order'][0]['delivery_date'],'B',0,'C',1);
			$this->pdf->Cell(100,4,utf8_decode($order['order'][0]['carrier'][0]['name']),'B',0,'C',1);
			$pay_method = "";
			if(isset($order['order_payment'][0]['payment_method']) && count($order['order_payment'][0]['payment_method']) > 0){
				$pay_method = $order['order_payment'][0]['payment_method'].': '.number_format((float)$order['order_payment'][0]['amount'], 2, ',', '.');
			}
			$this->pdf->Cell(60,4,$pay_method,'RB',1,'C',1);

			// SECCIÓN DE LISTADO DE PRODUCTOS
			$this->pdf->Ln(10);

			$this->pdf->SetFillColor(240,240,240);
			$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(19,4,"Ref Prod.",'LTB',0,'C',1);
			$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
			$this->pdf->Cell(20,4,"Referencia",'TB',0,'C',1);
			$this->pdf->Cell(80,4,"Producto",'TB',0,'C',1);
			$this->pdf->Cell(25,4,"Tela",'TB',0,'L',1);
			/*
			$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
			$this->pdf->Cell(15,4,"Talla",'TB',0,'C',1);
			$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
			$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
			$this->pdf->Cell(30,4,"Extra",'TB',0,'C',1);
			*/
			$this->pdf->Cell(25,4,"Precio Unitario",'TB',0,'C',1);
			$this->pdf->Cell(25,4,"Precio Total",'TRB',1,'C',1);

			$this->pdf->SetFillColor(255,255,255);
			$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
			$this->pdf->SetFont('Arial','',6);
			
			$j = 0;
			
		}
		#CIERRE DE SECCIÓN PARA EL SALTO DE PÁGINA CADA VEZ QUE IMPRIMA 25 REGISTROS (IMPRIMIMOS LAS REFERENCIAS Y LOS TÍTULOS)
		
		// Separación de personalización
		$product_long_name = explode(",", $order_detail->product_name);
		
		$tela = "No Aplica";
		$pos1 = strpos($product_long_name[0], "Tela");
		if(!$pos1 === false){
			$tela = explode(" - ", $product_long_name[0]);
			$tela = explode(" : ", $tela[1]);
			$tela = $tela[1];
		}		
		
		$talla = "No Aplica";
		if(isset($product_long_name[1])){  // Si la división del nombre dio como resultado más de una parte
			$pos2 = strpos($product_long_name[1], "Talla");
			if(!$pos2 === false){
				$talla = explode(" : ", $product_long_name[1]);
				$talla = $talla[1];
			}
		}	
		
		// Si el nombre del producto es muy extenso, generamos dos filas para que quepa.
		if(strlen($order_detail->product_name) > 50){
			
			$this->pdf->Cell(19,4,"".$order_detail->product_reference,'LT',0,'C',1);
			$this->pdf->Cell(10,4,"".$order_detail->product_quantity,'T',0,'C',1);
			//$this->pdf->Cell(20,4,utf8_decode("".$order_detail['product_reference']),'T',0,'C',1);
			$this->pdf->Cell(80,4,utf8_decode(explode("-", $order_detail->product_name)[0]),'T',0,'L',1);
			$this->pdf->Cell(25,4,utf8_decode($tela),'T',0,'L',1);
			// Validación de atributo Color
			//$color = ""; if(isset($order_detail['Color'])){ $color = $order_detail['Color']; }else{ $color = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($color),'T',0,'C',1);
			// Validación de atributo Talla
			/*$talla = "";*/ //if(isset($order_detail['Talla'])){ $talla = $order_detail['Talla']; }/*else{ $talla = "No Aplica"; }*/
			//$this->pdf->Cell(15,4,utf8_decode($talla),'T',0,'C',1);
			// Validación de atributo Variable
			//$variable = ""; if(isset($order_detail['Variable'])){ $variable = $order_detail['Variable']; }else{ $variable = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($variable),'T',0,'C',1);
			// Validación de atributo Combinación
			//$combinacion = ""; if(isset($order_detail['Combinación'])){ $combinacion = $order_detail['Combinación']; }else{ $combinacion = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($combinacion),'T',0,'C',1);
			// Validación de atributo Extra
			//$extra = ""; if(isset($order_detail['Extra'])){ $extra = $order_detail['Extra']; }else{ $extra = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($extra),'T',0,'C',1);
			$this->pdf->Cell(25,4,"".number_format((float)$order_detail->unit_price_tax_excl, 2, ',', '.'),'T',0,'R',1);
			$this->pdf->Cell(25,4,"".number_format((float)$order_detail->unit_price_tax_excl*$order_detail->product_quantity, 2, ',', '.'),'TR',1,'R',1);
			
			$this->pdf->Cell(19,3,"",'LB',0,'C',1);
			$this->pdf->Cell(10,3,"",'B',0,'C',1);
			$this->pdf->Cell(20,3,"",'B',0,'C',1);
			$this->pdf->Cell(80,3,utf8_decode(explode("-", $order_detail->product_name)[0]),'B',0,'L',1);
			$this->pdf->Cell(25,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(15,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(25,3,"",'B',0,'C',1);
			$this->pdf->Cell(25,3,"",'BR',1,'C',1);
			
		}else{
			$this->pdf->Cell(19,4,"".$order_detail->product_reference,'LT',0,'C',1);
			$this->pdf->Cell(10,4,"".$order_detail->product_quantity,'T',0,'C',1);
			//$this->pdf->Cell(20,4,utf8_decode("".$order_detail['product_reference']),'T',0,'C',1);
			$this->pdf->Cell(80,4,utf8_decode(explode("-", $order_detail->product_name)[0]),'T',0,'L',1);
			$this->pdf->Cell(25,4,utf8_decode($tela),'T',0,'L',1);
			// Validación de atributo Color
			//$color = ""; if(isset($order_detail['Color'])){ $color = $order_detail['Color']; }else{ $color = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($color),'T',0,'C',1);
			// Validación de atributo Talla
			/*$talla = "";*/ //if(isset($order_detail['Talla'])){ $talla = $order_detail['Talla']; }/*else{ $talla = "No Aplica"; }*/
			//$this->pdf->Cell(15,4,utf8_decode($talla),'T',0,'C',1);
			// Validación de atributo Variable
			//$variable = ""; if(isset($order_detail['Variable'])){ $variable = $order_detail['Variable']; }else{ $variable = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($variable),'T',0,'C',1);
			// Validación de atributo Combinación
			//$combinacion = ""; if(isset($order_detail['Combinación'])){ $combinacion = $order_detail['Combinación']; }else{ $combinacion = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($combinacion),'T',0,'C',1);
			// Validación de atributo Extra
			//$extra = ""; if(isset($order_detail['Extra'])){ $extra = $order_detail['Extra']; }else{ $extra = "No Aplica"; }
			//$this->pdf->Cell(30,4,utf8_decode($extra),'T',0,'C',1);
			$this->pdf->Cell(25,4,"".number_format((float)$order_detail->unit_price_tax_excl, 2, ',', '.'),'T',0,'R',1);
			$this->pdf->Cell(25,4,"".number_format((float)$order_detail->unit_price_tax_excl*$order_detail->product_quantity, 2, ',', '.'),'TR',1,'R',1);
		}
		$total_cant += ($order_detail->product_quantity);
		$subtotal_price += ((float)$order_detail->unit_price_tax_excl*(float)$order_detail->product_quantity);
		
		$j++;
	}
	
}

// Subtotal
$this->pdf->SetFillColor(204,204,204);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7.5);
$this->pdf->Cell(18,6,"Cant. Total",'LB',0,'C',1);
$this->pdf->Cell(117,6,"      $total_cant",'B',0,'L',1);
$this->pdf->Cell(23.5,6,"Subtotal",'B',0,'R',1);
$this->pdf->Cell(25.5,6,"".number_format((float)$subtotal_price, 2, ',', '.'),'RB',1,'R',1);

// Variables de calculos
$total_discounts_tax_excl = $order['order'][0]['total_discounts_tax_excl'];
$sub_total_desc = (float)$subtotal_price - (float)$total_discounts_tax_excl;
$mount_discounts = $sub_total_desc * (float)$tasa_iva / 100;

if($subtotal_price > 0){
	
	$iva_discounts =  $total_discounts_tax_excl *100 / $subtotal_price;

	$this->pdf->SetFillColor(255,255,255);
	$this->pdf->SetFont('Arial','B',7.5);
	$this->pdf->Cell(18,6,"",'',0,'C',1);
	$this->pdf->Cell(117,6,"",'',0,'C',1);
	$this->pdf->SetFillColor(204,204,204);
	$this->pdf->Cell(24,6,"Descuento(".number_format($iva_discounts, 0, '', '')."%)",'LB',0,'R',1);
	$this->pdf->Cell(25,6,"-".number_format($total_discounts_tax_excl, 2, ',', '.'),'RB',1,'R',1);
	$this->pdf->Ln(0.1);
	// Descuento
	$this->pdf->SetFillColor(255,255,255);
	$this->pdf->SetFont('Arial','B',7.5);
	$this->pdf->Cell(18,6,"",'',0,'C',1);
	$this->pdf->Cell(117,6,"",'',0,'C',1);
	$this->pdf->SetFillColor(204,204,204);
	$this->pdf->Cell(23.5,6," Subtotal-Desc",'LB',0,'R',1);
	$this->pdf->Cell(25.5,6,"".number_format(str_replace("-","",$sub_total_desc), 2, ',', '.'),'RB',1,'R',1);
	$this->pdf->Ln(0.1);
}

// Iva
$iva = $subtotal_price * (float)$tasa_iva / 100;
/*$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(19,6,"",'',0,'C',1);
$this->pdf->Cell(10,6,"",'',0,'C',1);
$this->pdf->Cell(20,6,"",'',0,'C',1);
$this->pdf->Cell(41,6,"",'',0,'L',1);
$this->pdf->Cell(15,6,"",'',0,'L',1);
$this->pdf->Cell(30,6,"",'',0,'L',1);
$this->pdf->Cell(15,6,"",'',0,'L',1);
$this->pdf->Cell(30,6,"",'',0,'L',1);
$this->pdf->Cell(30,6,"",'',0,'L',1);
$this->pdf->Cell(30,6,"",'',0,'L',1);
$this->pdf->SetFillColor(204,204,204);
$this->pdf->Cell(20,6,"IVA(".$tasa_iva."%)",'LB',0,'C',1);
$this->pdf->Cell(20,6,"".number_format((float)$iva, 2, ',', '.'),'RB',1,'C',1);*/

// Total + Iva
//~ $total_price = $subtotal_price + $iva;  // Monto anterior calculado desde el documento

$total_price = $order['order'][0]['total_paid_tax_incl'];
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7.5);
$this->pdf->Cell(18,6,"",'',0,'C',1);
$this->pdf->Cell(117,6,"",'',0,'C',1);

if($mount_discounts > 0){
	$iva_total = $mount_discounts;
}else{
	$iva_total = $iva;
}
$this->pdf->SetFillColor(204,204,204);
$this->pdf->Cell(23.5,6," IVA(".$tasa_iva."%)",'LB',0,'R',1);
$this->pdf->Cell(25.5,6,"".number_format($iva_total, 2, ',', '.'),'RB',1,'R',1);
$this->pdf->Ln(0.3);


$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7.5);
$this->pdf->Cell(18,6,"",'',0,'C',1);
$this->pdf->Cell(117,6,"",'',0,'C',1);

$this->pdf->SetFillColor(204,204,204);
$this->pdf->Cell(23.5,6," Total",'LB',0,'R',1);
$this->pdf->Cell(25.5,6,"".number_format((float)$total_price, 2, ',', '.'),'RB',1,'R',1);

// Info para los Terminos y Condiciones
if(count($order_terms) > 0){
	$this->pdf->SetFont('Arial','B',9);
	//$this->pdf->MultiCell(180, 5, "CONDICIONES DE PAGO", 0, 'L', 0);
	//$this->pdf->MultiCell(180, 5, utf8_decode(str_replace("<br/>", "\n", $order_terms->terms)), 0, 'L', 0);
	$this->pdf->WriteHTML(utf8_decode($order_terms->terms)); //
}
$this->pdf->Image(base_url().'assets/img/logos/firms-estimate.png',60,200,80);

// Dimensiones de X,Y
$this->pdf->SetFont('Arial','',9);
$this->pdf->SetY(15);
$this->pdf->SetX(124);
$this->pdf->Cell(40,4,"M3 Uniformes, C.A",'',1,'C',0);

$this->pdf->SetY(20);
$this->pdf->SetX(123);
$this->pdf->Cell(40,4,"RIF: J316659704",'',1,'C',0);

$this->pdf->SetY(25);
$this->pdf->SetX(140);
$this->pdf->Cell(40,4,"Calle 1 Casa Nro. 343, Urb. Los Samanes,",'',0,'C',0);

$this->pdf->SetY(30);
$this->pdf->SetX(126);
$this->pdf->Cell(40,4,"Maracay, Edo. Aragua",'',0,'C',0);

$this->pdf->SetY(34);
$this->pdf->SetX(144.5);
$this->pdf->Cell(40,4,utf8_decode("Correo Electrónico: contacto@m3uniformes.com"),'',0,'C',0);

$id_order = $order['order'][0]['id_order'];

// Salida del Formato PDF
$this->pdf->Output("Cotizacion_$id_order.pdf", 'I');
