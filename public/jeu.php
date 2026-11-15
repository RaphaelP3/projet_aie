<?php
session_start();
require_once __DIR__ . '/../config/twig.php';
require_once __DIR__ . '/../config/fonctions.php';

// Il faut être connecté
if (!isset($_SESSION['id_joueur'])) {
    header('Location: connexion.php');
    exit;
}

// Si aucune partie n'est chargée, on la charge (ou on en crée une)
if (!isset($_SESSION['jour'])) {
    demarrerPartie($pdo);
}

// Un bouton action a été cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'restart' && partieTerminee()) {
        // La partie est finie, demarrerPartie en crée une nouvelle
        demarrerPartie($pdo);
        $_SESSION['message'] = "Nouvelle partie !";
    } elseif (!partieTerminee()) {
        if ($action === 'rest') {
            $_SESSION['message'] = seReposer();
        } elseif ($action === 'food') {
            $_SESSION['message'] = chercherNourriture();
        } elseif ($action === 'material') {
            $_SESSION['message'] = chercherMateriaux();
        } elseif ($action === 'repair') {
            $_SESSION['message'] = reparerAbri();
        }
        sauvegarder($pdo);
    }

    // On recharge la page pour éviter de refaire l'action en actualisant
    header('Location: jeu.php');
    exit;
}

// Message de la dernière action 
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

echo $twig->render('jeu.html.twig', [
    'pseudo' => $_SESSION['pseudo'],
    'jour' => $_SESSION['jour'],
    'sante' => $_SESSION['sante'],
    'energie' => $_SESSION['energie'],
    'abri' => $_SESSION['abri'],
    'nourriture' => $_SESSION['nourriture'],
    'materiaux' => $_SESSION['materiaux'],
    'message' => $message,
    'terminee' => partieTerminee(),
    'victoire' => victoire()
]);