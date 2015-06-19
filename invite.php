<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

include('tcpdf/tcpdf.php');
include('fpdi/fpdi.php');

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';
include RACINE_SERVEUR.'modeles/Clients.class.php';
include RACINE_SERVEUR.'modeles/Commandes.class.php';

try {
	$json = file_get_contents('php://input'); 
	$invite = json_decode($json);
	Commandes::addGuests(array($invite));
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode($true);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}
