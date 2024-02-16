<!Doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title> TP1 </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
require('BD.php');
$_SESSION['ERREUR_ID'] = null;
$_SESSION['ERREUR_MDP'] = null;
$_SESSION['Connexion'] = null;
$_SESSION['IdentificationCorrecte'] = null;
$_SESSION['ERREUR_ID'] = null;

if (!isset($_SESSION['MESSAGE_ID'])) {
    $_SESSION['MESSAGE_ID'] = " ";
}
if (!isset($_SESSION['MESSAGE_MDP'])) {
    $_SESSION['MESSAGE_MDP'] = " ";
}
if (!isset($_SESSION['MESSAGE_MDP2'])) {
    $_SESSION['MESSAGE_MDP2'] = " ";
}
if (!isset($_SESSION['AucuneErreur'])) {
    $_SESSION['AucuneErreur'] = false;
}
if (!isset($_SESSION['CompteCree'])) {
    $_SESSION['CompteCree'] = " ";
}

if (isset($_POST['valider'])) {
    if (!IDExistant($_POST['NEW_ID']) && $_POST['NEW_MDP2'] === $_POST['NEW_MDP'] && !empty($_POST['NEW_MDP2'])) {
        CreationCompte($_POST['NEW_ID'], $_POST['NEW_MDP'], 0);
        $_SESSION['CompteCree'] = "Le compte de " . $_POST['NEW_ID'] . " a été créé avec succès";
        $_SESSION['AucuneErreur'] = true;
        $_SESSION['MESSAGE_ID'] = " ";
        $_SESSION['MESSAGE_MDP'] = " ";
        $_SESSION['MESSAGE_MDP2'] = " ";
    } else {
        if (empty($_POST['NEW_ID'])) {
            $_SESSION['MESSAGE_ID'] = "Champ vide";
        } elseif (IDExistant($_POST['NEW_ID'])) {
            $_SESSION['MESSAGE_ID'] = "L'identifiant existe déjà";
        } else {
            $_SESSION['MESSAGE_ID'] = " ";
        }
        if (empty($_POST['NEW_MDP'])) {
            $_SESSION['MESSAGE_MDP'] = "Champ vide";
        } elseif ($_POST['NEW_MDP'] !== $_POST['NEW_MDP2']) {
            $_SESSION['MESSAGE_MDP'] = "Erreur lors de la saisi du mot de passe";
        } else {
            $_SESSION['MESSAGE_MDP'] = " ";
        }
        if (empty($_POST['NEW_MDP2'])) {
            $_SESSION['MESSAGE_MDP2'] = "Champ vide";
        } elseif ($_POST['NEW_MDP2'] !== $_POST['NEW_MDP']) {
            $_SESSION['MESSAGE_MDP2'] = "Erreur lors de la saisi du mot de passe";
        } else {
            $_SESSION['MESSAGE_MDP2'] = " ";
        }
        $_SESSION['AucuneErreur'] = false;
    }
}

if (isset($_POST['retour'])) {
    $_SESSION['MESSAGE_ID'] = null;
    $_SESSION['MESSAGE_MDP'] = null;
    $_SESSION['MESSAGE_MDP2'] = null;
    $_SESSION['AucuneErreur'] = null;
    $_SESSION['CompteCree'] = null;
    header("Location: index.php");
    exit();
}
?>

<body>
    <form method="POST">
        <button class='boutonRetour' type='submit' name='retour'>Retour</button>
    </form>
    <div class='police'>
        <h2> Création du Compte</h2>

        <form method="POST">
            <a> Nouvel identifiant</a>
            <br>
            <input class='input' type='text' id='NEW_ID' name='NEW_ID' required></input>
            <?php
            echo "<div class='erreur'>" . $_SESSION['MESSAGE_ID'] . "</div>";
            ?>
            <br>
            <a> Nouveau mot de passe</a>
            <br>
            <input class='input' type='password' id='NEW_MDP' name='NEW_MDP' required></input>
            <?php
            echo "<div class='erreur'>" . $_SESSION['MESSAGE_MDP'] . "</div>";
            ?>
            <br>
            <a> Confirmation du nouveau mot de passe</a>
            <br>
            <input class='input' type='password' id='NEW_MDP2' name='NEW_MDP2' required></input>
            <?php
            echo "<div class='erreur'>" . $_SESSION['MESSAGE_MDP2'] . "</div>";
            ?>
            <br>
            <button class='bouton' type='submit' name='valider'>Créer un compte</button>
        </form>
        <br>
        <?php
        if ($_SESSION['AucuneErreur']) {
            echo "<p style='color:green'>" . $_SESSION['CompteCree'] . "</p>";
        }
        ?>
    </div>
</body>

</html>