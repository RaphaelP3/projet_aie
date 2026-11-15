<?php
require_once __DIR__ . '/../config/twig.php';
require_once __DIR__ . '/../config/database.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);

    if ($pseudo === '' || $mdp === '') {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        // Si le pseudo existe déjà
        $stmt = $pdo->prepare("SELECT id_player FROM players WHERE nickname = :pseudo");
        $stmt->execute(['pseudo' => $pseudo]);

        if ($stmt->fetch()) {
            $erreur = "Ce pseudo est déjà utilisé.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO players (nickname, password_) VALUES (:pseudo, :mdp)");
            $stmt->execute(['pseudo' => $pseudo, 'mdp' => $hash]);

            header('Location: connexion.php');
            exit;
        }
    }
}

echo $twig->render('inscription.html.twig', ['erreur' => $erreur]);