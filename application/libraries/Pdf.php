<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// Incluimos el archivo fpdf
require_once APPPATH . "/third_party/fpdf/fpdf.php";

//Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones
class Pdf extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimencion de la imagen */
                                                      # Y  Z D
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
		//~ $this->Image('static/img/002.jpg',220,10,45);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Nueva clase para los reportes de facturas
class PdfFactura extends FPDF
{
	// Nueva propiedad o atributo de la clase extendida, el cual almacenará el nombre del almacenista que entrega la factura
	var $entregado_por;
	var $fecha_entrega;
	var $hora_entrega;
	var $estado;

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimensión de la imagen */
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
        if($this->estado == 2 || $this->estado == 4){
			// $this->Image(ruta_imagen,x,y,tamaño);
			$this->Image(base_url().'static/img/pagado.png',30,100,150);
		}
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(75,5,utf8_decode("Entregado por: ".$this->entregado_por),'',0,'C',1);
		$this->Cell(35,5,"",'',0,'C',1);
		$fecha_actual = date('d/m/Y')." ".date("h:i:s a");
		$this->Cell(75,5,"Fecha: ".$this->fecha_entrega." ".$this->hora_entrega,'',1,'C',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Nueva clase para los reportes de facturas
class PdfRecibo extends FPDF
{
	// Nueva propiedad o atributo de la clase extendida, el cual almacenará el nombre del almacenista que entrega la factura
	var $entregado_por;
	var $fecha_entrega;
	var $hora_entrega;

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimensión de la imagen */
                                                      # Y  Z D
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(75,5,utf8_decode("Emitido por: ".$this->entregado_por),'',0,'C',1);
		$this->Cell(35,5,"",'',0,'C',1);
		$fecha_actual = date('d/m/Y')." ".date("h:i:s a");
		$this->Cell(75,5,"Fecha: ".$this->fecha_entrega." ".$this->hora_entrega,'',1,'C',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Nueva clase para los reportes de facturas
class PdfPago extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimensión de la imagen */
                                                      # Y  Z D
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Nueva clase para los reportes de facturas
class PdfPedido extends FPDF
{
	// Nueva propiedad o atributo de la clase extendida, el cual almacenará el nombre del almacenista que entrega la factura
	var $entregado_por;
	var $fecha_ingreso;
	var $hora_ingreso;

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimensión de la imagen */
                                                      # Y  Z D
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(75,5,utf8_decode("Ingresado por: ".$this->entregado_por),'',0,'C',1);
		$this->Cell(35,5,"",'',0,'C',1);
		$fecha_actual = date('d/m/Y')." ".date("h:i:s a");
		$this->Cell(75,5,"Fecha: ".$this->fecha_ingreso." ".$this->hora_ingreso,'',1,'C',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Clase para el reporte de ventas
class PdfVentas extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimencion de la imagen */
                                                      # Y  Z D
        //~ $this->Image(base_url().'script/image/Home.png',15,7,20);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(55,5,"",'',0,'C',1);
		$this->Cell(75,5,"Firma",'T',0,'C',1);
		$fecha_actual = date('d/m/Y');
		$this->Cell(55,5,"Fecha: $fecha_actual",'',1,'R',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Clase para el reporte de auto-consumo
class PdfAutoconsumo extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimencion de la imagen */
                                                      # Y  Z D
        $this->Image('static/img/GOErp_Logo.png',100,0,15);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(75,5,utf8_decode("Gerente de Admnistración"),'T',0,'C',1);
		$this->Cell(35,5,"",'',0,'C',1);
		//~ $fecha_actual = date('d/m/Y');
		$this->Cell(75,5,"Presidente",'T',1,'C',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Clase para el reporte de auto-consumo
class PdfInventario extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimencion de la imagen */
                                                      # Y  Z D
        //~ $this->Image('static/img/GOErp_Logo.png',100,0,15);
    }
    
    // El pie del pdf
    public function Footer()
    {
        //~ $this->SetY(-30);
        //~ $this->SetFont('Arial', 'I', 8);
        //~ $this->Cell(75,5,utf8_decode("Gerente de Administración"),'T',0,'C',1);
		//~ $this->Cell(35,5,"",'',0,'C',1);
		//~ // $fecha_actual = date('d/m/Y');
		//~ $this->Cell(75,5,"Presidente",'T',1,'C',1);
		//~ $this->SetY(-15);
        //~ $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

// Clase para el reporte de auto-consumo
class PdfInventarioTerminal extends FPDF
{

    public function __construct()
    {
        @parent::__construct();
    }
    
    public function Header()
    {   /*Y = Eje izquierdo
        # Z = Arriba / Abajo
        # D = Dimencion de la imagen */
                                                      # Y  Z D
        //~ $this->Image(base_url().'script/image/Home.png',15,7,20);
    }
    
    // El pie del pdf
    public function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(75,5,utf8_decode("Gerente de Comercialización"),'T',0,'C',1);
		$this->Cell(35,5,"",'',0,'C',1);
		//~ $fecha_actual = date('d/m/Y');
		$this->Cell(75,5,"Presidente",'T',1,'C',1);
		$this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function Format_number($decimal)
    {
        $result = str_replace('', '', number_format($decimal, 2, ",", "."))." Bs";
        return $result;
    }

}

?>
