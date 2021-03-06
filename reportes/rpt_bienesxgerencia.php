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
		$this->Image('../comunes/images/cab_reportes.jpg',20,3,30,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'BIENES TELEM�TICOS POR GERENCIA',0,0,'C');
		$this->Ln(10);
			
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(232,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$sql="select b.*, g.stritemc as gerencia, c.stritema as categoria, 
		case when b.id_unidad_maestro>0 then
		(select '(' ||stritema||')' from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
		else
		''
		end as unidad,t.stritema as tipo, m.stritema as marca,
		case when b.id_responsable >0 and b.id_estatus_maestro in (87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
'DANADO'
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
end end end end end end end end as responsable,  e.stritema as estatus, mo.stritema as modelo,to_char(b.dtmfechafactura,'DD/MM/YYYY') as dtmfecha,
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
		and t.bolborrado=0 and m.bolborrado=0 and e.bolborrado=0 and mo.bolborrado=0  and u.bolborrado=0
and g.stritemb='GERENCIA' and b.id_estatus_maestro in (86,88,89,90,91,618,619)     
ORDER BY id_gerencia_maestro,id_unidad_maestro,responsable,id_ubicacion";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);    

if ($data){
    $gerencia=$data[0]["gerencia"];
    $ubicacion=$data[0]["id_unidad_maestro"];
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
    $pdf->SetWidths(array(36,36,41,41,26,36,36));
	$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C','C'));
	$pdf->Row(array('EQUIPO','MARCA','MODELO','SERIAL','ESTATUS','USUARIO','UBICACI�N'));

	for ($i= 0; $i < count($data); $i++){
		if ($data[$i]['responsable']=='DANADO') {
			$responsable="DA�ADO";
		}else if ($data[$i]['responsable']==" "){
			$responsable="SIN USUARIO";
		}else{			
			$responsable=utf8_decode($data[$i]['responsable']);
		}
    	if ($gerencia!=$data[$i]["gerencia"] || $ubicacion!=$data[$i]["id_unidad_maestro"]){
			$pdf->SetWidths(array(252));
            $pdf->SetAligns(array('R'));
			$pdf->Row(array('Total de Equipos '.$total));
            $pdf->Ln();
            $sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc as gerencia, b.id_estatus_maestro 
			from tblbienes b, tblmaestros t,tblmaestros g 
			where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro 
			and b.bolborrado=0 and t.bolborrado=0 and g.stritemb='GERENCIA' and b.id_estatus_maestro in (86,88,89,90,91,618,619)   and g.stritemc='".$gerencia."' and b.id_unidad_maestro=".$ubicacion." group by t.stritema, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc, b.id_estatus_maestro
			order by b.id_gerencia_maestro,b.id_tipo_maestro,b.id_estatus_maestro";
            $conn->sql=$sql;
			$data1=$conn->ejecutarSentencia(2); 
			$pdf->SetFont('Arial','B',8);		
			$pdf->SetFillColor(255,0,0);		
			$pdf->SetTextColor(255,255,255);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(.3);       
			$pdf->SetWidths(array(50,20,20,20,20,25,20,25,25));
	        $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
			$pdf->Row(array('Equipo','Disponible','Asignado',utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
			
			
			$pdf->SetFillColor(255,255,255);		
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(.3);   
			    
			$pdf->SetWidths(array(50,20,20,20,20,25,20,25,25));
	        $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
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
						$totalc=$asignado + $danado + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
						$ttotalc+=$totalc;
						$pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
						
						
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
				$totalc=$asignado + $danado + $extraviado + $disponible + $desincorporado + $robado + $reemplazado;
				$ttotalc+=$totalc;
				$pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
				$pdf->Row(array('TOTAL',$tdis,$tasig,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
			}
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            $pdf->AddPage();
			$total=0;
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
            $pdf->SetWidths(array(36,36,41,41,26,36,36));
			$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C','C'));
			$pdf->Row(array('EQUIPO','MARCA','MODELO','SERIAL','ESTATUS','USUARIO','UBICACI�N'));
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetFont('Arial','',8);
            $pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L','J'));
            $pdf->Row(array(utf8_decode($data[$i]["tipo"]),$data[$i]["marca"], $data[$i]["modelo"],utf8_decode($data[$i]["strserial"]), utf8_decode($data[$i]["estatus"]),$responsable,utf8_decode($data[$i]["ubicacion"])));
			$total++;
		}
		else
		{
			$total++;
			$pdf->SetFont('Arial','',8);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
            $pdf->SetWidths(array(36,36,41,41,26,36,36));
			$pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L','J'));
            $pdf->Row(array(utf8_decode($data[$i]["tipo"]),$data[$i]["marca"], $data[$i]["modelo"],utf8_decode($data[$i]["strserial"]), utf8_decode($data[$i]["estatus"]),$responsable,utf8_decode($data[$i]["ubicacion"])));
		}
		$gerencia=$data[$i]["gerencia"];
		$ubicacion=$data[$i]["id_unidad_maestro"];
	}
	$pdf->SetWidths(array(252));
    $pdf->SetAligns(array('R'));
    $pdf->Row(array('Total de Equipos '.$total));
    $pdf->Ln(10);
    
$sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc as gerencia, b.id_estatus_maestro 
			from tblbienes b, tblmaestros t,tblmaestros g 
			where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro 
			and b.bolborrado=0 and t.bolborrado=0 and g.stritemb='GERENCIA' and b.id_estatus_maestro in (86,88,89,90,91,618,619)   and g.stritemc='".$gerencia."' and b.id_unidad_maestro=".$ubicacion." group by t.stritema, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc, b.id_estatus_maestro
			order by b.id_gerencia_maestro,b.id_tipo_maestro,b.id_estatus_maestro";
            $conn->sql=$sql;
			$data1=$conn->ejecutarSentencia(2); 
			$pdf->SetFont('Arial','B',8);		
			$pdf->SetFillColor(255,0,0);		
			$pdf->SetTextColor(255,255,255);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(.3);       
			$pdf->SetWidths(array(50,20,20,20,20,25,20,25,25));
	        $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
			$pdf->Row(array('Equipo','Disponible','Asignado',utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
			
			
			
			$pdf->SetFillColor(255,255,255);		
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(.3);   
			    
			$pdf->SetWidths(array(50,20,20,20,20,25,20,25,25));
	        $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
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
						$totalc=$asignado + $danado + $extraviado + $disponible  + $desincorporado + $robado + $reemplazado;
						$ttotalc+=$totalc;
						$pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
						
						
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
				$totalc=$asignado + $danado + $extraviado + $disponible  + $desincorporado + $robado + $reemplazado;
				$ttotalc+=$totalc;
				$pdf->Row(array(utf8_decode($equipo),$disponible,$asignado,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
				$pdf->Row(array('TOTAL',$tdis,$tasig,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
			}
    
    
    
    
    
    
    
    
    $pdf->Output();
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


$pdf->Output();	
		
?>

