<?php
session_start();
require_once __DIR__ . '/../config/twig.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['id_joueur'])) {
    header('Location: connexion.php');
    exit;
}

// Une seule requête : chaque joueur avec sa partie
$sql = "SELECT players.nickname, games.health, games.energy, games.shelter_,
               games.foods, games.materials_, games.day_
        FROM players
        INNER JOIN games ON games.id_player = players.id_player";
$stmt = $pdo->query($sql);
$parties = $stmt->fetchAll();

echo $twig->render('stats.html.twig', ['parties' => $parties]);