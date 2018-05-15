<?php

$this->pdf   = new Pdf($orientation = 'L', $unit = 'mm', $format = 'A4');
// Agregamos una página
$this->pdf->AddPage();
// Define el alias para el número de página que se imprimirá en el pie
$this->pdf->AliasNbPages();

//~ $this->pdf->AliasNbPages() # LLAMADA DE PAGINACIÓN
//~ $this->pdf->add_page() # AÑADE UNA NUEVA PAGINACIÓN
#$this->pdf->SetFont('Times','',10) # TAMAÑO DE LA FUENTE
$this->pdf->SetFont('Arial','B',15);
$this->pdf->SetFillColor(157,188,201); # COLOR DE BOLDE DE LA CELDA
$this->pdf->SetTextColor(24,29,31); # COLOR DEL TEXTO
$this->pdf->SetMargins(15,15,10); # MARGEN DEL DOCUMENTO
#$this->pdf->ln(20) # Saldo de línea
# 10 y 50 eje x y y 200 dimensión

$this->pdf->SetFillColor(255,255,255);
$this->pdf->SetFont('Arial','B',14);
$this->pdf->Ln(5);
//~ $this->pdf->Cell(185,5,"",'',1,'C',1);
$this->pdf->MultiCell(185,5,utf8_decode('GOErp Intranet'),'','C',0);
$this->pdf->SetFont('Arial','',8);
//~ $this->pdf->Cell(30,5,"RIF:",'',0,'C',1);
$this->pdf->Cell(185,5,"RIF:   J-40162325-5",'',1,'C',1);
$texto = 'Av. Ppal El Castaño - # 131 - Maracay, Aragua. Municipio Girardot. 2101.';
$this->pdf->MultiCell(185,5,utf8_decode($texto),'','C',0);
$fecha = date('d/m/Y');
$hora = date("h:i:s a");
$this->pdf->Cell(30,5,"FECHA: $fecha",'B',0,'L',1);
$this->pdf->Cell(30,5,"",'B',0,'L',1);
$this->pdf->Cell(30,5,"HORA: $hora",'B',0,'L',1);
$this->pdf->Cell(95,5,"",'B',1,'R',1);

if ($usuario != "xxx"){
	$usuario = $usuario->username;
}else{
	$usuario = 'Todos';
}

if ($desde != "xxx" && $hasta != "xxx"){
	$desde = explode("-",$desde);
	$desde = $desde[2]."-".$desde[1]."-".$desde[0];
	$hasta = explode("-",$hasta);
	$hasta = $hasta[2]."-".$hasta[1]."-".$hasta[0];
}

$this->pdf->Cell(30,5,"DATOS DEL REPORTE",'T',1,'L',1);
//~ $this->pdf->Cell(35,5,"RIF/CI: ".$cliente->tipocliente."-".$cliente->cirif,'',0,'L',1);
$this->pdf->Cell(35,5,utf8_decode("USUARIO: $usuario"),'',0,'L',1);
$this->pdf->Cell(60,5,utf8_decode("FECHA INICIAL: $desde"),'',0,'L',1);
$this->pdf->Cell(60,5,utf8_decode("FECHA FINAL: $hasta"),'',1,'L',1);


$this->pdf->Cell(30,5,"Modelo",'TB',0,'L',1);
$this->pdf->Cell(30,5,utf8_decode("Código"),'TB',0,'C',1);
$this->pdf->Cell(45,5,utf8_decode("Acción"),'TB',0,'L',1);
$this->pdf->Cell(40,5,utf8_decode("Usuario"),'TB',0,'L',1);
$this->pdf->Cell(20,5,"Fecha ",'TB',0,'L',1);
$this->pdf->Cell(20,5,"Hora",'TB',1,'R',1);

$j = 0;  # Contador para el salto de página

foreach ($auditoria as $auditoria){
	
	#Sección para el salto de página
	if ($j == 44){
		$this->pdf->AddPage();
		$this->pdf->SetFillColor(255,255,255);
		$this->pdf->SetFont('Arial','B',14);
		$this->pdf->Ln(1);
		$this->pdf->Cell(185,5,"",'',1,'C',1);
		$this->pdf->MultiCell(185,5,utf8_decode('A.C SYSERP'),'','C',0);
		$this->pdf->SetFont('Arial','',8);
		//~ $this->pdf->Cell(30,5,"RIF:",'',0,'C',1);
		$this->pdf->Cell(185,5,"RIF:   G-20010539-9",'',1,'C',1);
		$texto = 'AV. SUCRE. URB. SAN ISIDRO - MARACAY. EDIF. SYSERP. NRO. 26';
		$this->pdf->MultiCell(185,5,utf8_decode($texto),'','C',0);
		$fecha = date('d/m/Y');
		$hora = date("h:i:s a");
		$this->pdf->Cell(30,5,"FECHA: $fecha",'B',0,'L',1);
		$this->pdf->Cell(30,5,"",'B',0,'L',1);
		$this->pdf->Cell(30,5,"HORA: $hora",'B',0,'L',1);
		$this->pdf->Cell(95,5,"",'B',1,'R',1);
		
		$this->pdf->Cell(30,5,"DATOS DEL REPORTE",'T',1,'L',1);
		//~ $this->pdf->Cell(35,5,"RIF/CI: ".$cliente->tipocliente."-".$cliente->cirif,'',0,'L',1);
		$this->pdf->Cell(35,5,utf8_decode("USUARIO: $usuario"),'',0,'L',1);
		$this->pdf->Cell(60,5,utf8_decode("FECHA INICIAL: $desde"),'',0,'L',1);
		$this->pdf->Cell(60,5,utf8_decode("FECHA FINAL: $hasta"),'',1,'L',1);


		$this->pdf->Cell(30,5,"Modelo",'TB',0,'L',1);
		$this->pdf->Cell(30,5,utf8_decode("Código"),'TB',0,'C',1);
		$this->pdf->Cell(45,5,utf8_decode("Acción"),'TB',0,'L',1);
		$this->pdf->Cell(40,5,utf8_decode("Usuario"),'TB',0,'L',1);
		$this->pdf->Cell(20,5,"Fecha ",'TB',0,'L',1);
		$this->pdf->Cell(20,5,"Hora",'TB',1,'R',1);

		#pdf.ln(60)
		$j = 0;
	}
	
	$fecha_auditoria = explode("-",$auditoria->fecha);
	$fecha_auditoria = $fecha_auditoria[2]."-".$fecha_auditoria[1]."-".$fecha_auditoria[0];
	$this->pdf->Cell(30,5,utf8_decode("$auditoria->tabla"),'',0,'L',1);
	$this->pdf->Cell(30,5,"$auditoria->codigo",'',0,'C',1);
	$this->pdf->Cell(45,5,utf8_decode("$auditoria->accion"),'',0,'L',1);
	$this->pdf->Cell(40,5,utf8_decode("$usuario"),'',0,'L',1);
	$this->pdf->Cell(20,5,"$fecha_auditoria",'',0,'L',1);
	$this->pdf->Cell(20,5,"$auditoria->hora",'',1,'R',1);
	
	$j += 1;
}
$this->pdf->Cell(185,1,"",'T',1,'R',1);  // Cierre de bloque de productos/servicios

// Salida del Formato PDF
$this->pdf->Output("auditoria.pdf", 'I');
