<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';

try {
	$listids = $_GET['inviteId'];
	$cancel = isset($_GET['cancel']);
	$ids = explode(',', $listids);
	foreach ($ids as $inviteId) {
		if ($cancel)
			Invites::cancelInvite($inviteId);
		else
			Invites::validInvite($inviteId);
	}
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode(true);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}
