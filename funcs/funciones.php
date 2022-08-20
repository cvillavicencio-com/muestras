<?php

// limpia dato recibido por post. Retorna int.
function intpost($a){
    $r = isset($_POST[$a]) ? intval(htmlspecialchars($_POST[$a])) : false;
    return $r;
}

// hace array_merge entre array existente y resultado de str_split. Retorna array.
function split2array($a, $b, int $c = 1){
    $r = array_merge($a,str_split($b,$c));
    return $r;
}

// convierte array en string, separando elementos por coma y agregando una "y" antes del último.
// segundo argumento es caracter o palabra final (puede ser un punto, un sustantivo común/adjetivo plural o nada).
function poney($a, $b = ''){
    $c = (count($a)-1);
    $r = '';
    for ($i = 0; $i <= $c; $i++){
        if ($i == ($c-1)){
            $sep =' y ';
        } elseif ($i == $c){
            $sep=$b;
        } else {
            $sep = ', ';
        }
        
        $r .= $a[$i].$sep;
    }
    return $r;
}




?>
