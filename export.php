<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('content-type: application/json; charset=utf-8');
$nom = isset($_REQUEST['nom']) ? $_REQUEST['nom'] : '';
$prenom = isset($_REQUEST['prenom']) ? $_REQUEST['prenom'] : '';
$reset = isset($_REQUEST['reset']);
try {
	Invites::updateInvites();
	if ($reset) 
		Invites::resetInvites();
	$invite = Invites::searchInvite($nom, $prenom);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	$invites = array();
	foreach($invite as $i) {
		$invites[$i['id']] = $i;
	}
	echo json_encode($invites);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}