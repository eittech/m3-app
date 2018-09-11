<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// Incluimos el archivo fpdf
require_once APPPATH . "/third_party/fpdf/fpdf.php";


//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class PDF_HTML extends FPDF
{
//variables of html parser
protected $B;
protected $I;
protected $U;
protected $HREF;
protected $fontList;
protected $issetfont;
protected $issetcolor;

function __construct($orientation='P', $unit='mm', $format='A4')
{
    //Call parent constructor
    parent::__construct($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
    $this->issetfont=false;
    $this->issetcolor=false;
}

function WriteHTML($html)
{
    //HTML parser
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
    $html=str_replace("<br/>","\n",$html); //remplace retour à la ligne par un espace
    $html=str_replace("<br />","\n",$html); //remplace retour à la ligne par un espace
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,stripslashes(txtentities($e)));
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    //Opening tag
    switch($tag){
        case 'STRONG':
            $this->SetStyle('B',true);
            break;
        case 'EM':
            $this->SetStyle('I',true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag,true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
            break;
        case 'TR':
        case 'BLOCKQUOTE':
        case 'BR':
            $this->Ln(5);
            break;
        case 'P':
            $this->Ln(10);
            break;
        case 'FONT':
            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='STRONG')
        $tag='B';
    if($tag=='EM')
        $tag='I';
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag, $enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

}//end of class

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
