<?php

function vue_prenom()
{
    global $result;
    global $debug;

    $formulaire = <<<"END"
    <form method="POST" action="">
    <label for="prenom">Entrez votre prénom :</label>
    <input type="text" id="prenom" name="prenom" required>
    <button type="submit">Commencer</button>
    </form>
    END;
    
    $liste_joueur = '<h3>Liste des joueurs :</h3>';
    $liste_joueur .= '<ul>';
    foreach ($result as $row) 
    {
        $liste_joueur.= '<li>' . $row['prenom'] . ' - Moyenne : ' . $row['moyenne_tentatives'] . '</li>';
    }
    $liste_joueur.= '</ul>';

    $codehtml = <<<"FOOBAR"
    <!DOCTYPE html>
    <html>
    <head>
        <title>Mini jeu PHP</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            $formulaire
            $liste_joueur
        </div>
        $debug
    </body>
    </html>
    FOOBAR;

    return $codehtml;
}