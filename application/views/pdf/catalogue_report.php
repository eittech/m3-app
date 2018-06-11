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
$this->pdf->Cell(65,6,"TALLAS:",'',1,'L',1);
// Dividimos las tallas en cadenas de 10 en 10, para que haga un salto de línea
$tallas = explode("-", $tallas);
$i = 0;
$subtalla = "";
foreach($tallas as $sub){
	$i += 1;
	$subtalla .= $sub."-";
	if($i == 10){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtalla),'',1,'L',1);
		$subtalla = "";
	}else if($i > 10 && $i%10 == 0){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtalla),'',1,'L',1);
		$subtalla = "";
	}else if($i > 10 && $i == count($tallas)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtalla),'',1,'L',1);
	}else if($i < 10 && $i == count($tallas)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtalla),'',1,'L',1);
	}
}
$this->pdf->Cell(185,6,"",'',1,'L',1);  // Salto de línea

// Tela
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"TELA:",'',1,'L',1);
// Dividimos las telas en cadenas de 4 en 4, para que haga un salto de línea
$telas = explode("-", $telas);
$j = 0;
$subtela = "";
foreach($telas as $sub){
	$j += 1;
	$subtela .= $sub."-";
	if($j == 4){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtela),'',1,'L',1);
		$subtela = "";
	}else if($j > 4 && $j%4 == 0){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtela),'',1,'L',1);
		$subtela = "";
	}else if($j > 4 && $j == count($telas)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtela),'',1,'L',1);
	}else if($j < 4 && $j == count($telas)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subtela),'',1,'L',1);
	}
}
$this->pdf->Cell(185,6,"",'',1,'L',1);  // Salto de línea

// Combinación
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,utf8_decode("COMBINACIÓN:"),'',1,'L',1);
// Dividimos las combinaciones en cadenas de 3 en 3, para que haga un salto de línea
$combinaciones = explode("-", $combinaciones);
$k = 0;
$subcombinacion = "";
foreach($combinaciones as $sub){
	$k += 1;
	$subcombinacion .= $sub."-";
	if($k == 3){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subcombinacion),'',1,'L',1);
		$subcombinacion = "";
	}else if($k > 3 && $k%3 == 0){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subcombinacion),'',1,'L',1);
		$subcombinacion = "";
	}else if($k > 3 && $k == count($combinaciones)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subcombinacion),'',1,'L',1);
	}else if($k < 3 && $k == count($combinaciones)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subcombinacion),'',1,'L',1);
	}
}
$this->pdf->Cell(185,6,"",'',1,'L',1);  // Salto de línea

// Variable
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"VARIABLE:",'',1,'L',1);
// Dividimos las variables en cadenas de 3 en 3, para que haga un salto de línea
$variables = explode("-", $variables);
$l = 0;
$subvariable = "";
foreach($variables as $sub){
	$l += 1;
	$subvariable .= $sub."-";
	if($l == 3){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subvariable),'',1,'L',1);
		$subvariable = "";
	}else if($l > 3 && $l%3 == 0){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subvariable),'',1,'L',1);
		$subvariable = "";
	}else if($l > 3 && $l == count($variables)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subvariable),'',1,'L',1);
	}else if($l < 3 && $l == count($variables)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subvariable),'',1,'L',1);
	}
}
$this->pdf->Cell(185,6,"",'',1,'L',1);  // Salto de línea

// Extra
$this->pdf->Cell(120,6,"",'',0,'C',1);
$this->pdf->Cell(65,6,"EXTRA:",'',1,'L',1);
// Dividimos las variables en cadenas de 3 en 3
$extras = explode("-", $extras);
$m = 0;
$subextra = "";
foreach($extras as $sub){
	$m += 1;
	$subextra .= $sub."-";
	if($m == 3){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subextra),'',1,'L',1);
		$subextra = "";
	}else if($m > 3 && $m%3 == 0){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subextra),'',1,'L',1);
		$subextra = "";
	}else if($m > 3 && $m == count($extras)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subextra),'',1,'L',1);
	}else if($m < 3 && $m == count($extras)){
		$this->pdf->Cell(120,6,"",'',0,'C',1);
		$this->pdf->Cell(65,6,utf8_decode($subextra),'',1,'L',1);
	}
}
$this->pdf->Cell(185,6,"",'',1,'L',1);  // Salto de línea

// La imagen siempre se debe cargar al final, de lo contrario se mostrará incorrectamente
$this->pdf->Image('assets/m3uniformes.jpg', 15, 40, 110, 100,'JPG', 'http://psadmin.m3uniformes.com');

// Salida del Formato PDF
$this->pdf->Output("catalogue.pdf", 'I');
