<?php

/*
Prop�sito:
- Este script sirve para subir archivos gr�ficos, ya sean gif, jpg o png, a trav�s de un formulario. Redimensionado las im�genes grandes en otras menores, si as� se indica.
- Tambi�n sirve para subir otro tipo de ficheros, con extensi�n deseada.

Configuraci�n:
- Toda la configuraci�n esta indicado al inicio del mismo, "// CONFIGURACI�N" tan solo tener cuidado como se ponen los directorios y que estos tengan los permisos adecuados, 777.
- Salvar con el nombre deseado con extensi�n .php

Caracter�sticas:
- No sobrescribe archivos.
- No muestra ficheros que no sean gr�ficos.
- Muestra las im�genes en un directorio, por letra inicial o todas.
- Redimensiona la imagen si es muy grande y se marca la opci�n.
- Limitado el tama�o de las im�genes a subir, tanto en peso como en anchura y altura m�xima y m�nima.
- Limitado el tipo de im�genes a subir, por extensi�n.
- Bastante documentado.
- No necesita el uso de una base de datos.
- Muestra errores si la librer�a GD esta limitada.
- Opci�n oculta para ver caracter�sticas y versi�n de la librer�a GD, op=depurar.
- Funciona con register_globlas en off.
- El html generado es v�lido xhtml 1.1.
- El script puede tener cualquier nombre.
- Envia por mail a los administradores el fichero recien subido o el enlace, seg�n $enviar_adjunto.
- Buscador de archivos, basado en el nombre.

Redimensionamiento:
- $red_permitir='si' el usuario podr� elegir entre no redimensionar la imagen o hacerlo al tama�o seleccionado.
- $red_permitir='no' autom�ticamente se redimensionara la imagen al primer tama�o en $tamanos.
- Si la imagen es menor que las nuevas medidas no se redimensiona, es decir que no amplia la imagen.
- La imagen redimensionada se hace manteniendo la proporci�n.

Requisitos:
- Servidor con soporte PHP. Recomendado versi�n 4.3 o superior
- Librer�a GD. Recomendada versi�n 2.0.1 o superior
- Los directorios donde se almacenara tiene que tener permisos 777.
- Ser humano o compatible.

Agradecimientos:
- A la gente de http://www.altafidelidad.org por suponer que podr�a hacerlo y obligarme a hacerlo.
- A HarryLine de http://www.kualda.com por dejarme un cacho de espacio web.
- A la gente de http://www.php-hispano.net por ser una buena web para aprender y...  por a�adir el script.
- A los que hacen la documentaci�n en http://www.php.net, de nada sirve un lenguaje de programaci�n sino esta bien documentado.
- A http://www.google.es por que TODO esta all�.

Licencia:
- Moralmente esta obligado a mantenerme como autor del script y a comunicarme cualquier sugerencia para mejorarlo o fallos.
- Adem�s es GPL (�no?)
- Y estar�a bien que me dijeran que les parece y donde lo usa.

Script creado por: www.NoSetup.org <NoSetup.tk@gmail.com>

Actualizado: 29/01/2007

*/

// CONFIGURACI�N

// Para los archivos de imagenes
$direct_img = array('../uploads/fotos/');	// Directorios para imagenes
$extensi_img = array('.jpg','.jpeg','.gif','.png');		// Extensiones permitidas para imagenes
$tamano_img=512000;              				// Tama�o m�ximo de la imagen, en bytes
$anchura_min=0;                					// Anchura m�nima, en pixels. 0 para no limites
$anchura_max=0;              					// Anchura m�xima, en pixels. 0 para no limites
$altura_min=0;                 					// Altura m�nima, en pixels. 0 para no limites
$altura_max=0;               					// Altura m�xima, en pixels. 0 para no limites
$red_permitir='si';						// Permitir que el usuario eliga si redimensionar
// Tama�os a elegir, el primero es el tama�o por defecto
$tamanos=array('100x100','100x200','200x100','200x200');
// Normas sobre la subida de ficheros
$normas_img='Normas de subida de <b>imagenes</b>.';

// Para el resto de los archivos
$direct_fic = array('./temp1/','./Temp2/');			// Directorios para ficheros
$extensi_fic = array('.swf','.doc','.txt');			// Extensiones permitidas para ficheros
$tamano_fic=1048576;              				// Tama�o m�ximo del fichero, en bytes
// Normas sobre la subida de ficheros
$normas_fic='Normas de subida de <b>ficheros</b>.';

// Mails al que se envia el archivo recien subido, separados con comas. Dejar en blanco para no enviar mail
$correo='***@***.***,zzz@zzz.zzz';
// Para enviar el fichero como adjunto en el mail poner 'si', para enviar solo el enlace 'no'
$enviar_adjunto_img='si';					// Para imagenes
$enviar_adjunto_fic='no';					// Para archivos

// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR
// NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR, NO TOCAR

$version='v.3.4.9';						// Versi�n del programa.
$fichero_ruta;							// Ruta relativa del fichero
$link;								// Ruta absoluta del fichero
$ext;								// Extensi�n del archivo

// Para calcular el tiempo que tarda en cargar la p�gina
$mtime=microtime();						// Tiempo actual en microsegundos
$mtime=explode(" ",$mtime);					// Dividir el tiempo en microsegudos y segundos
$starttime=$mtime[1]+$mtime[0];					// Sumarles, tiempo donde se ha iniciado el script

// FORMULARIO DE SUBIDA DE IMAGENES
function formulario_img()
{
	global $direct_img,$tamano_img,$red_permitir,$tamanos,$normas_img;

	echo '<h2>Formulario de subida imagenes.</h2>';
	echo '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?op=subir_fichero_img" method="post">';
	echo '<p>';
	echo '<input type="hidden" name="tamano_img" value="'.$tamano_img.'" />';
	echo 'Enviar un archivo:<br />';
	echo '<input name="archivo" size="50" type="file" /><br />';

	// Para poner los directorios de imagenes
	echo 'Directorio de imagenes:<br />';
	echo '<select name="directorio" size="1">';
    foreach ($direct_img as $directorio)
		echo '<option value="'.$directorio.'">'.$directorio.'</option>';
	echo '</select>';
	echo '<br />';

	// Para redimensionar imagenes
	if($red_permitir=='si') // El usuario elige si se redimensiona la imagen
	{
		echo '�Redimensionar la imagen?<br />';
		echo '<select name="red_valor" size="1">';
		echo '<option value="no">Sin redimensionar</option>';
		foreach ($tamanos as $tamano)
			echo '<option value="'.$tamano.'">'.$tamano.'</option>';
		echo '</select>';
	}
	else // Obligado a redimensionar a la primera opci�n
		echo '<input type="hidden" name="red_valor" value="'.$tamanos[0].'" />';
	echo '<br />';

	echo '<input type="submit" name="submit_img" value="Enviar" />';
	echo '</p>';
	echo '</form>';

	echo '<p>'.$normas_img.'</p>';				// Especificar normas
}

// FORMULARIO DE SUBIDA DE ARCHIVOS
function formulario_fic()
{
	global $direct_fic,$tamano_fic,$normas_fic;

	echo '<h2>Formulario de subida de archivos.</h2>';
	echo '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?op=subir_fichero_fic" method="post">';
	echo '<p>';
	echo '<input type="hidden" name="tamano_fic" value="'.$tamano_fic.'" />';
	echo 'Enviar un archivo:<br />';
	echo '<input name="archivo" size="50" type="file" /><br />';

	// Para poner los directorios de archivos
	echo 'Directorio de archivos:<br />';
	echo '<select name="directorio" size="1">';
	// Para poner los directorios
    foreach ($direct_fic as $directorio)
		echo '<option value="'.$directorio.'">'.$directorio.'</option>';
	echo '</select>';
	echo '<br />';
	echo '<input type="submit" name="submit_fic" value="Enviar" />';
	echo '</p>';
	echo '</form>';

	echo '<p>'.$normas_fic.'</p>';				// Especificar normas
}

// SUBIR ARCHIVOS DE IMAGENES
function subir_fichero_img()
{
	global $extensi_img,$ext,$enviar_adjunto_img;

	if (isset($_POST['submit_img']))
	{
		// Datos de la imagen original
		$fichero1=$_FILES['archivo']['name'];		// Nombre del fichero
		$fichero1=strtolower($fichero1);		// Nombre del fichero (min�sculas)

		// Miro la extensi�n
		$ext=strrchr($fichero1,".");			// Extensi�n del archivo
		if(in_array ($ext,$extensi_img))
			subir_imagenes();
		else
			error ('<p>Ese archivo no se puede subir, no tiene la extensi�n correcta.</p>');

		// Para enviar el mail
		enviar_mail($fichero1,$enviar_adjunto_img);
	}
	else
		error('<p>Operaci�n no v�lida.</p>');
}

// SUBIR ARCHIVOS
function subir_fichero_fic()
{
	global $extensi_fic,$ext,$enviar_adjunto_fic;

	if (isset($_POST['submit_fic']))
	{
		// Datos del archivo original
		$fichero1=$_FILES['archivo']['name'];		// Nombre del fichero
		$fichero1=strtolower($fichero1);		// Nombre del fichero (min�sculas)

		// Miro la extensi�n
		$ext=strrchr($fichero1,".");			// Extensi�n del archivo
		if(in_array ($ext,$extensi_fic))
			subir_fic();
		else
			error ('<p>Ese archivo no se puede subir, no tiene la extensi�n correcta.</p>');

		// Para enviar el mail
		enviar_mail($fichero1,$enviar_adjunto_fic);
	}
	else
		error('<p>Operaci�n no v�lida.</p>');
}

// PARA SUBIR IMAGENES
function subir_imagenes()
{
	global $tamano_img,$anchura_min,$anchura_max,$altura_min,$altura_max,$fichero_ruta,$link,$ext;

	// Datos de la imagen original
	$fichero1=$_FILES['archivo']['name'];			// Nombre del fichero
	$fichero1=strtolower($fichero1);			// Nombre del fichero (min�sculas)

	// Busco si el nombre del fichero tiene espacios, pues son problematicos
	$fichero=str_replace(" ","_",$fichero1);
	if($fichero!=$fichero1)
		echo '<p>El nombre del fichero contiene espacios '.$fichero1.', se cambiara a '.$fichero.'</p>';
		
	$directorio=$_POST['directorio'];			// Directorio
	$fichero_ruta="$directorio$fichero";			// Ruta del fichero donde se guardara
	$temporal=$_FILES['archivo']['tmp_name'];		// Ruta del fichero temporal
	$red=$_POST['red_valor'];				// Dimensiones a redimensionar la imagen

	// Dimensiones nuevas de la imagen
	$red_datos=explode ('x',$red);				// Dividir cadena en dos partes
	$ancho_nuevo=$red_datos[0];				// Ancho de la imagen redimensionada
	$alto_nuevo=$red_datos[1];				// Alto de la imagen redimensionada

	// Comprobar que esta subido, de forma temporal
	if (!is_uploaded_file ($temporal))
		error ('<p>No ha seleccionado el archivo.</p>');

	// Comprobar que no exista
	if(file_exists ($fichero_ruta))
		error ('<p>El fichero ya existe ('.$fichero_ruta.')</p>');

	// Mover a la ruta
	move_uploaded_file($temporal,$fichero_ruta);

	// Datos de la imagen original
	$datos=getimagesize ($fichero_ruta);

	// Comprobar tama�o, en bytes
	$tamano=filesize($fichero_ruta);
	if ($tamano>$tamano_img)
		error ('<p>Ese archivo no se puede subir, es demasiado grande ('.$tamano_img.' bytes como m�ximo).</p>');

	// Comprobar anchura m�xima
	if (($anchura_max!=0) && ($datos[0]>$anchura_max))
		error ('<p>Ese archivo no se puede subir, su anchura ('.$datos[0].') es superior a la permitida ($anchura_max).</p>');

	// Comprobar anchura m�nima
	if (($anchura_min!=0) && ($datos[0]<$anchura_min))
		error ('<p>Ese archivo no se puede subir, su anchura ('.$datos[0].') es inferior a la permitida ($anchura_min).</p>');

	// Comprobar altura m�xima
	if (($altura_max!=0) && ($datos[1]>$altura_max))
		error ('<p>Ese archivo no se puede subir, su altura ('.$datos[1].') es superior a la permitida ($altura_max).</p>');

	// Comprobar altura m�nima
	if (($altura_min!=0) && ($datos[1]<$altura_min))
		error ('<p>Ese archivo no se puede subir, su altura ('.$datos[1].') es inferior a la permitida ($altura_min).</p>');

	// Hacer una imagen reducida, funciona con GIF, JPG, PNG
	if (($red!='no') && (($datos[1]>$alto_nuevo) || ($datos[0]>$ancho_nuevo)))
	{
		// Comprobar el soporte GD para este tipo de archivo y devuelve identificador de la imagen
		if ($datos[2]==1) // GIF
			if (function_exists("imagecreatefromgif"))
				$img = imagecreatefromgif($fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes GIF en este servidor PHP.</p>');

		if ($datos[2]==2) // JPG
			if (function_exists("imagecreatefromjpeg"))
				$img = imagecreatefromjpeg($fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes JPG en este servidor PHP.</p>');

		if ($datos[2]==3) // PNG
			if (function_exists("imagecreatefrompng"))
				$img = imagecreatefrompng($fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes PNG en este servidor PHP.</p>');

		// Calculo de las nuevas dimensiones de la imagen
		$ancho_orig=$datos[0]; 				// Anchura de la imagen original
		$alto_orig=$datos[1];				// Altura de la imagen original
		if ($ancho_orig>$alto_orig)
		{
			$ancho_dest=$ancho_nuevo;
			$alto_dest=($ancho_dest/$ancho_orig)*$alto_orig;
		}
		else
		{
			$alto_dest=$alto_nuevo;
			$ancho_dest=($alto_dest/$alto_orig)*$ancho_orig;
		}

		// Imagen destino
		// imagecreatetruecolor, solo estan en G.D. 2.0.1 con PHP 4.0.6+
		$img2=@imagecreatetruecolor($ancho_dest,$alto_dest) or $img2=imagecreate($ancho_dest,$alto_dest);

		// Redimensionar
		// imagecopyresampled, solo estan en G.D. 2.0.1 con PHP 4.0.6+
		@imagecopyresampled($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig) or imagecopyresized($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig);

		// Crear fichero nuevo, seg�n extensi�n.
		if ($datos[2]==1) // GIF
			if (function_exists("imagegif"))
				imagegif($img2, $fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes GIF en este servidor PHP.</p>');

		if ($datos[2]==2) // JPG
			if (function_exists("imagejpeg"))
				imagejpeg($img2, $fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes JPG en este servidor PHP.</p>');

		if ($datos[2]==3)  // PNG
			if (function_exists("imagepng"))
				imagepng($img2, $fichero_ruta);
			else
				error('<p>No hay soporte de im&aacute;genes PNG en este servidor PHP.</p>');
	}

	// Datos del fichero nuevo
	$datos2=getimagesize($fichero_ruta);
	clearstatcache(); // para limpiar el cache de tama�o de archivos
	$tamano_nuevo=filesize($fichero_ruta);

	// Direcci�n de la imagen
	$fichero_ruta2=str_replace ('./','',$fichero_ruta); 	// Quitar el ./ inicial
	$link2=pathinfo($_SERVER['PHP_SELF']);			// Informaci�n sobre la ruta
	$servidor=$_SERVER['HTTP_HOST'];			// Servidor donde esta el script
	if ($link2[dirname]=='/')				// Sino esta en una carpeta
		$link2[dirname]='';
	$link='http://'.$servidor.$link2[dirname].'/'.$fichero_ruta2;	// Crear ruta absoluta

	echo '<h2>Datos de la imagen.</h2>';
	echo 'Ruta relativa: '.$fichero_ruta.'<br />';
	echo 'Ruta absoluta: '.$link.'<br />';
	echo 'Tama�o viejo: '.$tamano.' (bytes)<br />';
	echo 'Tama�o nuevo: '.$tamano_nuevo.' (bytes)<br />';
	echo 'Extensi�n: '.$ext.'<br />';
	echo 'Anchura vieja: '.$datos[0].'<br />';
	echo 'Anchura nueva: '.$datos2[0].'<br />';
	echo 'Altura vieja: '.$datos[1].'<br />';
	echo 'Altura nueva: '.$datos2[1].'<br />';
	echo 'Tag viejo: '.$datos[3].'<br />';
	echo 'Tag nuevo: '.$datos2[3].'<br />';
	echo '<br />';
	echo '<img src="'.$fichero_ruta.'" alt="'.$fichero_ruta.'" /><br />';
}

// PARA SUBIR OTRO TIPO DE FICHEROS
function subir_fic()
{
	global $tamano_fic,$fichero_ruta,$link,$ext;

	// Datos del archivo original
	$fichero=$_FILES['archivo']['name'];			// Nombre del fichero
	$fichero=strtolower($fichero);				// Nombre del fichero (min�sculas)
	$directorio=$_POST['directorio'];			// Directorio
	$fichero_ruta="$directorio$fichero";			// Ruta del fichero donde se guardara
	$temporal=$_FILES['archivo']['tmp_name'];		// Ruta del fichero temporal

	// Comprobar que esta subido, de forma temporal
	if (!is_uploaded_file ($temporal))
		error ('<p>No ha seleccionado el archivo.</p>');

	// Comprobar que no exista
	if(file_exists ($fichero_ruta))
		error ('<p>El fichero ya existe ('.$fichero_ruta.')</p>');

	// Mover a la ruta
	move_uploaded_file($temporal,$fichero_ruta);

	// Comprobar tama�o, en bytes
	$tamano=filesize($fichero_ruta);
	if ($tamano>$tamano_fic)
		error ('<p>Ese archivo no se puede subir, es demasiado grande ('.$tamano_fic.' bytes como m�ximo).</p>');

	// Direcci�n del fichero
	$fichero_ruta2=str_replace ('./','',$fichero_ruta); 	// Quitar el ./ inicial
	$link2=pathinfo($_SERVER['PHP_SELF']);			// Informaci�n sobre la ruta
	$link='http://'.$_SERVER['HTTP_HOST'].$link2[dirname].'/'.$fichero_ruta2;

	// Datos del fichero nuevo
	echo '<h2>Datos del fichero.</h2>';
	echo 'Ruta relativa: '.$fichero_ruta.'<br />';
	echo 'Ruta absoluta: '.$link.'<br />';
	echo 'Tama�o: '.$tamano.' (bytes)<br />';
	echo 'Extensi�n: '.$ext.'</p>';
}

// ENLACES IMAGENES
function enlaces_img()
{
	global $direct_img;

	echo '<h2>Elegir archivos gr�ficos a mostrar.</h2>';
	echo '<p>';
    foreach ($direct_img as $directorio)
	{
		echo $directorio.': ';
		for($i=ord('0');$i<=ord('9');$i++)
			echo '<a href="'.$_SERVER['PHP_SELF'].'?op=ver_letras_img&amp;dir='.$directorio.'&amp;letra='.chr($i).'">'.chr($i).'</a> ';
		for($i=ord('a');$i<=ord('z');$i++)
			echo '<a href="'.$_SERVER['PHP_SELF'].'?op=ver_letras_img&amp;dir='.$directorio.'&amp;letra='.chr($i).'">'.chr($i).'</a> ';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?op=ver_letras_img&amp;dir='.$directorio.'&amp;letra=otros">otros</a> ';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?op=ver_letras_img&amp;dir='.$directorio.'&amp;letra=todos">todos</a> ';
		echo '<br />';
	}
	echo '</p>';
}

// ENLACES ARCHIVOS
function enlaces_fic()
{
	global $direct_fic;

	echo '<h2>Elegir directorio de archivos a mostrar.</h2>';
	echo '<p>';
	foreach ($direct_fic as $directorio)
		echo '<a href="'.$_SERVER['PHP_SELF'].'?op=ver_letras_fic&amp;dir='.$directorio.'">'.$directorio.'</a><br />';
	echo '</p>';
}

// PARA VER LOS DIRECTORIOS IMAGENES
function ver_directorios_img()
{
	global $direct_img;

	if(!in_array($_GET[dir],$direct_img))
		error('<p>Por favor, no toque la direcci�n del script.</p>');

	$dir=@opendir($_GET[dir]);					// Abrir directorio
	if($dir==FALSE)
		error('<p>Hay un problema en la configuraci�n del script y esta carpeta no existe.</p>');

	echo '<h2>Mostrando archivos que empiezen por <em>'.$_GET[letra].'</em> del directorio <em>'.$_GET[dir].'</em>.</h2>';
	echo '<p>';

	$datos=array();						// Creo matriz con las entradas

	while (false !== ($file = readdir($dir)))
	{
		$ext=strrchr($file,'.');			// Extraer extensi�n
		$ext=strtolower($ext);				// Pasarla a min�sculas
		$path="$_GET[dir]$file";			// Fichero con ruta

		// Extraer letra inicial
		$file=strtolower($file);			// Pasar a min�sculas
		$letra_inicial=substr($file,0,1); 		// Letra inicial del archivo
		$letra_inicial2=ord($letra_inicial);		// Letra inicial en n�mero

		// Imprimir imagenes
		if ($ext=='.gif' || $ext=='.jpg' || $ext=='.png')
		{
			if ($_GET[letra]=="todos")
				$datos[]=$path; 		// Almaceno entrada

			elseif (($_GET[letra]=='otros') && ($letra_inicial2<ord('0') || ($letra_inicial2>ord('9') && $letra_inicial2<ord('a')) || $letra_inicial2>ord('z')))
				$datos[]=$path; 		// Almaceno entrada

			elseif ($letra_inicial==$_GET[letra])
				$datos[]=$path; 		// Almaceno entrada
		}
	}

	natcasesort($datos);					// Ordenado por nombre

	foreach ($datos as $entry)				// De uno a uno recorro el array
		echo '<img src="'.$entry.'" alt="'.$entry.'" />'.$entry.'<br />';	// Imprimir imagenes

	echo '</p>';
	closedir($dir);						// Cerrar directorio
}

// PARA VER LOS DIRECTORIOS DE ARCHIVOS
function ver_directorios_fic()
{
	global $direct_fic,$extensi_fic;

	if(!in_array($_GET[dir],$direct_fic))
		error('<p>Por favor, no toque la direcci�n del script.</p>');

	$dir=@opendir($_GET[dir]);				// Abrir directorio
	if($dir==FALSE)
		error('<p>Hay un problema en la configuraci�n del script y esta carpeta no existe.</p>');

	echo '<h2>Mostrando archivos del directorio <em>'.$_GET[dir].'</em>.</h2>';

	$datos=array();						// Creo matriz con las entradas

	echo '<ul>';
	while ($elemento = readdir($dir)) 			// Leemos el nombre de los archivos
	{
		// Comprobar extensiones
		$ext=strrchr($elemento,".");			// Extensi�n del archivo
		$ext_correcta='no';
		foreach ($extensi_fic as $extension)
		{
			if($ext==$extension)
				$ext_correcta='si';
		}

		if ($ext_correcta=='si')
			$datos[]=$_GET[dir].$elemento; 		// Almaceno entrada
	}

	natcasesort($datos);						// Ordenado por nombre
	foreach ($datos as $entry)					// De uno a uno recorro el array
		echo '<li><a href="'.$entry.'">'.$entry.'</a></li>';	// Enlaces

	echo '</ul>';

	closedir($dir);						// Cerrar directorio
}

// FUNCI�N BUSCADOR
function buscar()
{
	if(isset($_POST['submit_bus']))
	{
		echo '<h2>Ficheros con nombre: '.$_POST['fichero'].'</h2>';
		$datos=glob($_POST['directorio'].'*'.strtolower($_POST['fichero']).'*');
		foreach ($datos as $entry)					// De uno a uno recorro el array
		{
			if(@getimagesize($entry))
				echo '<img src="'.$entry.'" alt="'.$entry.'" />'.$entry.'<br />';	// Imprimir imagenes
			else
				echo '<p><a href="'.$entry.'">'.$entry.'</a></p>';			// Enlaces
		}
	}
	else
		die('<p>No</p>');
}

// FUNCI�N FORMULARIO BUSCADOR
function buscar_form()
{
	global $direct_img,$direct_fic;

	echo '<h2>Formulario b�squeda de un archivo o imagen, basado en el nombre.</h2>';
	echo '<form action="'.$_SERVER['PHP_SELF'].'?op=buscar" method="post">';
	echo '<p>';
	echo '<input type="text" name="fichero" />';

	echo '<select name="directorio" size="1">';
	$dir=$direct_img;					// Directorio de imagenes
	foreach ($direct_fic as $directorio)			// Incorporo directorio de fichero
	{
		if(!in_array($directorio,$dir))
			$dir[]=$directorio;			// Pero solo si no esta ya incluido
	}
	natcasesort($dir);					// Ordenado
    foreach ($dir as $directorio)
		echo '<option value="'.$directorio.'">'.$directorio.'</option>';
	echo '</select>';

	echo '<input type="submit" name="submit_bus" value="Buscar" />';
	echo '</p>';
	echo '</form>';
}

// CABECERA
function cabecera()
{
	global $version;

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Subir archivos... <?php echo $version; ?></title>
	</head>
	<body>
	<?php
}

// PIE
function pie()
{
	global $starttime;

	?>
	<p>[<a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=formulario">Inicio</a>] [Script creado por: <em><a href="http://www.nosetup.org">NoSetup.org</a></em>] [<a href="http://validator.w3.org/check?uri=referer">Valid XHTML 1.1!</a>]</p>
	<?php
	// Tiempo de carga de la p�gina
	$mtime=microtime();					// Tiempo actual en microsegundos
	$mtime=explode(" ",$mtime);				// Dividir el tiempo en microsegudos y segundos
	$endtime=$mtime[1]+$mtime[0];				// Sumarles, tiempo donde se ha finallizado el script
	$totaltime=$endtime - $starttime;			// Tiempo que tarda en ejecutarse es la diferencia
	echo '<p>P�gina generada en: '.substr($totaltime,0,10).' segundos.</p>';// Me  quedo con 8 decimales
	?>

	</body>
	</html>
	<?php
	die();							// Para acabar el script
}

// MENSAJES DE ERROR
function error($mensaje)
{
	global $fichero1;

	@unlink ($fichero1); 					// Borrar el fichero temporal
	echo $mensaje.'<hr />';					// Imprimir mensaje de error
	pie();							// Cerrar el html
}

// ENVIO DE CORREO
function enviar_mail($fichero,$enviar_adjunto)
{
	global $fichero_ruta,$link,$correo;

	if($correo!='')
	{
		// Envio del archivo por mail
		// Adaptado de http://www.tecnocodigo.com/dipro/php/ver.php?categoria=email&articulo=0
		$asunto = 'Archivo nuevo: '.$link;	 	// Asunto del mensaje

		if($enviar_adjunto=='si')
		{
			// Lectura  del fichero
			$fp = fopen($fichero_ruta,"r");
			$buffer = fread($fp,filesize($fichero_ruta));
			fclose($fp);
			$buffer =chunk_split(base64_encode($buffer));	// Codificaci�n en base64 y divido

			// Cabeceras
			$headers = "MIME-version: 1.0\n";
			$headers .= "Content-type: multipart/mixed; ";
			$headers .= "boundary=\"Message-Boundary\"\n";
			$headers .= "Content-transfer-encoding: 7BIT\n";
			$headers .= "X-attachments: $fichero_ruta";

			// Mensaje
			$mensaje = "--Message-Boundary\n";
			$mensaje .= "Content-type: text/plain; charset=ISO-8859-1\n";
			$mensaje .= "Content-transfer-encoding: 7BIT\n";
			$mensaje .= "Content-description: Mail message body\n\n";
			$mensaje .= 'Alguien ha subido un nuevo archivo la direcci�n es: '."\n".$link;	// Cuerpo del mensaje

			// Adjuntar el fichero
			$mensaje .= "\n\n--Message-Boundary\n";
			$mensaje .= "Content-type: Binary; name=\"$fichero\"\n";
			$mensaje .= "Content-Transfer-Encoding: BASE64\n";
			$mensaje .= "Content-disposition: attachment; filename=\"$fichero\"\n\n";
			$mensaje .= "$buffer\n";
			$mensaje .= "--Message-Boundary--\n";
		}
		else
			$mensaje = 'Alguien ha subido un nuevo archivo la direcci�n es: '."\n".$link;	// Cuerpo del mensaje

		// Envio de mail, la @ es para que no de aviso de error en caso de fallar,
		// pues no queremos que el usuario sepa de esta funci�n
		@mail($correo,$asunto,$mensaje,$headers);
	}
}

// DEPURACI�N
function depurar()
{
	global $version;
	echo '<p>Versi�n PHP: '.phpversion().'<br />';
	echo 'Versi�n del script: '.$version.'<br />';

	// Para la hora
	echo '<p>Informaci�n horaria: <br />';
	echo 'Versi�n horaria: '.setlocale(LC_TIME,'es_ES').'<br />';
	$tiempo=time();																	// Hora actual
	echo 'Hora del servidor '.strftime ('%d/%m/%g @ %H:%M %Z',$tiempo).'<br />';	// Hora local
	echo 'Hora GMT '.gmstrftime ('%d/%m/%g @ %H:%M %Z',$tiempo).'<br />'; 		// Hora GMT
	echo '<p>';

	if (function_exists("gd_info"))
	{
		echo 'Informaci�n sobre libreria GD.<br />';
		$info=gd_info();
		$clave=array_keys($info); 				// Devuelve todas las claves de una matriz
		echo '<b>'.$clave[0].'</b>: '.$info[$clave[0]]; 	// Versi�n de la librer�a GD
		for($i=1;$i<count($clave);$i++)
    		echo '<br /><b>'.$clave[$i].'</b>: '.siNO($info[$clave[$i]]);
    	echo '</p>';
    }
    else
    	echo 'gd_info no esta disponible en el sistema.</p>';

    // Comprobar si ciertas funciones existe.
	echo '<p>Informaci�n sobre ciertas funciones usadas: <br />';

	if (function_exists("imagecreatefromgif"))
		echo 'imagecreatefromgif existe.<br />';
	else
		echo 'imagecreatefromgif NO existe.<br />';

	if (function_exists("imagecreatefromjpeg"))
		echo 'imagecreatefromjpeg existe.<br />';
	else
		echo 'imagecreatefromjpeg NO existe.<br />';

	if (function_exists("imagecreatefrompng"))
		echo 'imagecreatefrompng existe.<br />';
	else
		echo 'imagecreatefrompng NO existe.<br />';

	if (function_exists("imagecreatetruecolor"))
		echo 'imagecreatetruecolor existe.<br />';
	else
		echo 'imagecreatetruecolor NO existe.<br />';

	if (function_exists("imagecreate"))
		echo 'imagecreate existe.<br />';
	else
		echo 'imagecreate NO existe.<br />';

	if (function_exists("imagecopyresampled"))
		echo 'imagecopyresampled existe.<br />';
	else
		echo 'imagecopyresampled NO existe.<br />';

	if (function_exists("imagecopyresized"))
		echo 'imagecopyresized existe.<br />';
	else
		echo 'imagecopyresized NO existe.<br />';

	echo '</p>';
}

// PARA LA DEPURACI�N
function siNO($bool)
{
	if($bool)
		return 'verdadero';
	else
		return 'falso';
}

// FUNCI�N PRINCIPAL
switch($_REQUEST['op'])
{
	// P�gina principal, formulario de subida
    default:
    case 'formulario':
	   	cabecera();
		formulario_img();
		echo '<hr />';
		formulario_fic();
		echo '<hr />';
		enlaces_img();
		echo '<hr />';
		enlaces_fic();
		echo '<hr />';
		buscar_form();
		echo '<hr />';
	  	pie();
	break;

	// Cuando se sube una imagen
    case 'subir_fichero_img':
    	cabecera();
		subir_fichero_img();
		echo '<hr />';
		pie();
	break;

	// Cuando se sube un archivo
    case 'subir_fichero_fic':
    	cabecera();
		subir_fichero_fic();
		echo '<hr />';
		pie();
	break;

	// Cuando se ve imagenes
	case 'ver_letras_img':
	   	cabecera();
		ver_directorios_img();
		echo '<hr />';
		pie();
	break;

	// Cuando se ve archivos
	case 'ver_letras_fic':
	   	cabecera();
		ver_directorios_fic();
		echo '<hr />';
		pie();
	break;

	// Buscador
	case 'buscar':
		cabecera();
		buscar();
		echo '<hr />';
		pie();
	break;

	// Depuraci�n
	case 'depurar':
	    cabecera();
		depurar();
		echo '<hr />';
		pie();
	break;
}

?>