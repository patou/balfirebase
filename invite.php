<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  // return only the headers and not the content
  // only allow CORS if we're doing a GET - i.e. no saving for now.
  //if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST') {
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
	header('Access-Control-Allow-Methods: OPTIONS, GET, POST');
	header('content-type: application/json; charset=utf-8');
  //}
  exit;
}

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';
include RACINE_SERVEUR.'modeles/Clients.class.php';
include RACINE_SERVEUR.'modeles/Commandes.class.php';
error_reporting(E_ALL);
try {
	$json = file_get_contents('php://input'); 
	$invite = json_decode($json);
	if ($invite == null || empty($invite['prenom']) || empty($invite['nom']) {
		http_response_code(500);
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header('content-type: application/json; charset=utf-8');
		echo '{"error": "Il faut saisir au moins le prenom et le nom"}';
		exit;
	}
	if (empty($invite['email']))
		$invite['email'] = '';
	if (empty($invite['idCommande']))
		$invite['idCommande'] = '1';
	if (empty($invite['idProduit']))
		$invite['idProduit'] = '3';
	if (empty($invite['valider']))
		$invite['valider'] = 'false';
	
	$inviteId = Commandes::addGuest(array($invite));
	$invite = Invites::getInvite($inviteId);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode($invite);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}
