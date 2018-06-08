<?php

$this->pdf = new PdfInventario($orientation = 'L', $unit = 'mm', $format = 'A4');
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BORDE DE LA CELDA
$this->pdf->SetTextColor(20,20,20); # COLOR DEL TEXTO
$this->pdf->SetMargins(15,15,10); # MÁRGENES DEL DOCUMENTO

$this->pdf->SetFillColor(255,255,255);
$this->pdf->Ln(0);

// Título
$this->pdf->SetFont('Arial','B',10);
$this->pdf->Cell(185,10,utf8_decode($product[0]->name),'',1,'L',1);
// Nombre
$this->pdf->SetFont('Arial','B',20);
$this->pdf->Cell(185,6,utf8_decode($product[0]->name),'',1,'L',1);
// Referencia
$this->pdf->SetFont('Arial','B',10);
$this->pdf->Cell(185,6,utf8_decode("REFERENCIA: ".$product[0]->reference),'',1,'L',1);
// ID
$this->pdf->SetFont('Arial','B',10);
$this->pdf->Cell(185,5,utf8_decode("ID: ".$product[0]->id_product),'',1,'L',1);

$this->pdf->Ln(2);

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','',10);
// Tallas
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"TALLAS:",'L',1,'L',1);
//~ $this->pdf->MultiCell(120,6,"",'','C',1);
$this->pdf->MultiCell(185,6,utf8_decode($tallas),'LTRB','R',1);
// Tela
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"TELA:",'L',1,'L',1);
$this->pdf->MultiCell(185,6,utf8_decode(""),'LTRB','R',1);
// Combinación
//~ $this->pdf->Cell(120,6,"",'',0,'C',1);
//~ $this->pdf->Cell(65,6,utf8_decode("COMBINACIÓN:"),'L',1,'L',1);
//~ $subcombinaciones = array();
//~ $combinaciones = explode("-", $combinaciones);
//~ $i = 0;
//~ foreach($combinaciones as $sub){
	//~ $i += 1; 
	//~ $subcombinacion = "";
	//~ $subcombinacion .= $sub."-";
	//~ if($i == 3){
		//~ echo $subcombinacion;
	//~ }
//~ }
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,utf8_decode("COMBINACIÓN:"),'L',1,'L',1);
$this->pdf->MultiCell(185,6,utf8_decode($combinaciones),'LTRB','R',1);
// Variable
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"VARIABLE:",'L',1,'L',1);
$this->pdf->MultiCell(185,6,utf8_decode($variables),'LTRB','R',1);
// Extra
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"EXTRA:",'L',1,'L',1);
$this->pdf->MultiCell(185,6,utf8_decode($extras),'LTRB','R',1);


// Número de pedido
$this->pdf->SetY(51);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(5,utf8_decode("Número de pedido:"),'',1,'C',0);
$this->pdf->SetY(55);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,"",'',1,'C',0);

// Fecha de pedido
$this->pdf->SetY(62);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(6,utf8_decode("Fecha de pedido:"),'',1,'R',0);
$this->pdf->SetY(66);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,"",'',1,'R',0);

// Método de pago
$this->pdf->SetY(73);
$this->pdf->SetTextColor(77,77,77); # COLOR DEL TEXTO
$this->pdf->SetFont('Arial','B',7);
$this->pdf->Write(5,utf8_decode("Método de pago:"),'',1,'C',0);
$this->pdf->SetY(77);
$this->pdf->SetFont('Arial','',8);
$this->pdf->Write(5,"",'',1,'C',0);

//~ $this->pdf->Cell(125,1,"",'',1,'R',1);  // Cierre de bloque de productos

// Salida del Formato PDF
$this->pdf->Output("catalogue.pdf", 'I');
