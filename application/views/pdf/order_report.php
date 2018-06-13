<?php

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

// SECCIÓN DE CABECERAS DE PROVEEDOR Y CLIENTE
// Nombre cliente
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','B',20);
$this->pdf->Ln(5);
$customer_name = utf8_decode($order['order'][0]['customer'][0]['firstname']." ".$order['order'][0]['customer'][0]['lastname']);
$this->pdf->Cell(189,5,$customer_name,0,1,'L',1);
$this->pdf->Ln(3);
//Títulos
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(63,5,"",0,0,'L',1);
$this->pdf->Cell(63,5,utf8_decode("Dirección de Entrega"),0,0,'L',1);
$this->pdf->Cell(63,5,utf8_decode("Dirección de Facturación"),0,1,'L',1);

// Razón social
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',5);
$this->pdf->Cell(63,6,utf8_decode(""),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Nombre o razón social: ".$order['order'][0]['address_delivery'][0]['company']),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("Nombre o razón social: ".$order['order'][0]['address_invoice'][0]['company']),0,1,'L',1);

// RIF
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',5);
$this->pdf->Cell(63,4,utf8_decode(""),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("CI o RIF: ".$order['order'][0]['address_delivery'][0]['dni']),0,0,'L',1);
$this->pdf->Cell(63,4,utf8_decode("CI o RIF: ".$order['order'][0]['address_invoice'][0]['dni']),0,1,'L',1);

// Dirección fiscal
$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','',5);
$this->pdf->Cell(63,4,utf8_decode("Nombre o razón social: M3 Uniformes C.A."),0,0,'L',1);

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


// Generamos una línea más para las direcciones principales si éstas superan el límite de 55 caracteres
if(strlen($order['order'][0]['address_delivery'][0]['address1']) > 55 || strlen($order['order'][0]['address_invoice'][0]['address1']) > 55){
	$this->pdf->Cell(63,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(substr($order['order'][0]['address_delivery'][0]['address1'], 55)),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(substr($order['order'][0]['address_invoice'][0]['address1'], 55)),0,1,'L',1);
}

// Imprimimos las direcciones secundarias si existe alguna
if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 0 || strlen($order['order'][0]['address_invoice'][0]['address2']) > 0){
	
	$this->pdf->Cell(63,4,utf8_decode("Dirección fiscal: Los Samanes, Maracay, estado Aragua, Maracay,"),0,0,'L',1);
	
	// Recortamos la dirección de entrega secundaria si es muy larga
	if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 80){
		$direccion_entrega = substr($order['order'][0]['address_delivery'][0]['address2'], 0, 70);
	}else{
		$direccion_entrega = $order['order'][0]['address_delivery'][0]['address2'];
	}
	$this->pdf->Cell(63,4,utf8_decode($direccion_entrega),0,0,'L',1);	
	
	// Recortamos la dirección de facturación secundaria si es muy larga
	if(strlen($order['order'][0]['address_invoice'][0]['address2']) > 80){
		$direccion_factura = substr($order['order'][0]['address_invoice'][0]['address2'], 0, 70);
	}else{
		$direccion_factura = $order['order'][0]['address_invoice'][0]['address2'];
	}
	$this->pdf->Cell(63,4,utf8_decode($direccion_factura),0,1,'L',1);
	
}else{
	$this->pdf->Cell(63,4,utf8_decode("Dirección fiscal: Los Samanes, Maracay, estado Aragua, Maracay,"),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(""),0,1,'L',1);
}


// Generamos una línea más para las direcciones secundarias si éstas superan el límite de 70 caracteres
if(strlen($order['order'][0]['address_delivery'][0]['address2']) > 70 || strlen($order['order'][0]['address_invoice'][0]['address2']) > 70){
	$this->pdf->Cell(63,4,utf8_decode(""),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(substr($order['order'][0]['address_delivery'][0]['address2'], 70)),0,0,'L',1);
	$this->pdf->Cell(63,4,utf8_decode(substr($order['order'][0]['address_invoice'][0]['address2'], 70)),0,1,'L',1);
}

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
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(19,4,"ID.",'LTB',0,'C',1);
$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
$this->pdf->Cell(20,4,"Referencia",'TB',0,'C',1);
$this->pdf->Cell(41,4,"Producto",'TB',0,'C',1);
$this->pdf->Cell(15,4,"Tela",'TB',0,'C',1);
$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
$this->pdf->Cell(15,4,"Talla",'TB',0,'C',1);
$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
$this->pdf->Cell(30,4,"Extra",'TB',0,'C',1);
$this->pdf->Cell(20,4,"Precio Unitario",'TB',0,'C',1);
$this->pdf->Cell(20,4,"Precio Total",'TRB',1,'C',1);

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',6);
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

if(isset($order['order_detail']) && count($order['order_detail']) > 0){
	
	$j = 0;  # Contador para el salto de página
	
	foreach($order['order_detail'] as $order_detail){
		
		#SECCIÓN PARA EL SALTO DE PÁGINA CADA VEZ QUE IMPRIMA 25 REGISTROS (IMPRIMIMOS LAS REFERENCIAS Y LOS TÍTULOS)
		if ($j == 25){
		
			$this->pdf->AddPage();

			// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

			$this->pdf->Ln(10);

			// Preparación de las fechas de recepción y entrega
			$fecha_re = date("d/m/Y");

			// Títulos
			$this->pdf->SetFillColor(240,240,240);
			$this->pdf->SetFont('Arial','B',6);
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
			$this->pdf->SetFont('Arial','B',6);
			$this->pdf->Cell(19,4,"ID.",'LTB',0,'C',1);
			$this->pdf->Cell(10,4,"Cant.",'TB',0,'C',1);
			$this->pdf->Cell(20,4,"Referencia",'TB',0,'C',1);
			$this->pdf->Cell(41,4,"Producto",'TB',0,'C',1);
			$this->pdf->Cell(15,4,"Tela",'TB',0,'C',1);
			$this->pdf->Cell(30,4,"Color",'TB',0,'C',1);
			$this->pdf->Cell(15,4,"Talla",'TB',0,'C',1);
			$this->pdf->Cell(30,4,"Variable",'TB',0,'C',1);
			$this->pdf->Cell(30,4,utf8_decode("Combinación"),'TB',0,'C',1);
			$this->pdf->Cell(30,4,"Extra",'TB',0,'C',1);
			$this->pdf->Cell(20,4,"Precio Unitario",'TB',0,'C',1);
			$this->pdf->Cell(20,4,"Precio Total",'TRB',1,'C',1);

			$this->pdf->SetFillColor(255,255,255);
			$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
			$this->pdf->SetFont('Arial','',6);
			
			$j = 0;
			
		}
		#CIERRE DE SECCIÓN PARA EL SALTO DE PÁGINA CADA VEZ QUE IMPRIMA 25 REGISTROS (IMPRIMIMOS LAS REFERENCIAS Y LOS TÍTULOS)
		
		// Separación de personalización
		$product_long_name = explode(",", $order_detail['product_name']);
		
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
		if(strlen($order_detail['product_short_name']) > 50){
			
			$this->pdf->Cell(19,4,"".$order_detail['product_id'],'LT',0,'C',1);
			$this->pdf->Cell(10,4,"".$order_detail['product_quantity'],'T',0,'C',1);
			$this->pdf->Cell(20,4,utf8_decode("".$order_detail['product_reference']),'T',0,'C',1);
			$this->pdf->Cell(41,4,utf8_decode(substr($order_detail['product_short_name'], 0, 50)),'T',0,'L',1);
			$this->pdf->Cell(15,4,utf8_decode($tela),'T',0,'C',1);
			// Validación de atributo Color
			$color = ""; if(isset($order_detail['Color'])){ $color = $order_detail['Color']; }else{ $color = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($color),'T',0,'C',1);
			// Validación de atributo Talla
			/*$talla = "";*/ if(isset($order_detail['Talla'])){ $talla = $order_detail['Talla']; }/*else{ $talla = "No Aplica"; }*/
			$this->pdf->Cell(15,4,utf8_decode($talla),'T',0,'C',1);
			// Validación de atributo Variable
			$variable = ""; if(isset($order_detail['Variable'])){ $variable = $order_detail['Variable']; }else{ $variable = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($variable),'T',0,'C',1);
			// Validación de atributo Combinación
			$combinacion = ""; if(isset($order_detail['Combinación'])){ $combinacion = $order_detail['Combinación']; }else{ $combinacion = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($combinacion),'T',0,'C',1);
			// Validación de atributo Extra
			$extra = ""; if(isset($order_detail['Extra'])){ $extra = $order_detail['Extra']; }else{ $extra = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($extra),'T',0,'C',1);
			$this->pdf->Cell(20,4,"".number_format((float)$order_detail['unit_price_tax_excl'], 2, ',', '.'),'T',0,'C',1);
			$this->pdf->Cell(20,4,"".number_format((float)$order_detail['unit_price_tax_excl']*$order_detail['product_quantity'], 2, ',', '.'),'TR',1,'C',1);
			
			$this->pdf->Cell(19,3,"",'LB',0,'C',1);
			$this->pdf->Cell(10,3,"",'B',0,'C',1);
			$this->pdf->Cell(20,3,"",'B',0,'C',1);
			$this->pdf->Cell(41,3,utf8_decode(substr($order_detail['product_short_name'], 50)),'B',0,'L',1);
			$this->pdf->Cell(15,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(15,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(30,3,utf8_decode(""),'B',0,'L',1);
			$this->pdf->Cell(20,3,"",'B',0,'C',1);
			$this->pdf->Cell(20,3,"",'BR',1,'C',1);
			
		}else{
			$this->pdf->Cell(19,4,"".$order_detail['product_id'],'LT',0,'C',1);
			$this->pdf->Cell(10,4,"".$order_detail['product_quantity'],'T',0,'C',1);
			$this->pdf->Cell(20,4,utf8_decode("".$order_detail['product_reference']),'T',0,'C',1);
			$this->pdf->Cell(41,4,utf8_decode($order_detail['product_short_name']),'T',0,'L',1);
			$this->pdf->Cell(15,4,utf8_decode($tela),'T',0,'C',1);
			// Validación de atributo Color
			$color = ""; if(isset($order_detail['Color'])){ $color = $order_detail['Color']; }else{ $color = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($color),'T',0,'C',1);
			// Validación de atributo Talla
			/*$talla = "";*/ if(isset($order_detail['Talla'])){ $talla = $order_detail['Talla']; }/*else{ $talla = "No Aplica"; }*/
			$this->pdf->Cell(15,4,utf8_decode($talla),'T',0,'C',1);
			// Validación de atributo Variable
			$variable = ""; if(isset($order_detail['Variable'])){ $variable = $order_detail['Variable']; }else{ $variable = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($variable),'T',0,'C',1);
			// Validación de atributo Combinación
			$combinacion = ""; if(isset($order_detail['Combinación'])){ $combinacion = $order_detail['Combinación']; }else{ $combinacion = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($combinacion),'T',0,'C',1);
			// Validación de atributo Extra
			$extra = ""; if(isset($order_detail['Extra'])){ $extra = $order_detail['Extra']; }else{ $extra = "No Aplica"; }
			$this->pdf->Cell(30,4,utf8_decode($extra),'T',0,'C',1);
			$this->pdf->Cell(20,4,"".number_format((float)$order_detail['unit_price_tax_excl'], 2, ',', '.'),'T',0,'C',1);
			$this->pdf->Cell(20,4,"".number_format((float)$order_detail['unit_price_tax_excl']*$order_detail['product_quantity'], 2, ',', '.'),'TR',1,'C',1);
		}
		$total_cant += ($order_detail['product_quantity']);
		$subtotal_price += ($order_detail['unit_price_tax_excl']*$order_detail['product_quantity']);
		
		$j++;
	}
	
}

// Subtotal
$this->pdf->SetFillColor(204,204,204);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',6);
$this->pdf->Cell(19,6,"Cant. Total",'LB',0,'C',1);
$this->pdf->Cell(10,6,"".$total_cant,'B',0,'C',1);
$this->pdf->Cell(20,6,"",'B',0,'C',1);
$this->pdf->Cell(41,6,"",'B',0,'L',1);
$this->pdf->Cell(15,6,"",'B',0,'L',1);
$this->pdf->Cell(30,6,"",'B',0,'L',1);
$this->pdf->Cell(15,6,"",'B',0,'L',1);
$this->pdf->Cell(30,6,"",'B',0,'L',1);
$this->pdf->Cell(30,6,"",'B',0,'L',1);
$this->pdf->Cell(30,6,"",'B',0,'L',1);
$this->pdf->Cell(20,6,"Subtotal",'B',0,'C',1);
$this->pdf->Cell(20,6,"".number_format((float)$subtotal_price, 2, ',', '.'),'RB',1,'C',1);

// Iva
$iva = $subtotal_price * (float)$tasa_iva / 100;
$this->pdf->SetFillColor(255,255,255);
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
$this->pdf->Cell(20,6,"".number_format((float)$iva, 2, ',', '.'),'RB',1,'C',1);

// Total + Iva
//~ $total_price = $subtotal_price + $iva;  // Monto anterior calculado desde el documento
$total_price = $order['order'][0]['total_paid_tax_incl'];
$this->pdf->SetFillColor(255,255,255);
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
$this->pdf->Cell(20,6,"Total",'LB',0,'C',1);
$this->pdf->Cell(20,6,"".number_format((float)$total_price, 2, ',', '.'),'RB',1,'C',1);

// Salida del Formato PDF
$this->pdf->Output("order.pdf", 'I');
