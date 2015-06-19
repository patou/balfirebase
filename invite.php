<?php
error_reporting(E_ALL);
define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  // return only the headers and not the content
  // only allow CORS if we're doing a GET - i.e. no saving for now.
  //if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST') {
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header('Access-Control-Allow-Methods: OPTIONS, GET, POST');
	
  //}
  exit;
}

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';
include RACINE_SERVEUR.'modeles/Clients.class.php';
include RACINE_SERVEUR.'modeles/Commandes.class.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

try {
	$json = file_get_contents('php://input'); 
	
	$invite = json_decode($json, true);
	if ($invite == null || empty($invite['prenom']) || empty($invite['nom'])) {
		
		http_response_code(500);
		header('content-type: application/json; charset=utf-8');
		echo '{"error": "Il faut saisir au moins le prenom et le nom"}';
		exit;
	}
	
	
	if (empty($invite['email'])) {
		$invite['email'] = '';
	}
	$invite['idCommande'] = null;
	if (empty($invite['idProduit']))
		$invite['idProduit'] = '3';
	if (empty($invite['valider']))
		$invite['valider'] = 'false';
	
	$inviteId = Commandes::addGuest($invite);
	$newinvite = Invites::getInvite($inviteId);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode($newinvite);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}
