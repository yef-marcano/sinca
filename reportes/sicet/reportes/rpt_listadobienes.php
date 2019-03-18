<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";
require_once "../modelo/clMaestroModelo.php";


class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w){
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data){
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++){
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++){
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h,'DF');
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a,0);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0){
            $w=$this->w-$this->rMargin-$this->x;
        }
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n"){
            $nb--;
        }
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
            if($c==' '){
                $sep=$i;
            }
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j){
                        $i++;
                    }
                }else{
                    $i=$sep+1;
                }
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }else{
                $i++;
            }
        }
        return $nl;
    }
}
class PDF  extends PDF_MC_Table
{
	function Header()
	{
		
		//Logo
		$this->Image('../comunes/images/carta.png',10,3,200,0);
                
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		if ($_REQUEST['titulo']!=""){
			$this->Cell(0,10,strtoupper(utf8_decode($_REQUEST['titulo'])),0,0,'C');
		}else{
			$this->Cell(0,10,utf8_decode('LISTADO DE BIENES TELEMÁTICOS'),0,0,'C');
		}
		$this->Ln(10);
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-20);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		if ($_REQUEST['filtro']!=""){
			$this->MultiCell(0,5,'Filtro Aplicado:'.utf8_decode($_REQUEST['filtro']),0,'J');
		}
		$this->Cell(232,8,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,8,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$bienes= new clBienesModelo();
$usuario= new clUsuarioModelo();

$sql="select b.*, g.stritema as gerencia, c.stritema as categoria, 
		case when b.id_unidad_maestro>0 then
		(select '(' ||stritema||')' from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
		else
		''
		end as unidad,t.stritema as tipo, m.stritema as marca,
		case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
		(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
		else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
		b.strnombres
		else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
		'DISPONIBLE'
		else case  when b.id_estatus_maestro in (89) then
		e.stritemb
		else case  when b.id_estatus_maestro in (90) then
		'EXTRAVIADO'
		else case  when b.id_estatus_maestro in (91) then
		'DESINCORPORADO'
		else case  when b.id_estatus_maestro in (618) then
		'ROBADO'
		else case  when b.id_estatus_maestro in (619) then
		'REEMPLAZADO'
		else
		'SIN USUARIO'
		end end end end end end end end as responsable, e.stritema as estatus, mo.stritema as modelo,to_char(b.dtmfechafactura,'DD/MM/YYYY') as dtmfecha,
		case when b.id_proveedor>0 then
		(select strnombre from  tblproveedor where id_proveedor=b.id_proveedor and bolborrado=0)
		else
		'SE DESCONOCE'
		end as proveedor, u.stritema as ubicacion 
		from tblbienes b, tblmaestros g, tblmaestros c, tblmaestros t, 
		tblmaestros m,tblmaestros e, tblmaestros mo, tblmaestros u 
		where b.id_gerencia_maestro=g.id_maestro 
		and b.id_categoria_maestro=c.id_maestro and b.id_tipo_maestro=t.id_maestro and b.id_ubicacion=u.id_maestro 
		and b.id_marca_maestro=m.id_maestro and b.id_estatus_maestro=e.id_maestro 
		and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0 and c.bolborrado=0 
		and t.bolborrado=0 and m.bolborrado=0 and e.bolborrado=0 and mo.bolborrado=0  and u.bolborrado=0 ";
 		if ($_REQUEST['nombre']!=""){
			$sql.="AND b.strnombre LIKE '%".strtoupper($_REQUEST['nombre'])."%' ";
		}
 		if ($_REQUEST['serial']!=""){
			$sql.="AND b.strserial LIKE '%".strtoupper($_REQUEST['serial'])."%' ";
		}		
 		
		if ($_REQUEST['marca']!=""){
			$sql.="AND b.id_marca_maestro='".$_REQUEST['marca']."' ";
		}
 		if ($_REQUEST['modelo']!=""){
			$sql.="AND b.id_modelo_maestro='".$_REQUEST['modelo']."' ";
		}
 		if ($_REQUEST['categoria']!=""){
			$sql.="AND b.id_categoria_maestro='".$_REQUEST['categoria']."' ";
		}
 		if ($_REQUEST['tipo']!=""){
			$sql.="AND b.id_tipo_maestro in (".$_REQUEST['tipo'].") ";
		}
 		if ($_REQUEST['gerencia']!=""){
			$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
		}
 		if ($_REQUEST['unidad']!=""){
			$sql.="AND b.id_unidad_maestro in (".$_REQUEST['unidad'].") ";
		}
 		if ($_REQUEST['responsable']!=""){
			$sql.="AND b.id_responsable='".strtoupper($_REQUEST['responsable'])."' ";
		}
 		if ($_REQUEST['garantia']!=""){
			$sql.="AND b.lnggarantia='".$_REQUEST['garantia']."' ";
		}
 		if ($_REQUEST['proveedor']!=""){
			$sql.="AND b.id_proveedor='".$_REQUEST['proveedor']."' ";
		}
 		if ($_REQUEST['estatus']!=""){
			$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
		}
 		if ($_REQUEST['ubicacion']!=""){
			$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
		}
        $sql.=" ORDER BY gerencia, responsable, t.stritema";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
//$pdf->MultiCell(0,3,$sql,1);       
        
$nroCampos=split(",",$_REQUEST['campo']);
$textColumnas=split(",",$_REQUEST['nombreCampo']);
$campos=count($nroCampos);
$ancho='252'/$campos;

if ($_REQUEST['nomEstatus']!=""){
    $estatus=split("-",$_REQUEST['nomEstatus']);
    $nroestatus=split(",",$_REQUEST['estatus']);
    $cantidad=count($estatus);
}

for ($i= 0; $i < count($nroCampos); $i++){
	$anchocolumnas[]=$ancho;
	$alincolumnas[]="C";
}

if ($data){
	if ($_REQUEST['continuo']!="1"){
	    $gerencia=$data[0]["id_gerencia_maestro"];
	    $gerencianombre=$data[0]["gerencia"]." ".$data[0]['unidad'];
	    $ubicacion= $data[0]["id_unidad_maestro"];
	    $pdf->SetFont('Arial','B',8);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(0.3);
	    $pdf->Cell(0, 10,'Gerencia: '.utf8_decode($data[0]["gerencia"]." ".$data[0]["unidad"]), 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetFillColor(255,0,0);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(0);
	    $pdf->SetWidths($anchocolumnas);
		$pdf->SetAligns($alincolumnas);
		$pdf->Row($textColumnas);
		for ($i= 0; $i < count($data); $i++){
			if ($data[$i]['responsable']=='DANADO') {
				$responsable="DAÑADO";
			}else if ($data[$i]['responsable']==" "){
				$responsable="SIN USUARIO";
			}else{			
				$responsable=utf8_decode($data[$i]['responsable']);
			}
                        if ($gerencianombre!=$data[$i]["gerencia"]." ".$data[$i]['unidad']){
				$pdf->SetWidths(array(252));
                                $pdf->SetAligns(array('R'));
                                $pdf->Row(array('Total de Equipos '.$total));
                                $pdf->Ln();
                                $total=0;
				if ($_REQUEST['consolidado']=="1"){
					
					$sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc as gerencia, b.id_estatus_maestro 
					from tblbienes b, tblmaestros t,tblmaestros g 
					where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro 
					and b.bolborrado=0 and t.bolborrado=0 and b.id_gerencia_maestro='".$gerencia."' and b.id_unidad_maestro=".$ubicacion." ";
					if ($_REQUEST['nombre']!=""){
						$sql.="AND b.strnombre LIKE '%".strtoupper($_REQUEST['nombre'])."%' ";
					}
			 		if ($_REQUEST['serial']!=""){
						$sql.="AND b.strserial LIKE '%".strtoupper($_REQUEST['serial'])."%' ";
					}		
			 		
					if ($_REQUEST['marca']!=""){
						$sql.="AND b.id_marca_maestro='".$_REQUEST['marca']."' ";
					}
			 		if ($_REQUEST['modelo']!=""){
						$sql.="AND b.id_modelo_maestro='".$_REQUEST['modelo']."' ";
					}
			 		if ($_REQUEST['categoria']!=""){
						$sql.="AND b.id_categoria_maestro='".$_REQUEST['categoria']."' ";
					}
			 		if ($_REQUEST['tipo']!=""){
						$sql.="AND b.id_tipo_maestro in (".$_REQUEST['tipo'].") ";
					}
			 		if ($_REQUEST['gerencia']!=""){
						$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
					}
			 		if ($_REQUEST['unidad']!=""){
						$sql.="AND b.id_unidad_maestro in (".$_REQUEST['unidad'].") ";
					}
			 		if ($_REQUEST['responsable']!=""){
						$sql.="AND b.id_responsable='".strtoupper($_REQUEST['responsable'])."' ";
					}
			 		if ($_REQUEST['garantia']!=""){
						$sql.="AND b.lnggarantia='".$_REQUEST['garantia']."' ";
					}
			 		if ($_REQUEST['proveedor']!=""){
						$sql.="AND b.id_proveedor='".$_REQUEST['proveedor']."' ";
					}
			 		if ($_REQUEST['estatus']!=""){
						$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
					}
			 		if ($_REQUEST['ubicacion']!=""){
						$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
					}
					
					$sql.=" group by t.stritema, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc, b.id_estatus_maestro order by b.id_gerencia_maestro,b.id_tipo_maestro,b.id_estatus_maestro";
					
                                        $conn->sql=$sql;
					$data1=$conn->ejecutarSentencia(2); 
					$pdf->SetFont('Arial','B',8);		
					$pdf->SetFillColor(255,0,0);		
					$pdf->SetTextColor(255,255,255);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3);
                                        switch ($cantidad) {
                                            case 1:
                                                $pdf->SetWidths(array(40,23,20));
                                                $pdf->SetAligns(array('C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),'Total'));
                                                $pdf->SetAligns(array('L','R','R'));
                                                break;
                                            case 2:
                                                $pdf->SetWidths(array(40,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R'));
                                                break;
                                            case 3:
                                                $pdf->SetWidths(array(40,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R'));
                                                break;
                                            case 4:
                                                $pdf->SetWidths(array(40,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R'));
                                                break;
                                            case 5:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R'));
                                                break;
                                            case 6:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R'));
                                                break;
                                            case 7:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
                                                break;
                                            case 8:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),utf8_decode($estatus[7]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
                                                break;
                                            default:
                                                $pdf->SetWidths(array(40,20,20,20,20,20,25,20,25,25));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo','Disponible','Asignado',utf8_decode('Préstamo'),utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));


                                        }					
					$pdf->SetFont('Arial','',8);
					$pdf->SetFillColor(255,255,255);		
					$pdf->SetTextColor(0);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3); 
					if ($data1){	
						$asignado=0;
						$danado=0;
						$extraviado=0;
						$disponible=0;
						$prestamo=0;
						$reemplazado=0;
						$robado=0;
						$desincorporado=0;				
						$totalc=0;
						$equipo=$data1[0]['equipo'];
						$tdis=0;
						$tpres=0;
						$tasig=0;
						$tdan=0;
						$text=0;
						$tdes=0;
						$trob=0;
						$tree=0;
						$ttotalc=0;
						for ($j= 0; $j < count($data1); $j++){
							if ($equipo==$data1[$j]['equipo']){			
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;				
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];				
								}
							}else{
								$totalc=$asignado + $prestamo + $danado + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
								$ttotalc+=$totalc;
                                                                $resultado[]=utf8_decode($equipo);
                                                                if ($cantidad>'0'){
                                                                    for ($h=1; $h<=$cantidad; $h++){
                                                                        switch ($nroestatus[$h-1]) {
                                                                            case '86':
                                                                                $resultado[]=$disponible;
                                                                                break;
                                                                            case '87':
                                                                                $resultado[]=$prestamo;
                                                                                break;
                                                                            case '88':
                                                                                $resultado[]=$asignado;
                                                                                break;
                                                                            case '89':
                                                                                $resultado[]=$danado;
                                                                                break;
                                                                            case '90':
                                                                                $resultado[]=$extraviado;
                                                                                break;
                                                                            case '91':
                                                                                $resultado[]=$desincorporado;
                                                                                break;
                                                                            case '618':
                                                                                $resultado[]=$robado;
                                                                                break;
                                                                            case '619':
                                                                                $resultado[]=$reemplazado;
                                                                                break;
                                                                        }
                                                                    }
                                                                    $resultado[]=$totalc;
                                                                    $pdf->Row($resultado);
                                                                }else{
                                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                                }
								
								$asignado=0;
								$danado=0;
								$extraviado=0;
								$disponible=0;
								$prestamo=0;
								$reemplazado=0;
								$robado=0;
								$desincorporado=0;				
								$totalc=0;
								$equipo=$data1[$j]['equipo'];
                                                                unset($resultado);
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;				
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];				
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];				
								}
							}
							
						}
						$totalc=$asignado + $danado + $prestamo + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
						$ttotalc+=$totalc;
                                                $resultado[]=utf8_decode($equipo);
                                                $tresultado[]="TOTAL";
                                                if ($cantidad>'0'){
                                                    for ($h=1; $h<=$cantidad; $h++){
                                                        switch ($nroestatus[$h-1]) {
                                                            case '86':
                                                                $resultado[]=$disponible;
                                                                $tresultado[]=$tdis;
                                                                break;
                                                            case '87':
                                                                $resultado[]=$prestamo;
                                                                $tresultado[]=$tpres;
                                                                break;
                                                            case '88':
                                                                $resultado[]=$asignado;
                                                                $tresultado[]=$tasig;
                                                                break;
                                                            case '89':
                                                                $resultado[]=$danado;
                                                                $tresultado[]=$tdan;
                                                                break;
                                                            case '90':
                                                                $resultado[]=$extraviado;
                                                                $tresultado[]=$text;
                                                                break;
                                                            case '91':
                                                                $resultado[]=$desincorporado;
                                                                $tresultado[]=$tdes;
                                                                break;
                                                            case '618':
                                                                $resultado[]=$robado;
                                                                $tresultado[]=$trob;
                                                                break;
                                                            case '619':
                                                                $resultado[]=$reemplazado;
                                                                $tresultado[]=$tree;
                                                                break;
                                                        }
                                                    }
                                                    $resultado[]=$totalc;
                                                    $tresultado[]=$ttotalc;
                                                    $pdf->Row($resultado);
                                                    $pdf->Row($tresultado);
                                                }else{
                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                    $pdf->Row(array('TOTAL',$tdis,$tasig,$tpres,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
                                                }

						
						
					}		
					$pdf->AddPage();
				}
                                
				//$pdf->AddPage();
				$pdf->SetFont('Arial','B',8);
				$pdf->SetFillColor(255,255,255);
				$pdf->SetTextColor(0);
				$pdf->SetDrawColor(0);
				$pdf->SetLineWidth(0.3);
				$pdf->Cell(0, 10,'Gerencia: '.utf8_decode($data[$i]["gerencia"]." ".$data[$i]["unidad"]), 0, 0, 'L');
                                $pdf->Ln();
                                $pdf->SetFillColor(255,0,0);
                                $pdf->SetTextColor(255,255,255);
				$pdf->SetDrawColor(0);
                                $pdf->SetWidths($anchocolumnas);
				$pdf->SetAligns($alincolumnas);
				$pdf->Row($textColumnas);
				$pdf->SetFillColor(255,255,255);
				$pdf->SetTextColor(0);
				$pdf->SetFont('Arial','',8);
	    		switch ($campos) {
	    			case 1:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]])));
	    				break;
	    			case 2:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]])));
	    				
	    				break;
	    			case 3:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]])));
	    				break;
	    			case 4:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]])));
	    				break;
	    			case 5:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]])));
	    				break;
	    			case 6:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]])));
	    				break;
	    			case 7:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]])));
	    				break;
	    			case 8:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]])));
	    				break;
	    			case 9:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]])));
	    				break;
	    			case 10:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]])));
	    				break;
	    			case 11:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]])));
	    				break;    				
	    			case 12:	
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]]),utf8_decode($data[$i][$nroCampos[11]])));
	    				break;
	    			case 13:	
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]]),utf8_decode($data[$i][$nroCampos[11]]),utf8_decode($data[$i][$nroCampos[12]])));
	    				break;
	    		}
	    		
				
				$total++;
			}
			else
			{
				$total++;
				$pdf->SetFont('Arial','',8);
				$pdf->SetFillColor(255,255,255);
				$pdf->SetTextColor(0);
                                $pdf->SetWidths($anchocolumnas);
				switch ($campos) {
	    			case 1:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]])));
	    				break;
	    			case 2:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]])));
	    				break;
	    			case 3:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]])));
	    				break;
	    			case 4:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]])));
	    				break;
	    			case 5:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]])));
	    				break;
	    			case 6:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]])));
	    				break;
	    			case 7:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]])));
	    				break;
	    			case 8:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]])));
	    				break;
	    			case 9:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]])));
	    				break;
	    			case 10:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]])));
	    				break;
	    			case 11:
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]])));
	    				break;    				
	    			case 12:	
	    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]]),utf8_decode($data[$i][$nroCampos[11]])));
	    				break;
	    		}
			}
			$gerencia=$data[$i]["id_gerencia_maestro"];
			$ubicacion=$data[$i]["id_unidad_maestro"];
			$gerencianombre=$data[$i]["gerencia"]." ".$data[$i]['unidad'];
		}
		$pdf->SetWidths(array(252));
	    $pdf->SetAligns(array('R'));
	    $pdf->Row(array('Total de Equipos '.$total));
	    $pdf->Ln();

            
	    if ($_REQUEST['consolidado']=="1"){
					
					$sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc as gerencia, b.id_estatus_maestro 
					from tblbienes b, tblmaestros t,tblmaestros g 
					where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro 
					and b.bolborrado=0 and t.bolborrado=0 and b.id_gerencia_maestro='".$gerencia."' and b.id_unidad_maestro=".$ubicacion." ";
					if ($_REQUEST['nombre']!=""){
						$sql.="AND b.strnombre LIKE '%".strtoupper($_REQUEST['nombre'])."%' ";
					}
			 		if ($_REQUEST['serial']!=""){
						$sql.="AND b.strserial LIKE '%".strtoupper($_REQUEST['serial'])."%' ";
					}		
			 		
					if ($_REQUEST['marca']!=""){
						$sql.="AND b.id_marca_maestro='".$_REQUEST['marca']."' ";
					}
			 		if ($_REQUEST['modelo']!=""){
						$sql.="AND b.id_modelo_maestro='".$_REQUEST['modelo']."' ";
					}
			 		if ($_REQUEST['categoria']!=""){
						$sql.="AND b.id_categoria_maestro='".$_REQUEST['categoria']."' ";
					}
			 		if ($_REQUEST['tipo']!=""){
						$sql.="AND b.id_tipo_maestro in(".$_REQUEST['tipo'].") ";
					}
			 		if ($_REQUEST['gerencia']!=""){
						$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
					}
			 		if ($_REQUEST['unidad']!=""){
						$sql.="AND b.id_unidad_maestro='".$_REQUEST['unidad']."' ";
					}
			 		if ($_REQUEST['responsable']!=""){
						$sql.="AND b.id_responsable='".strtoupper($_REQUEST['responsable'])."' ";
					}
			 		if ($_REQUEST['garantia']!=""){
						$sql.="AND b.lnggarantia='".$_REQUEST['garantia']."' ";
					}
			 		if ($_REQUEST['proveedor']!=""){
						$sql.="AND b.id_proveedor='".$_REQUEST['proveedor']."' ";
					}
			 		if ($_REQUEST['estatus']!=""){
						$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
					}
			 		if ($_REQUEST['ubicacion']!=""){
						$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
					}
					
					$sql.=" group by t.stritema, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc, b.id_estatus_maestro order by b.id_gerencia_maestro,b.id_tipo_maestro,b.id_estatus_maestro";
					
		            $conn->sql=$sql;
					$data1=$conn->ejecutarSentencia(2);
					$pdf->SetFont('Arial','B',8);
					$pdf->SetFillColor(255,0,0);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3);
                                        switch ($cantidad) {
                                            case 1:
                                                $pdf->SetWidths(array(40,23,20));
                                                $pdf->SetAligns(array('C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),'Total'));
                                                $pdf->SetAligns(array('L','R','R'));
                                                break;
                                            case 2:
                                                $pdf->SetWidths(array(40,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R'));
                                                break;
                                            case 3:
                                                $pdf->SetWidths(array(40,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R'));
                                                break;
                                            case 4:
                                                $pdf->SetWidths(array(40,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R'));
                                                break;
                                            case 5:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R'));
                                                break;
                                            case 6:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R'));
                                                break;
                                            case 7:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
                                                break;
                                            case 8:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),utf8_decode($estatus[7]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
                                                break;
                                            default:
                                                $pdf->SetWidths(array(40,20,20,20,20,20,25,20,25,25));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo','Disponible','Asignado',utf8_decode('Préstamo'),utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));


                                        }
					$pdf->SetFont('Arial','',8);
					$pdf->SetFillColor(255,255,255);
					$pdf->SetTextColor(0);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3);
					if ($data1){
						$asignado=0;
						$danado=0;
						$extraviado=0;
						$disponible=0;
						$prestamo=0;
						$reemplazado=0;
						$robado=0;
						$desincorporado=0;
						$totalc=0;
						$equipo=$data1[0]['equipo'];
						$tdis=0;
						$tpres=0;
						$tasig=0;
						$tdan=0;
						$text=0;
						$tdes=0;
						$trob=0;
						$tree=0;
						$ttotalc=0;
                                                unset($resultado);
                                                unset($tresultado);
						for ($j= 0; $j < count($data1); $j++){
							if ($equipo==$data1[$j]['equipo']){
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];
								}
							}else{
								$totalc=$asignado + $prestamo + $danado + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
								$ttotalc+=$totalc;
                                                                $resultado[]=utf8_decode($equipo);
                                                                if ($cantidad>'0'){
                                                                    for ($h=1; $h<=$cantidad; $h++){
                                                                        switch ($nroestatus[$h-1]) {
                                                                            case '86':
                                                                                $resultado[]=$disponible;
                                                                                break;
                                                                            case '87':
                                                                                $resultado[]=$prestamo;
                                                                                break;
                                                                            case '88':
                                                                                $resultado[]=$asignado;
                                                                                break;
                                                                            case '89':
                                                                                $resultado[]=$danado;
                                                                                break;
                                                                            case '90':
                                                                                $resultado[]=$extraviado;
                                                                                break;
                                                                            case '91':
                                                                                $resultado[]=$desincorporado;
                                                                                break;
                                                                            case '618':
                                                                                $resultado[]=$robado;
                                                                                break;
                                                                            case '619':
                                                                                $resultado[]=$reemplazado;
                                                                                break;
                                                                        }
                                                                    }
                                                                    $resultado[]=$totalc;
                                                                    $pdf->Row($resultado);
                                                                }else{
                                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                                }

								$asignado=0;
								$danado=0;
								$extraviado=0;
								$disponible=0;
								$prestamo=0;
								$reemplazado=0;
								$robado=0;
								$desincorporado=0;
								$totalc=0;
								$equipo=$data1[$j]['equipo'];
                                                                unset($resultado);
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];
								}
							}

						}
						$totalc=$asignado + $danado + $prestamo + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
						$ttotalc+=$totalc;
                                                $resultado[]=utf8_decode($equipo);
                                                $tresultado[]="TOTAL";
                                                if ($cantidad>'0'){
                                                    for ($h=1; $h<=$cantidad; $h++){
                                                        switch ($nroestatus[$h-1]) {
                                                            case '86':
                                                                $resultado[]=$disponible;
                                                                $tresultado[]=$tdis;
                                                                break;
                                                            case '87':
                                                                $resultado[]=$prestamo;
                                                                $tresultado[]=$tpres;
                                                                break;
                                                            case '88':
                                                                $resultado[]=$asignado;
                                                                $tresultado[]=$tasig;
                                                                break;
                                                            case '89':
                                                                $resultado[]=$danado;
                                                                $tresultado[]=$tdan;
                                                                break;
                                                            case '90':
                                                                $resultado[]=$extraviado;
                                                                $tresultado[]=$text;
                                                                break;
                                                            case '91':
                                                                $resultado[]=$desincorporado;
                                                                $tresultado[]=$tdes;
                                                                break;
                                                            case '618':
                                                                $resultado[]=$robado;
                                                                $tresultado[]=$trob;
                                                                break;
                                                            case '619':
                                                                $resultado[]=$reemplazado;
                                                                $tresultado[]=$tree;
                                                                break;
                                                        }
                                                    }
                                                    $resultado[]=$totalc;
                                                    $tresultado[]=$ttotalc;
                                                    $pdf->Row($resultado);
                                                    $pdf->Row($tresultado);
                                                }else{
                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                    $pdf->Row(array('TOTAL',$tdis,$tasig,$tpres,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
                                                }



					}
					
	    }
	}else{ // Reporte Continuo
		$pdf->SetFillColor(255,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetDrawColor(0);
	    $pdf->SetWidths($anchocolumnas);
		$pdf->SetAligns($alincolumnas);
		$pdf->Row($textColumnas);
		for ($i= 0; $i < count($data); $i++){
			if ($data[$i]['responsable']=='DANADO') {
				$responsable="DAÑADO";
			}else if ($data[$i]['responsable']==" "){
				$responsable="SIN USUARIO";
			}else{
				$responsable=utf8_decode($data[$i]['responsable']);
			}
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetFont('Arial','',8);
    		switch ($campos) {
    			case 1:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]])));
    				break;
    			case 2:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]])));

    				break;
    			case 3:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]])));
    				break;
    			case 4:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]])));
    				break;
    			case 5:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]])));
    				break;
    			case 6:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]])));
    				break;
    			case 7:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]])));
    				break;
    			case 8:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]])));
    				break;
    			case 9:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]])));
    				break;
    			case 10:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]])));
    				break;
    			case 11:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]])));
    				break;
    			case 12:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]]),utf8_decode($data[$i][$nroCampos[11]])));
    				break;
    			case 13:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]]),utf8_decode($data[$i][$nroCampos[10]]),utf8_decode($data[$i][$nroCampos[11]]),utf8_decode($data[$i][$nroCampos[12]])));
    				break;
    		}


				$total++;

		}
		$pdf->SetWidths(array(252));
	    $pdf->SetAligns(array('R'));
	    $pdf->Row(array('Total de Equipos '.$total));
	    $pdf->Ln();
	    if ($_REQUEST['consolidado']=="1"){
					$sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_estatus_maestro
					from tblbienes b, tblmaestros t
					where b.id_tipo_maestro=t.id_maestro
					and b.bolborrado=0 and t.bolborrado=0 ";
					if ($_REQUEST['nombre']!=""){
						$sql.="AND b.strnombre LIKE '%".strtoupper($_REQUEST['nombre'])."%' ";
					}
			 		if ($_REQUEST['serial']!=""){
						$sql.="AND b.strserial LIKE '%".strtoupper($_REQUEST['serial'])."%' ";
					}

					if ($_REQUEST['marca']!=""){
						$sql.="AND b.id_marca_maestro='".$_REQUEST['marca']."' ";
					}
			 		if ($_REQUEST['modelo']!=""){
						$sql.="AND b.id_modelo_maestro='".$_REQUEST['modelo']."' ";
					}
			 		if ($_REQUEST['categoria']!=""){
						$sql.="AND b.id_categoria_maestro='".$_REQUEST['categoria']."' ";
					}
			 		if ($_REQUEST['tipo']!=""){
						$sql.="AND b.id_tipo_maestro in (".$_REQUEST['tipo'].") ";
					}
			 		if ($_REQUEST['gerencia']!=""){
						$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
					}
			 		if ($_REQUEST['unidad']!=""){
						$sql.="AND b.id_unidad_maestro='".$_REQUEST['unidad']."' ";
					}
			 		if ($_REQUEST['responsable']!=""){
						$sql.="AND b.id_responsable='".strtoupper($_REQUEST['responsable'])."' ";
					}
			 		if ($_REQUEST['garantia']!=""){
						$sql.="AND b.lnggarantia='".$_REQUEST['garantia']."' ";
					}
			 		if ($_REQUEST['proveedor']!=""){
						$sql.="AND b.id_proveedor='".$_REQUEST['proveedor']."' ";
					}
			 		if ($_REQUEST['estatus']!=""){
						$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";
					}
			 		if ($_REQUEST['ubicacion']!=""){
						$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
					}

					$sql.=" group by t.stritema, b.id_tipo_maestro, b.id_estatus_maestro order by b.id_tipo_maestro,b.id_estatus_maestro";

		            $conn->sql=$sql;
					$data1=$conn->ejecutarSentencia(2);
					$data1=$conn->ejecutarSentencia(2);
					$pdf->SetFont('Arial','B',8);
					$pdf->SetFillColor(255,0,0);
					$pdf->SetTextColor(255,255,255);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3);
                                        switch ($cantidad) {
                                            case 1:
                                                $pdf->SetWidths(array(40,23,20));
                                                $pdf->SetAligns(array('C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),'Total'));
                                                $pdf->SetAligns(array('L','R','R'));
                                                break;
                                            case 2:
                                                $pdf->SetWidths(array(40,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R'));
                                                break;
                                            case 3:
                                                $pdf->SetWidths(array(40,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R'));
                                                break;
                                            case 4:
                                                $pdf->SetWidths(array(40,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R'));
                                                break;
                                            case 5:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R'));
                                                break;
                                            case 6:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R'));
                                                break;
                                            case 7:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
                                                break;
                                            case 8:
                                                $pdf->SetWidths(array(40,23,23,23,23,23,23,23,23,20));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo',utf8_decode($estatus[0]),utf8_decode($estatus[1]),utf8_decode($estatus[2]),utf8_decode($estatus[3]),utf8_decode($estatus[4]),utf8_decode($estatus[5]),utf8_decode($estatus[6]),utf8_decode($estatus[7]),'Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
                                                break;
                                            default:
                                                $pdf->SetWidths(array(40,20,20,20,20,20,25,20,25,25));
                                                $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
                                                $pdf->Row(array('Equipo','Disponible','Asignado',utf8_decode('Préstamo'),utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
                                                $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));


                                        }
					$pdf->SetFont('Arial','',8);
					$pdf->SetFillColor(255,255,255);
					$pdf->SetTextColor(0);
					$pdf->SetDrawColor(0);
					$pdf->SetLineWidth(.3);
					if ($data1){
						$asignado=0;
						$danado=0;
						$extraviado=0;
						$disponible=0;
						$prestamo=0;
						$reemplazado=0;
						$robado=0;
						$desincorporado=0;
						$totalc=0;
						$equipo=$data1[0]['equipo'];
						$tdis=0;
						$tpres=0;
						$tasig=0;
						$tdan=0;
						$text=0;
						$tdes=0;
						$trob=0;
						$tree=0;
						$ttotalc=0;
                                                unset($resultado);
                                                unset($tresultado);
						for ($j= 0; $j < count($data1); $j++){
							if ($equipo==$data1[$j]['equipo']){
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];
								}
							}else{
								$totalc=$asignado + $prestamo + $danado + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
								$ttotalc+=$totalc;
                                                                $resultado[]=utf8_decode($equipo);
                                                                if ($cantidad>'0'){
                                                                    for ($h=1; $h<=$cantidad; $h++){
                                                                        switch ($nroestatus[$h-1]) {
                                                                            case '86':
                                                                                $resultado[]=$disponible;
                                                                                break;
                                                                            case '87':
                                                                                $resultado[]=$prestamo;
                                                                                break;
                                                                            case '88':
                                                                                $resultado[]=$asignado;
                                                                                break;
                                                                            case '89':
                                                                                $resultado[]=$danado;
                                                                                break;
                                                                            case '90':
                                                                                $resultado[]=$extraviado;
                                                                                break;
                                                                            case '91':
                                                                                $resultado[]=$desincorporado;
                                                                                break;
                                                                            case '618':
                                                                                $resultado[]=$robado;
                                                                                break;
                                                                            case '619':
                                                                                $resultado[]=$reemplazado;
                                                                                break;
                                                                        }
                                                                    }
                                                                    $resultado[]=$totalc;
                                                                    $pdf->Row($resultado);
                                                                }else{
                                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                                }

								$asignado=0;
								$danado=0;
								$extraviado=0;
								$disponible=0;
								$prestamo=0;
								$reemplazado=0;
								$robado=0;
								$desincorporado=0;
								$totalc=0;
								$equipo=$data1[$j]['equipo'];
                                                                unset($resultado);
								if ($data1[$j]['id_estatus_maestro']=='86'){
									$disponible=$data1[$j]['count'];
									$tdis+=$disponible;
								}
								if ($data1[$j]['id_estatus_maestro']=='87'){
									$prestamo=$data1[$j]['count'];
									$tpres+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='88'){
									$asignado=$data1[$j]['count'];
									$tasig+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='89'){
									$danado=$data1[$j]['count'];
									$tdan+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='90'){
									$extraviado=$data1[$j]['count'];
									$text+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='91'){
									$desincorporado=$data1[$j]['count'];
									$tdes+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='618'){
									$robado=$data1[$j]['count'];
									$trob+=$data1[$j]['count'];
								}
								if ($data1[$j]['id_estatus_maestro']=='619'){
									$reemplazado=$data1[$j]['count'];
									$tree+=$data1[$j]['count'];
								}
							}

						}
						$totalc=$asignado + $danado + $prestamo + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
						$ttotalc+=$totalc;
                                                $resultado[]=utf8_decode($equipo);
                                                $tresultado[]="TOTAL";
                                                if ($cantidad>'0'){
                                                    for ($h=1; $h<=$cantidad; $h++){
                                                        switch ($nroestatus[$h-1]) {
                                                            case '86':
                                                                $resultado[]=$disponible;
                                                                $tresultado[]=$tdis;
                                                                break;
                                                            case '87':
                                                                $resultado[]=$prestamo;
                                                                $tresultado[]=$tpres;
                                                                break;
                                                            case '88':
                                                                $resultado[]=$asignado;
                                                                $tresultado[]=$tasig;
                                                                break;
                                                            case '89':
                                                                $resultado[]=$danado;
                                                                $tresultado[]=$tdan;
                                                                break;
                                                            case '90':
                                                                $resultado[]=$extraviado;
                                                                $tresultado[]=$text;
                                                                break;
                                                            case '91':
                                                                $resultado[]=$desincorporado;
                                                                $tresultado[]=$tdes;
                                                                break;
                                                            case '618':
                                                                $resultado[]=$robado;
                                                                $tresultado[]=$trob;
                                                                break;
                                                            case '619':
                                                                $resultado[]=$reemplazado;
                                                                $tresultado[]=$tree;
                                                                break;
                                                        }
                                                    }
                                                    $resultado[]=$totalc;
                                                    $tresultado[]=$ttotalc;
                                                    $pdf->Row($resultado);
                                                    $pdf->Row($tresultado);
                                                }else{
                                                    $pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));
                                                    $pdf->Row(array('TOTAL',$tdis,$tasig,$tpres,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
                                                }
                                        }
	    }














	}
    $pdf->Output();
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


$pdf->Output();


	
		
?>

