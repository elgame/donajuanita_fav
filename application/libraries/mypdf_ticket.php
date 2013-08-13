<?php

class mypdf_ticket extends FPDF {
    var $limiteY = 0;
    var $titulo1 = 'CAFE DIGITAL';

    var $pag_size = array();

    private $header_entrar = false;

	/**
	 * P:Carta Vertical, L:Carta Horizontal, lP:Legal vertical, lL:Legal Horizontal
	 * @param unknown_type $orientation
	 * @param unknown_type $unit
	 * @param unknown_type $size
	 */
	function __construct($orientation='P', $unit='mm', $size=array(63, 180)){
		parent::__construct($orientation, $unit, $size);
		$this->limiteY = 50;
        $this->pag_size = $size;

        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false);
	}

    //Page header
    public function Header() {
        if ($this->header_entrar) {
            // Título
            $this->SetFont('Arial', 'B', 8);
            $this->SetXY(0, 0);
            $this->MultiCell($this->pag_size[0], 10, $this->titulo1, 0, 'C');

            $this->header_entrar = false;
        }
    }

    public function datosTicket($data){
        $this->SetXY(0, 0);
        if ($data[0]->empresa_url_logop == true) {
            $logo = explode('.', $data[0]->empresa_url_logo);
            $logo = $logo[0].'_bn.'.$logo[1];
            if(!file_exists($logo))
                $logo = $data[0]->empresa_url_logo;

            $size = $this->getSizeImage($logo, 0, 0);
            $xw = ($this->CurPageSize[0]-$size[0])/2;
            $this->Image($logo, $xw, 0);
            $this->SetXY(0, $size[1]);
        }

        // Título
        $this->SetFont('Arial', 'B', 8);
        $this->MultiCell($this->pag_size[0], 10, $data[0]->empresa_nombre, 0, 'C');

        $direccon = $data[0]->empresa_calle.' '.$data[0]->empresa_num_ext.($data[0]->empresa_num_int!=''? '-'.$data[0]->empresa_num_int: '');
        $direccon .= ($data[0]->empresa_colonia!=''? ', Col. '.$data[0]->empresa_colonia: '');
        $direccon1 = $data[0]->empresa_municipio.', '.$data[0]->empresa_estado.($data[0]->empresa_cp!=''? ', CP '.$data[0]->empresa_cp: '');
        $this->SetFont('Arial', '', 8);
        $this->MultiCell($this->pag_size[0], 3, $data[0]->empresa_razon_social, 0, 'L');
        if($data[0]->empresa_rfc!='')
            $this->MultiCell($this->pag_size[0], 3, 'RFC: '.$data[0]->empresa_rfc, 0, 'L');
        $this->MultiCell($this->pag_size[0], 3, $direccon, 0, 'L');
        $this->MultiCell($this->pag_size[0], 3, $direccon1, 0, 'L');

        $this->MultiCell($this->pag_size[0], 3, '====================================', 0, 'L');

        $this->MultiCell($this->pag_size[0], 3, 'TICKET : ' . $data[0]->id_ticket , 0, 'L');

        $this->MultiCell($this->pag_size[0], 3, 'FECHA :' . $data[0]->fecha, 0, 'L');

        $this->MultiCell($this->pag_size[0], 3, '====================================', 0, 'L');
    }

    public function productosTicket($data, $data_info){

        $this->SetY($this->GetY()+3);

        $this->SetFont('Arial', '', 8);
        $this->SetWidths(array(62));
        $this->SetAligns(array('L'));
        $this->Row(array('ARTICULO'), false, false);

        $this->SetWidths(array(12, 19, 15, 15));
        $this->SetAligns(array('L'));
        $this->Row(array('CANT.', 'PRECIO/UN.', 'IMPORTE', '% DESC'), false, false);

        $this->SetFont('Arial', '', 8);
        $this->CheckPageBreak(4);
        $this->MultiCell($this->pag_size[0], 3, '--------------------------------------------------------------', 0, 'L');
        if(is_array($data_info)){
            foreach ($data_info as $prod){
              $this->SetFont('Arial', '', 8);
              $this->SetWidths(array(62));
              $this->SetAligns(array('L'));
              $this->Row(array($prod->nombre), false, false);

              $this->SetWidths(array(12, 19, 15, 15));
              $this->SetAligns(array('L'));
              $this->Row(array(
                $prod->cantidad,
                String::formatoNumero($prod->precio_venta,2),
                String::formatoNumero($prod->importe,2),
                $prod->descuento . '%'), false, false);
              $this->SetY($this->GetY() );
            }
        }
        $this->CheckPageBreak(4);
        $this->MultiCell($this->pag_size[0], 3, '---------------------------------------------------------------', 0, 'L');

        $this->SetWidths(array(31, 30));
        $this->SetAligns(array('L'));

        $this->Row(array( 'SUB-TOTAL SIN IVA', String::formatoNumero($data[0]->subtotal_no_iva)), false, false, 3);
        $descuento_dinero = String::float($data[0]->subtotal_no_iva * ($data[0]->descuento / 100), false, 2);
        $this->Row(array( "DESCUENTO ({$data[0]->descuento}%)" , String::formatoNumero($descuento_dinero)), false, false, 3);

        $this->Row(array( 'SUB-TOTAL', String::formatoNumero($data[0]->subtotal_no_iva - $descuento_dinero)), false, false, 3);
        $this->Row(array( 'IVA', String::formatoNumero($data[0]->iva)), false, false, 3);


        $this->Row(array( 'TOTAL', String::formatoNumero($data[0]->total)), false, false, 3);

        $this->SetY($this->GetY() + 5);

        $this->Row(array( 'PAGO CON', String::formatoNumero($data[0]->recibido)), false, false, 3);
        $this->Row(array( 'CAMBIO', String::formatoNumero($data[0]->cambio)), false, false, 3);

        $this->SetY($this->GetY() + 3);
        $this->Row(array( 'TIPO DE PAGO', strtoupper($data[0]->tipo_pago)), false, false, 3);
    }

    public function pieTicket($data){

      $this->SetY($this->GetY() + 5);

      $this->MultiCell($this->pag_size[0], 3, '====================================', 0, 'L');

      $this->SetFont('Arial', '', 7);
      $this->SetWidths(array($this->pag_size[0]));
      $this->SetAligns(array('C'));
      if($data[0]->empresa_telefono!='')
        $this->Row(array('Tel. '.$data[0]->empresa_telefono), false, false);
      $this->Row(array($data[0]->empresa_email), false, false);
      $this->SetY($this->GetY() - 3);
      $this->Row(array($data[0]->empresa_pag_web ), false, false);

      if($data[0]->empresa_footer!=''){
          $this->SetFont('Arial', '', 6);
          $this->SetY($this->GetY());
          $this->Row(array($data[0]->empresa_footer ), false, false);
      }
    }

    public function printTicket($data, $data_prod){
        $this->datosTicket($data);
        $this->productosTicket($data, $data_prod);
        $this->pieTicket($data);
    }



    function getSizeImage($file, $w=0, $h=0, $type='')
    {
        // Put an image on the page
        if(!isset($this->images[$file]))
        {
            // First use of this image, get info
            if($type=='')
            {
                $pos = strrpos($file,'.');
                if(!$pos)
                    $this->Error('Image file has no extension and no type was specified: '.$file);
                $type = substr($file,$pos+1);
            }
            $type = strtolower($type);
            if($type=='jpeg')
                $type = 'jpg';
            $mtd = '_parse'.$type;
            if(!method_exists($this,$mtd))
                $this->Error('Unsupported image type: '.$type);
            $info = $this->$mtd($file);
            // $info['i'] = count($this->images)+1;
            // $this->images[$file] = $info;
        }
        else
            $info = $this->images[$file];

        // Automatic width and height calculation if needed
        if($w==0 && $h==0)
        {
            // Put image at 96 dpi
            $w = -96;
            $h = -96;
        }
        if($w<0)
            $w = -$info['w']*72/$w/$this->k;
        if($h<0)
            $h = -$info['h']*72/$h/$this->k;
        if($w==0)
            $w = $h*$info['w']/$info['h'];
        if($h==0)
            $h = $w*$info['h']/$info['w'];

        return array($w, $h);
    }


    var $col=0;

    function SetCol($col){
        //Move position to a column
        $this->col=$col;
        $x=10+$col*65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    function AcceptPageBreak(){
        if($this->col<2){
            //Go to next column
            $this->SetCol($this->col+1);
            $this->SetY(10);
            return false;
        }else{
            //Regrese a la primera columna y emita un salto de página
            $this->SetCol(0);
            return true;
        }
    }




    /*Crear tablas*/
    var $widths;
    var $aligns;
    var $links;

    function SetWidths($w){
        $this->widths=$w;
    }

    function SetAligns($a){
        $this->aligns=$a;
    }

    function SetMyLinks($a){
        $this->links=$a;
    }

    function Row($data, $header=false, $bordes=true, $h=NULL){
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h= $h==NULL? $this->FontSize*$nb+3: $h;
            if($header)
                $h += 2;
            $this->CheckPageBreak($h);
            for($i=0;$i<count($data);$i++){
                $w=$this->widths[$i];
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                $x=$this->GetX();
                $y=$this->GetY();

                if($header && $bordes)
                    $this->Rect($x,$y,$w,$h,'DF');
                elseif($bordes)
                    $this->Rect($x,$y,$w,$h);

                if($header)
                    $this->SetXY($x,$y+3);
                else
                    $this->SetXY($x,$y+2);

                if(isset($this->links[$i]{0}) && $header==false){
                    $this->SetTextColor(35, 95, 185);
                    $this->Cell($w, $this->FontSize, $data[$i], 0, strlen($data[$i]), $a, false, $this->links[$i]);
                    $this->SetTextColor(0,0,0);
                }else
                    $this->MultiCell($w,$this->FontSize, $data[$i],0,$a);

                $this->SetXY($x+$w,$y);
            }
            $this->Ln($h);
    }

    function CheckPageBreak($h, $limit=0){
        $limit = $limit==0? $this->PageBreakTrigger: $limit;
        if($this->GetY()+$h>$limit){
            $this->AddPage($this->CurOrientation);
            return true;
        }
        return false;
    }

    function NbLines($w,$txt){
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }



    /**
     * indica si se abre el dialogo de imprecion inmediatamente
     * @param boolean $dialog [description]
     */
    function AutoPrint($dialog=false){
        //Open the print dialog or start printing immediately on the standard printer
        $param=($dialog ? 'true' : 'false');
        $script="print($param);";
        $this->IncludeJS($script);
    }


    /**
     * SOPORTE PARA INTRODUCIR JAVASCRIPT
     */
    var $javascript;
    var $n_js;

    function IncludeJS($script) {
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}


?>