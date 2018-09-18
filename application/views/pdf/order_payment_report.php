<?php

$this->pdf = new FPDF($orientation = 'P', $unit = 'mm', $format = 'A4');  // Instancando la clase FPDF original SÍ toma la horientación
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
$this->pdf->Image(base_url().'assets/img/logos/logotipo_320x130.png',3,7,35);
$this->pdf->Ln(30);


// Info de la empresa
$this->pdf->SetFont('Arial','',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetMargins(8,8,8); # MÁRGENES DEL DOCUMENTO


$this->pdf->Ln(-10);

$this->pdf->Cell(180,4,utf8_decode("Reporte de Pagos"),0,0,'C',0);
// SECCIÓN DE REFERENICA Y FECHAS DE LA ORDEN

$this->pdf->Ln(5);

// Preparación de las fechas de recepción y entrega
$fecha_re = date("d/m/Y");




// SECCIÓN DE LISTADO DE PRODUCTOS
$this->pdf->Ln(10);

$this->pdf->SetFillColor(240,240,240);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',8);
$this->pdf->Cell(25,7,"Fecha.",'LTB',0,'C',1);
$this->pdf->Cell(15,7,"ID.",'TB',0,'C',1);
$this->pdf->Cell(35,7,"Metodo de pago",'TB',0,'L',1);
$this->pdf->Cell(25,7,"Nro de Referencia",'TB',0,'L',1);
$this->pdf->Cell(25,7,"Monto",'TB',0,'C',1);
$this->pdf->Cell(60,7,"Observaciones",'TRB',1,'C',1);

foreach ($order_payment as $key => $value) {
	$this->pdf->SetFillColor(255,255,255);
	$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
	$this->pdf->SetFont('Arial','',7);
	$this->pdf->Cell(25,7,$value->date_add,'LTB',0,'C',1);
	$this->pdf->SetFont('Arial','',8);
	$this->pdf->Cell(15,7,$value->id_order_payment,'TB',0,'C',1);
	$this->pdf->Cell(35,7,$value->payment_method,'TB',0,'L',1);
	$this->pdf->Cell(25,7,$value->transaction_id,'TB',0,'L',1);
	$this->pdf->Cell(25,7,number_format((float)$value->amount, 2, ',', '.'),'TB',0,'C',1);
	$this->pdf->Cell(60,7,"",'LTBR',1,'C',1);
}

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(0,0,0); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',8);


// Dimensiones de X,Y
/*$this->pdf->SetFont('Arial','',9);
$this->pdf->SetY(15);
$this->pdf->SetX(124);
$this->pdf->Cell(40,4,"M3 Uniformes, C.A",'',1,'C',0);

$this->pdf->SetY(20);
$this->pdf->SetX(131);
$this->pdf->Cell(40,4,"Urb. Los Samanes C/1 N 343",'',1,'C',0);

$this->pdf->SetY(25);
$this->pdf->SetX(126);
$this->pdf->Cell(40,4,"Maracay  Edo. Aragua",'',0,'C',0);

$this->pdf->SetY(30);
$this->pdf->SetX(123);
$this->pdf->Cell(40,4,"Rif: J-31665970-4",'',0,'C',0);

$this->pdf->SetY(34);
$this->pdf->SetX(137);
$this->pdf->Cell(40,4,"e - mail : contacto@m3uniformes.com",'',0,'C',0);*/

// Salida del Formato PDF
$this->pdf->Output("Reporte de Pagos.pdf", 'I');
