<!DOCTYPE html>
<html>
    <head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/script.js" defer></script>
    </head>

    <body>

	<div id="res">
	    <h2>Portafolio operativo</h2>
	    <iframe class="ifr" src="README" name="ifr"></iframe><br>
	    <input onclick="cres();" type="button" value="Cerrar"> 
	    <form id="infobtn" style="display:none;" action="README" target="ifr"><input type="submit" value="Info"></form>
	</div>

	<div class="container">


	    
	    <div id="crearImagen" class="oper">
		<h2>Crear imagen con texto</h2>
		<form action="funcs/operaciones.php" method="POST" target="ifr">
		    <input type="hidden" name="func" value="crearImagen">
		    Palabra que aparecerá en la imagen:<br>
		    <input maxlength="16" type="text" name="palabra" value="texto de prueba" required><br>

		    Filtro:
		    <select name="filtro">
			<option value="0" selected>ninguno</option>
			<option value="1">detectar bordes</option>
			<option value="2">colores negativos</option>
			<option value="3">relieve</option>

		    </select><br><br>
		    <input onunload="vr();" onclick="vr();" type="submit" value="Crear imagen">
		</form>
		<div class="info">
		    <pre>&lt;?php
$palabra = utf8_decode(htmlspecialchars($_POST['palabra']));	
header("Content-Type: image/png");
$im = @imagecreate(250, 50) or die("Cannot Initialize new GD image stream");


$bg_r = rand(189,255); $bg_g = rand(189,255); $bg_b = rand(189,255);
$color_fondo = imagecolorallocate($im, $bg_r, $bg_g, $bg_b);
$co_r = rand(0,66); $co_g = rand(0,126); $co_b = rand(0,126);
$color_texto = imagecolorallocate($im, $co_r, $co_g, $co_b);


$palabra = str_split($palabra);

$pos_x = 5;
$cont = 0;
foreach ($palabra as &$letra){
    $cont++;
    $pos_y = rand(15,25);
    if ($cont <= 16){
	imagestring($im, 5, $pos_x, $pos_y, $letra, $color_texto);
	$pos_x = $pos_x + 15;
    } else {
	break;
    }
}

$filtro = intval(htmlspecialchars($_POST['filtro']));
switch ($filtro){
    case 1: $elFiltro = IMG_FILTER_EDGEDETECT; break;
    case 2: $elFiltro = IMG_FILTER_NEGATE; break;
    case 3: $elFiltro = IMG_FILTER_EMBOSS; break;
}

if ($filtro >= 1 && $filtro <=3){
    imagefilter($im,$elFiltro);
}

imagepng($im);
imagedestroy($im);
?&gt;
		    </pre>
		</div>
	    </div>

	    <div id="crearPwd" class="oper">
		<h2>Crear password</h2>
		<form action="funcs/operaciones.php" method="POST" target="ifr">
		    <input type="hidden"   name="func"   value="crearPwd">
		    <input type="checkbox" checked disabled>Números<br>
		    <input type="checkbox" name="chars[]" value="1" /> Mayúsculas<br>
		    <input type="checkbox" name="chars[]" value="2" /> Minúsculas<br>
		    <input type="checkbox" name="chars[]" value="3" /> Símbolos<br>

		    Largo: 
		    <select name="largo">
			<option value="4" selected>4</option>
			<option value="8">8</option>
			<option value="12">12</option>
		    </select><br><br>
		    <input onclick="vr();" type="submit" value="Crear contraseña">
		</form>
	    </div>

	    <div id="escribirBd" class="oper">
		<h2>Escribir en base de datos</h2>
		<form action="funcs/operaciones.php" method="POST" target="ifr">
		    <input type="hidden" name="func" value="escribirBd">
		    En este momento, el día está 
		    <select name="tiempo">
			<option value="1" selected>despejado</option>
			<option value="2">nublado</option><br>
		    </select><br>en la zonazona

		    <select name="zona">
			<option value="1" selected>Norte</option>
			<option value="2" selected>Centro</option>
			<option value="3" selected>Centro sur</option>
			<option value="4" selected>Sur</option>
			<option value="5" selected>Austral</option>
		    </select>		    
		    <br>
		    <div class="nota">Zona Norte: Arica y Parinacota, Tarapacá, Antofagasta, Atacama
			Zona Centro: Coquimbo, Valparaíso, Metropolitana
			Zona Centro Sur: O'Higgins, Maule, Ñuble y Biobío 
			Zona Sur: La Araucanía, Los Lagos y Los Ríos
			Zona Austral: Aysén y Magallanes y la Antártica Chilena
			Fuente: <a target="_blank" href="https://ayuda.anid.cl/hc/es/articles/360048562731-9-C%C3%B3mo-se-distribuyen-las-regiones-en-cada-macrozona-">anid.cl</a>.
		    </div>
		    <input onclick="vr();" type="submit" value="Guardar registro">
		</form>

		<div class="info">
		    <img src="img/bd_tablas.png">
		    <pre>Ejemplo:
INSERT INTO Registros (fecha, tiempoId, zonaId)
VALUES ('<?php echo date("Y-m-d H:i:s"); ?> ','1','3');
		    </pre>
		</div>
	    </div>
	    
	    <div id="consultaBd" class="oper">
		<h2>Consultar base de datos</h2>
		<form action="funcs/operaciones.php" method="POST" target="ifr">
		    <input type="hidden" name="func" value="consultaBd">
		    Ver registro de días
		    <select name="tiempo">
			<option value="1">despejados</option>
			<option value="2">nublados</option>
			<option value="0" selected>todos</option>
		    </select><br>
		    en la zona
		    <select name="zona">
			<option value="1">Norte</option>
			<option value="2">Centro</option>
			<option value="3">Centro sur</option>
			<option value="4">Sur</option>
			<option value="5">Austral</option>
			<option value="0" selected>Todas</option>

		    </select>.		    
		    <br>
		    Año:
		    <select name="ano">
			<option value="2020" selected>2020</option>
			<option value="2021">2021</option>
			<option value="2022">2022</option>
		    </select>
		    
		    <br><br>
		    <input onclick="vr();" type="submit" value="Realizar consulta">
		</form>
		<div class="info">
		    <img src="img/bd_info.png">
		    <pre>Ejemplo:
SELECT * FROM Info
WHERE tiempoId = 2
AND zonaId = 4
AND YEAR(fecha) = '2022';
		    </pre>
		</div>

	    </div>


	    <div id="graphBd" class="oper">
		<h2>Gráfico con datos obtenidos de base de datos</h2>
		<form action="funcs/operaciones.php" method="POST" target="ifr">
		    <input type="hidden" name="func" value="graphBd">
		    Seleccionar año a consultar: 
		    <select name="ano">
			<option value="2020" selected>2020</option>
			<option value="2021">2021</option>
			<option value="2022">2022</option>
		    </select>
		    
		    <br><br>
		    <input onclick="vr();" type="submit" value="Realizar consulta">
		</form>
		<div class="info">
		</div>

	    </div>


	    

    </body>

</html>
