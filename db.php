<?php

function open_database()
{
    global $conn;

    try 
    {    
        // Connexion à la base de données avec PDO
        $dsn = "mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB;
        $conn = new PDO($dsn, MYSQL_USER, MYSQL_PWD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) 
    {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

function close_database()
{
    global $conn;

    // Fermeture de la connexion à la base de données
    $conn = null;
}

function parties_score()
{
    global $conn;

    // Récupération de la liste des joueurs classés par moyenne du nombre de tentatives
    $sql = "SELECT prenom, AVG(tentatives) AS moyenne_tentatives FROM parties GROUP BY prenom ORDER BY moyenne_tentatives";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

function parties_stat($prenom)
{
    global $prenom;
    global $nbParties;
    global $totalTentatives;
    global $moyenneTentatives;
    global $minTentatives;
    global $maxTentatives;
    global $conn;

    // Récupération des statistiques du joueur depuis la base de données
    $sql = "SELECT COUNT(*) AS nb_parties, SUM(tentatives) AS total_tentatives, MIN(tentatives) AS min_tentatives, MAX(tentatives) AS max_tentatives FROM parties WHERE prenom = :prenom";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nbParties = $row['nb_parties'];
    $totalTentatives = $row['total_tentatives'];
    $moyenneTentatives = $nbParties > 0 ? $totalTentatives / $nbParties : 0;
    $minTentatives = $row['min_tentatives'];
    $maxTentatives = $row['max_tentatives'];
}

function parties_save($prenom, $nbTentatives)
{
    global $conn;

    $sql = "INSERT INTO parties (prenom, tentatives) VALUES (:prenom, :tentatives)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':tentatives', $nbTentatives);
    $stmt->execute();
}