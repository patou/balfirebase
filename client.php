<?php

define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT'].'/');

include('tcpdf/tcpdf.php');
include('fpdi/fpdi.php');

include RACINE_SERVEUR.'modeles/Modele.class.php';
include RACINE_SERVEUR.'modeles/Invites.class.php';
include RACINE_SERVEUR.'modeles/Clients.class.php';
include RACINE_SERVEUR.'modeles/Commandes.class.php';

try {
	$idClient = $_GET['idClient'];
	$customer = Clients::getOneCustomer($idClient);
	$commandes = Commandes::getCommandeClient($idClient);
	$ids = [];
	$totalCommande = 0;
	foreach ($commandes as $commande) {
		$ids[] = $commande['id'];
		$totalCommande += $commande['total'];
	}
	$produits = Commandes::getLigneCommandeByCommandesId($ids);

	$invites = Invites::getInvitesByCommandesId($ids);
			
	unset($customer['motPasse']);
	$return = array('info' => $customer, 'commandes' => $commandes, 'produits' => $produits, 'invites' => $invites);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo json_encode($return);
}
catch(PDOException $exception)
{
	http_response_code(500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header('content-type: application/json; charset=utf-8');
	echo '{error: ', json_encode($exception->getMessage()), '}';
}
