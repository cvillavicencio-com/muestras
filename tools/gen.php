<script type="text/javascript">
 function ctc(containerid) {
     if (document.selection) {
	 var range = document.body.createTextRange();
	 range.moveToElementText(document.getElementById(containerid));
	 range.select().createTextRange();
	 document.execCommand("copy");
     } else if (window.getSelection) {
	 var range = document.createRange();
	 range.selectNode(document.getElementById(containerid));
	 window.getSelection().addRange(range);
	 document.execCommand("copy");
	 alert("Text has been copied, now paste in the text-area")
     }
 }
</script>
<input type="button" onclick="ctc('qry')"><hr>

<?php

for ($i = 0; $i <= 100; $i++){
    $fecha = rand(1577847600,time());
    $fecha = date("Y-m-d H:i:s",$fecha);
    $tiempo = rand(1,2);
    $zona = rand(1,5);
    $query .= "INSERT INTO Registros (fecha, tiempoId, zonaId) VALUES ('$fecha','$tiempo','$zona');".PHP_EOL;
}

echo '<pre id="qry">'.$query.'</pre>';

?>
<hr>

INSERT INTO Registros (fecha, tiempoId, macrosId) VALUES ('2020-08-01','2','1');
