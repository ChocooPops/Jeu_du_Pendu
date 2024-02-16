<!Doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title> TP1 </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
require('BD.php');
if (!isset($_SESSION['ERREUR_ID'])) {
    $_SESSION['ERREUR_ID'] = " ";
}
if (!isset($_SESSION['ERREUR_MDP'])) {
    $_SESSION['ERREUR_MDP'] = " ";
}
if (!isset($_SESSION['Connexion'])) {
    $_SESSION['Connexion'] = false;
}
if (!isset($_SESSION['IdentificationCorrecte'])) {
    $_SESSION['IdentificationCorrecte'] = " ";
}
if (!isset($_SESSION['nomJoueur'])) {
    $_SESSION['nomJoueur'] = " ";
}

if (isset($_POST['valider'])) {
    if (empty($_POST['ID'])) {
        $_SESSION['ERREUR_ID'] = "Champ vide";
        $_SESSION['nomJoueur'] = " ";
    } elseif (!IDExistant($_POST['ID'])) {
        $_SESSION['ERREUR_ID'] = "Identifiant introuvable";
        $_SESSION['nomJoueur'] = " ";
        $_SESSION['Connexion'] = false;
    } else {
        $_SESSION['ERREUR_ID'] = null;
    }
    if (empty($_POST['MDP'])) {
        $_SESSION['ERREUR_MDP'] = "Champ vide";
        $_SESSION['Connexion'] = false;
    } elseif (!IDExistant($_POST['ID'])) {
        $_SESSION['ERREUR_MDP'] = "Identifiant ou mot de passe invalide";
        $_SESSION['Connexion'] = false;
    } else {
        $_SESSION['ERREUR_MDP'] = null;
    }
    if ((IDExistant($_POST['ID']))) {
        if (IdentificationCorrecte($_POST['ID'], $_POST['MDP'])) {
            $_SESSION['Connexion'] = true;
            $_SESSION['nomJoueur'] = $_POST['ID'];
            setId($_SESSION['nomJoueur']);
            $_SESSION['IdentificationCorrecte'] = "Identification correcte";
        } else {
            $_SESSION['ERREUR_ID'] = "Identifiant ou mot de passe invalide";
            $_SESSION['ERREUR_MDP'] = "Identifiant ou mot de passe invalide";
            $_SESSION['Connexion'] = false;
        }
    } else {
        $_SESSION['Connexion'] = false;
    }
}
?>

<body>
    <div class='police'>
        <h2> Bienvenue dans le jeu du pendu </h2>
        <h2> Connexion </h2>
        <a> Identifiant </a>
        <form method="POST">
            <input class='input' type='text' id='ID' name='ID' required></label>
            <?php
            echo '<div class="erreur">' . $_SESSION['ERREUR_ID'] . '</div>';
            ?>
            <br>
            <a> Mot de Passe </a>
            <br>
            <input class='input' type='password' id='MDP' name='MDP' required></label>
            <?php
            echo '<div class="erreur">' . $_SESSION['ERREUR_MDP'] . '</div>';
            ?>
            <br>
            <button class='bouton' type='submit' name='valider'>Connexion</button>
        </form>
        <p class='lien'><a href="CreationCompte.php">Créer un compte</a></p>
        <br>
        <?php
        if ($_SESSION['Connexion']) {
            echo "<a style='color:green'>" . $_SESSION['IdentificationCorrecte'] . "</a>";
            echo "<p><form method='POST'>
        <button class='bouton' type='submit' name='boutonJouer'>Commencer à jouer Joueur : " . getID() . "</button>
        </from></p>";
        }
        if (isset($_POST['boutonJouer'])) {
            $_SESSION['ERREUR_ID'] = null;
            $_SESSION['ERREUR_MDP'] = null;
            $_SESSION['Connexion'] = null;
            $_SESSION['IdentificationCorrecte'] = null;
            $_SESSION['ERREUR_ID'] = null;
            header("Location: Pendu.php");
            exit();
        }
        ?>
    </div>
    <div class='classement'>
        <h1 style='font-family: Arial, sans-serif'> Classement </h1>
        <div class='score'>
            <?php
            Classement();
            ?>
        </div>
    </div>


</body>

</html>