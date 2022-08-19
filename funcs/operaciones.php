<?php

if (!isset($_POST['func'])){
    echo "acceso no contemplado";
    exit();    
}

$func = htmlspecialchars($_POST['func']);

switch ($func) {
    case "crearImagen":

	$palabra = utf8_decode(htmlspecialchars($_POST['palabra']));
	
	header("Content-Type: image/png");
	$im = @imagecreate(250, 50) or die("Cannot Initialize new GD image stream");


	$bg_r = rand(189,255);
	$bg_g = rand(189,255);
	$bg_b = rand(189,255);
	$color_fondo = imagecolorallocate($im, $bg_r, $bg_g, $bg_b);

	$co_r = rand(0,66);

	$co_g = rand(0,126);
	$co_b = rand(0,126);
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
	    case 1:
		$elFiltro = IMG_FILTER_EDGEDETECT;
		break;

	    case 2:
		$elFiltro = IMG_FILTER_NEGATE;
		break;

	    case 3:
		$elFiltro = IMG_FILTER_EMBOSS;
		break;
	}

	if ($filtro >= 1 && $filtro <=3){
            imagefilter($im,$elFiltro);
	}
	
	imagepng($im);
	imagedestroy($im);    
	break;

    case "crearPwd":
	$largo = intval(htmlspecialchars($_POST['largo']));
	$chars= @$_POST['chars'];
	$keys = str_split("0123456789");
	if (@count($chars)>=1){
            $opciones=array();
            foreach ($chars as &$char){
		if (intval($char) == 1){
                    $keys = array_merge($keys,str_split("ABCDEFGHJKLMNPQRSTUVWXYZ"));
		}
		if (intval($char) == 2){
                    $keys = array_merge($keys,str_split("abcdefghijkmnpqrstuvwxyz"));
		}
		if (intval($char) == 3){
                    $keys = array_merge($keys,str_split("!$%&)=;?!_:@"));
		}
            }
	}

	echo '<pre>';
	for ($i = 0; $i <= ($largo-1); $i++){
            $o = rand(0, (count($keys)-1));
            echo $keys[$o];
	}
	echo '</pre>';

	
        
	break;

    case "escribirBd":
	include('bdconfig.php');
	$tiempo = intval(htmlspecialchars($_POST['tiempo']));
	$zona = intval(htmlspecialchars($_POST['zona']));

	$bd = mysqli_connect($srv, $usr, $pwd, $dbn);
	if (mysqli_connect_errno()) {
            printf("Falló la conexión: %s\n", mysqli_connect_error());
            exit();
	}

	$fecha = date("Y-m-d H:i:s");
	
	$query = "INSERT INTO Registros (fecha, tiempoId, zonaId) VALUES ('$fecha','$tiempo','$zona');";


	if (mysqli_query($bd,$query) === TRUE) {
            printf("Se escribió el nuevo registro.\n");
	} else {
            printf("Error: %s\n", mysqli_error($bd));
	}  
	break;

    case "consultaBd":
	include('bdconfig.php');
	$conn = new mysqli($srv, $usr, $pwd, $dbn);
	if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
	}

	$tiempo = intval(htmlspecialchars($_POST['tiempo']));
	$zona  = intval(htmlspecialchars($_POST['zona']));
	$ano = intval(htmlspecialchars($_POST['ano']));
	$where = array();
	
	
	if ($tiempo <= 2 && $tiempo != 0){
            $where[] = 'tiempoId = '.$tiempo;
	}

	if ($zona <= 5 && $zona != 0){
            $where[] = 'zonaId = '.$zona;
	}


	$finalWhere = " WHERE YEAR(fecha) = '$ano'";
	//where event_date between '2018-01-01' and '2018-01-31';

	if (count($where) >=1){    
            foreach ($where as &$wh){

		$finalWhere .= " AND ".$wh ;

            }
	}

	$sql = "SELECT * FROM Info$finalWhere;";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
            echo '
<!DOCTYPE html>
<style>
table {
  width:100%;
  font-family: arial;
  font-size: x-small;
  border-collapse: collapse;
}

td, th {
  border: 1px solid #999;
  padding: 0.5rem;
  text-align: left;
}
</style>



<table>
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Tiempo</th>
      <th>Macrozona</th>
    </tr>
  </thead>
  <tbody>
	    ';
            
            // output data of each row
            while($row = $result->fetch_assoc()) {
		echo "<tr><td>" . $row["fecha"]. "</td><td>" . $row["tiempo"]. "</td><td>" . $row["zona"]. "</td></tr>";
            }
            echo '</tbody></table>';
	} else {
            echo "0 results";
	}
	$conn->close();


	
	break;


    case "graphBd":
	include('plot.php');
	include('bdconfig.php');

	// Create connection
	$conn = new mysqli($srv, $usr, $pwd, $dbn);
	// Check connection
	if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
	}

	$ano = intval(htmlspecialchars($_POST['ano']));
	$sql = "select
	(select count(*) from Info where tiempoId =1 and zonaId =1 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t1m1,
	(select count(*) from Info where tiempoId =1 and zonaId =2 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t1m2,
	(select count(*) from Info where tiempoId =1 and zonaId =3 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t1m3,
	(select count(*) from Info where tiempoId =1 and zonaId =4 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t1m4,
	(select count(*) from Info where tiempoId =1 and zonaId =5 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t1m5,
	(select count(*) from Info where tiempoId =2 and zonaId =1 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t2m1,
	(select count(*) from Info where tiempoId =2 and zonaId =2 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t2m2,
	(select count(*) from Info where tiempoId =2 and zonaId =3 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t2m3,
	(select count(*) from Info where tiempoId =2 and zonaId =4 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t2m4,
	(select count(*) from Info where tiempoId =2 and zonaId =5 and fecha BETWEEN '$ano-01-01 00:00:00' AND '$ano-12-31 23:59:59') as t2m5;";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    header ("Content-type: image/png");
            $plot = new plot2D();
	    $plot->setTitle("El tiempo en ".$ano);
	    $plot->setDescription("Zonas", "Registros");
	    $plot->setGrid();

            while($row = $result->fetch_assoc()) {
		$plot->addCategory("Despejado", 0x00, 0x00, 0xAA);
		$plot->addItem("Despejado", "Norte", $row["t1m1"]);
		$plot->addItem("Despejado", "Centro",$row["t1m2"]);
		$plot->addItem("Despejado", "CentroSur", $row["t1m3"]);
		$plot->addItem("Despejado", "Sur", $row["t1m4"]);
		$plot->addItem("Despejado", "Austral", $row["t1m5"]);
		$plot->addCategory("Nublado", 0x55, 0x55, 0x55);
		$plot->addItem("Nublado", "Norte", $row["t2m1"]);
		$plot->addItem("Nublado", "Centro",$row["t2m2"]);
		$plot->addItem("Nublado", "CentroSur", $row["t2m3"]);
		$plot->addItem("Nublado", "Sur", $row["t2m4"]);
		$plot->addItem("Nublado", "Austral", $row["t2m5"]);
            }
            
	    $plot->printGraph();
	    $plot->destroy();
	}

	$conn->close();

	
    default:
	echo "opción recibida no existe";
}
?>
