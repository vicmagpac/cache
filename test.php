<?php

require_once 'Cache.php';

//verifica se a frase já está no cache
$cache = new Cache();
$frase = $cache->read('frase-dia');
//se não houver frase no cache ou já tiver expirado
if(!$frase) {
	// Cria uma nova frase e salva-a no cache por 30s
    $frase = 'Estudo, criando um cache :) - ('. date('H:i:s') .')';
    $cache->save('frase-dia', $frase, '30 seconds');
}

echo "<p>{$frase}</p>";