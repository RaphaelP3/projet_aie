<?php
require_once __DIR__ . '/database.php';

// Fin de journée : le jour avance, l'abri se détériore
function finDeJournee() {
    $_SESSION['jour'] = $_SESSION['jour'] + 1;
    $_SESSION['abri'] = $_SESSION['abri'] - 5;
    $message = " Fin de la journée, l'abri se détériore.";

    if ($_SESSION['abri'] <= 0) {
        $_SESSION['abri'] = 0;
        $_SESSION['sante'] = $_SESSION['sante'] - 10;
        $message = $message . " Ton abri est détruit, tu perds de la santé à cause des radiations (-10 santé).";
    }

    return $message;
}

function seReposer() {
    $_SESSION['energie'] = $_SESSION['energie'] + 10;
    if ($_SESSION['energie'] > 100) {
        $_SESSION['energie'] = 100;
    }

    if ($_SESSION['nourriture'] >= 5) {
        $_SESSION['nourriture'] = $_SESSION['nourriture'] - 5;
        $message = "Bonne nuit ! (+10 énergie, -5 nourriture)";
    } else {
        $_SESSION['sante'] = $_SESSION['sante'] - 10;
        $message = "Tu passes une mauvaise nuit à cause de la faim. (+10 énergie, -10 santé)";
    }

    return $message . finDeJournee();
}

function chercherNourriture() {
    if ($_SESSION['energie'] < 10) {
        return "Pas assez d'énergie (10 nécessaires).";
    }

    $_SESSION['energie'] = $_SESSION['energie'] - 10;
    $_SESSION['nourriture'] = $_SESSION['nourriture'] + 10;

    return "Tu as trouvé de la nourriture. (+10 nourriture, -10 énergie)" . finDeJournee();
}

function chercherMateriaux() {
    if ($_SESSION['energie'] < 10) {
        return "Pas assez d'énergie. (10 nécessaires).";
    }

    $_SESSION['energie'] = $_SESSION['energie'] - 10;
    $_SESSION['materiaux'] = $_SESSION['materiaux'] + 10;

    return "Tu trouves des matériaux (+10 matériaux, -10 énergie)." . finDeJournee();
}

function reparerAbri() {
    if ($_SESSION['materiaux'] < 10) {
        return "Il te manque des matériaux (10 nécessaires).";
    }
    if ($_SESSION['energie'] < 10) {
        return "Pas assez d'énergie (10 nécessaires).";
    }

    $_SESSION['materiaux'] = $_SESSION['materiaux'] - 10;
    $_SESSION['energie'] = $_SESSION['energie'] - 10;
    $_SESSION['abri'] = $_SESSION['abri'] + 25;
    if ($_SESSION['abri'] > 100) {
        $_SESSION['abri'] = 100;
    }

    return "L'abri est réparé. " . finDeJournee();
}

// La partie est finie si on est mort ou si les 30 jours sont passés
function partieTerminee() {
    return $_SESSION['sante'] <= 0 || $_SESSION['jour'] > 30;
}

function victoire() {
    return $_SESSION['sante'] > 0 && $_SESSION['jour'] > 30;
}

// Enregistre la partie 
function sauvegarder(PDO $pdo): void {
    $sante = $_SESSION['sante'];
    if ($sante < 0) {
        $sante = 0;
    }

    $sql = "UPDATE games
            SET health = :sante, energy = :energie, shelter_ = :abri,
                foods = :nourriture, materials_ = :materiaux, day_ = :jour
            WHERE id_player = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'sante' => $sante,
        'energie' => $_SESSION['energie'],
        'abri' => $_SESSION['abri'],
        'nourriture' => $_SESSION['nourriture'],
        'materiaux' => $_SESSION['materiaux'],
        'jour' => $_SESSION['jour'],
        'id' => $_SESSION['id_joueur']
    ]);
}

// Reprend la partie en cours du joueur, ou en commence une nouvelle
function demarrerPartie(PDO $pdo): void {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id_player = :id");
    $stmt->execute(['id' => $_SESSION['id_joueur']]);
    $partie = $stmt->fetch();

    // Reprendre une partie qui existe
    if ($partie && $partie['health'] > 0 && $partie['day_'] <= 30) {
        $_SESSION['sante'] = $partie['health'];
        $_SESSION['energie'] = $partie['energy'];
        $_SESSION['abri'] = $partie['shelter_'];
        $_SESSION['nourriture'] = (int) $partie['foods'];
        $_SESSION['materiaux'] = (int) $partie['materials_'];
        $_SESSION['jour'] = $partie['day_'];
        return;
    }

    // Nouvelle partie si il n'y en a pas
    $_SESSION['sante'] = 100;
    $_SESSION['energie'] = 100;
    $_SESSION['abri'] = 100;
    $_SESSION['nourriture'] = 0;
    $_SESSION['materiaux'] = 0;
    $_SESSION['jour'] = 1;

    if ($partie) {
        sauvegarder($pdo);
    } else {
        $sql = "INSERT INTO games (health, energy, shelter_, foods, materials_, day_, id_player)
                VALUES (100, 100, 100, 0, 0, 1, :id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $_SESSION['id_joueur']]);
    }
}