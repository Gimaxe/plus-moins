<?php
require_once("config.php");
require_once("vue_prenom.php");
require_once("vue_jeu.php");
require_once("db.php");

$prenom = 0;
$nbParties = 0;
$totalTentatives = 0;
$moyenneTentatives = 0;
$minTentatives = 0;
$maxTentatives = 0;

$conn = null;

session_start();
open_database();


    $debug = '<div class="container">';
    $debug .= "GET : ". print_r($_GET, true). "<br/>";
    $debug .= "POST : ". print_r($_POST, true). "<br/>";
    $debug .= "SESSION : ". print_r($_SESSION, true). "<br/>";
    $debug .= "REQUEST_METHOD : " . $_SERVER['REQUEST_METHOD'] . "<br/>";
    $debug .= "COOKIE : ". print_r($_COOKIE, true). "<br/>";
    $debug .= '</div>';

    // Vérification si la session doit être réinitialisée
    if (isset($_GET['reset'])) {
        session_unset();
        header("Location: index.php");
        exit();
    }

    // Vérification si le prénom existe dans la session
    if (isset($_SESSION['prenom'])) {
        $prenom = $_SESSION['prenom'];
    } else {
        // Affichage du formulaire pour demander le prénom
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prenom'])) {
            $prenom = $_POST['prenom'];
            $_SESSION['prenom'] = $prenom;
        } else {
            $result = parties_score();

            echo vue_prenom();

            exit();
        }
    }

    parties_stat($prenom);

    // Vérification si le joueur a soumis une tentative
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tentative'])) {
        $tentative = $_POST['tentative'];
        $nombreTire = $_SESSION['nombreTire'];

        // Vérification de la tentative du joueur
        if ($tentative > $nombreTire) {
            $message = 'Moins';
            $_SESSION['nbTentatives']++;
        } elseif ($tentative < $nombreTire) {
            $message = 'Plus';
            $_SESSION['nbTentatives']++;
        } else {
            $message = 'Félicitations, vous avez trouvé le nombre !';

            // Enregistrement du résultat de la partie dans la base de données
            $nbTentatives = $_SESSION['nbTentatives'];
            
            parties_save($prenom, $nbTentatives);

            // Réinitialisation de la session pour une nouvelle partie
            unset($_SESSION['nombreTire']);
            unset($_SESSION['nbTentatives']);
        }
    } elseif (!isset($_SESSION['nombreTire'])) {
        // Commencer une nouvelle partie
        $_SESSION['nombreTire'] = rand(1, 9);
        $_SESSION['nbTentatives'] = 0;
        $message = 'Une nouvelle partie commence !';
    }

echo vue_jeu();

close_database();