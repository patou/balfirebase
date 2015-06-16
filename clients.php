<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Clients.class.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('content-type: application/json; charset=utf-8');
try {
	$clients = Clients::getClients();
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode($clients);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}