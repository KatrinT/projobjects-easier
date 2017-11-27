<?php
//a modifier - copie de student
// Autoload PSR-4
spl_autoload_register();

// Imports 
use \Classes\Webforce3\Config\Config;
use \Classes\Webforce3\DB\location;
use \Classes\Webforce3\Helpers\SelectHelper;

// Get the config object
$conf = Config::getInstance();

$locationId = isset($_GET['loc_id']) ? intval($_GET['loc_id']) : 0;
$locationObject = new Location();


// Récupère la liste complète des location en DB
$locationList = Location::getAllForSelect();


// Si modification d'un location, on charge les données pour le formulaire
if ($locationId > 0) {
	$locationObject = Location::get($locationId);
}

// Si lien suppression
if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
	if (Student::deleteById(intval($_GET['delete']))) {
		header('Location: location.php?success='.urlencode('Suppression effectuée'));
		exit;
	}
}

// Si formulaire soumis
if (!empty($_POST)) {

	$locationId = isset($_POST['loc_id']) ? intval($_POST['loc_id']) : 0;
	$locationName = isset($_POST['loc_name']) ? trim($_POST['loc_name']) : '';
	

	if (strlen($studentBirthdate) < 10) {
		$conf->addError('Birthdate non correcte');
	}
	if (!array_key_exists($studentFriendliness, $friendlinessList)) {
		$conf->addError('Sympathie non valide');
	}
	if (!array_key_exists($cityId, $citiesList)) {
		$conf->addError('Ville non valide');
	}
	if (!array_key_exists($sessionId, $sessionsList)) {
		$conf->addError('Session de formation non valide');
	}
	if (empty($studentEmail) || filter_var($studentEmail, FILTER_VALIDATE_EMAIL) === false) {
		$conf->addError('Email non valide');
	}
	if (empty($studentLastName)) {
		$conf->addError('Veuillez renseigner le nom');
	}
	if (empty($studentFirstName)) {
		$conf->addError('Veuillez renseigner le prénom');
	}

	// je remplis l'objet qui est lu pour les inputs du formulaire, ou pour l'ajout en DB
	$studentObject = new Student(
		$studentId,
		new Session($sessionId),
		new City($cityId),
		$studentLastName,
		$studentFirstName,
		$studentEmail,
		$studentBirthdate,
		$studentFriendliness
	);

	// Si tout est ok
	if (!$conf->haveError()) {
		if ($studentObject->saveDB()) {
			header('Location: student.php?success='.urlencode('Ajout/Modification effectuée').'&stu_id='.$studentObject->getId());
			exit;
		}
		else {
			$conf->addError('Erreur dans l\'ajout ou la modification');
		}
	}
}

// Instancie le générateur de menu déroulant pour la sympathie
$selectFriendliness = new SelectHelper($friendlinessList, $studentObject->getFriendliness(), array(
	'name' => 'stu_friendliness',
	'id' => 'stu_friendliness',
	'class' => 'form-control',
));

// Instancie le générateur de menu déroulant pour la liste des étudiants
$selectStudents = new SelectHelper($studentList, $studentId, array(
	'name' => 'stu_id',
	'id' => 'stu_id',
	'class' => 'form-control',
));

// Instancie le générateur de menu déroulant pour les trainings
$selectSessions = new SelectHelper($sessionsList, $studentObject->getSession()->getId(), array(
	'name' => 'ses_id',
	'id' => 'ses_id',
	'class' => 'form-control',
));

// Instancie le générateur de menu déroulant pour les cities
$selectCities = new SelectHelper($citiesList, $studentObject->getCity()->getId(), array(
	'name' => 'cit_id',
	'id' => 'cit_id',
	'class' => 'form-control',
));

// Views - toutes les variables seront automatiquement disponibles dans les vues
require $conf->getViewsDir().'header.php';
require $conf->getViewsDir().'student.php';
require $conf->getViewsDir().'footer.php';