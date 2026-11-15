<?php
session_start();
require_once __DIR__ . '/../config/twig.php';
require_once __DIR__ . '/../config/database.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);

    $stmt = $pdo->prepare("SELECT id_player, nickname, password_ FROM players WHERE nickname = :pseudo");
    $stmt->execute(['pseudo' => $pseudo]);
    $joueur = $stmt->fetch();

    if ($joueur && password_verify($mdp, $joueur['password_'])) {
        session_regenerate_id(true);
        $_SESSION['id_joueur'] = $joueur['id_player'];
        $_SESSION['pseudo'] = $joueur['nickname'];

        header('Location: jeu.php');
        exit;
    } else {
        $erreur = "Pseudo ou mot de passe incorrect.";
    }
}

echo $twig->render('connexion.html.twig', ['erreur' => $erreur]);