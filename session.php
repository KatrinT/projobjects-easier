<?php

// Autoload PSR-4
spl_autoload_register();

// Imports 
use \Classes\Webforce3\Config\Config;
use \Classes\Webforce3\DB\Training;
use \Classes\Webforce3\DB\Session;
use \Classes\Webforce3\DB\Location;
use \Classes\Webforce3\Helpers\SelectHelper;

// Get the config object
$conf = Config::getInstance();

$sessionId = isset($_GET['ses_id']) ? intval($_GET['ses_id']) : 0;
$sessionObject = new Session();

// Récupère la liste complète des training en DB
$trainingList = Training::getAllForSelect();
// Récupère la liste complète des location en DB
$locationList = Location::getAllForSelect();


// Si modification d'un session, on charge les données pour le formulaire
if ($sessionId > 0) {
	$sessionObject = Session::get($sessionId);
}

// Si lien suppression
if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
	if (Session::deleteById(intval($_GET['delete']))) {
		header('Location: session.php?success='.urlencode('Suppression effectuée'));
		exit;
	}
}

// Formulaire soumis
if(!empty($_POST)) {

	$sessionId = isset($_POST['ses_id']) ? intval($_POST['ses_id']) : 0;
	$locationId = isset($_POST['location_loc_id']) ? intval($_POST['location_loc_id']) : 0;
	$sessionStartDate = isset($_POST['ses_start_date']) ? intval(($_POST['ses_start_date'])) : 0;
	$sessionEndDate = isset($_POST['ses_end_date']) ? intval(($_POST['ses_end_date'])) : 0;
	$trainingId = isset($_POST['training_tra_id']) ? trim($_POST['training_tra_id']) : '';
	

	if (strlen($sessionId, $locationId) < 2) {
		$conf->addError('Donnée non correcte');
	}
	if (empty($sessionEndDate)){
		$conf->addError('Veuillez renseigner la date de fin');
	}
        if (empty($sessionStartDate)){
		$conf->addError('Veuillez renseigner la date de debut');
	}
	if (empty($trainingId)) {
		$conf->addError('Veuillez renseigner l\'id de la formation');
	}
        if (empty($sessionNumber)) {
		$conf->addError('Veuillez renseigner l\'id de la formation');
	}
   

	// je remplis l\'objet qui est lu pour les inputs du formulaire, ou pour l\'ajout en DB
        
	$sessionObject = new Session(
		$sessionId,
		new Location($locationId),
		new Training($trainingId),
                $sessionStartDate,
                $sessionEndDate,
                $sessionNumber
	);

	// Si tout est ok
	if (!$conf->haveError()) {
		if ($sessionObject->saveDB()) {
			header('Location: session.php?success='.urlencode('Ajout/Modification effectuée').'&stu_id='.$studentObject->getId());
			exit;
		}
		else {
			$conf->addError('Erreur dans l\'ajout ou la modification');
		}
	}
}

$selectSessions = new SelectHelper($trainingList, $sessionId, array(
	'name' => 'ses_id',
	'id' => 'ses_id',
	'class' => 'form-control',
));




// Views - toutes les variables seront automatiquement disponibles dans les vues
require $conf->getViewsDir().'header.php';
require $conf->getViewsDir().'session.php';
require $conf->getViewsDir().'footer.php';