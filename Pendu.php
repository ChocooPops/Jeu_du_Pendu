<!Doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title> TP1 </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
require('BD.php');
?>

<body>
    <form method="POST">
        <button class='boutonRetour' type="submit" name="Retour">Retour</button>
    </form>
    <div class='police'>
        <h1> Jeu du pendu </h1><?php
                                echo "<h3> Bienvenue " . getId() . "</h3>" ?>

        <form method="POST">
            <button class='bouton2' type="submit" name="start">Nouveau mots</button>
        </form>
        </h1>
        <?php
        //Réinitialise toutes les variables SESSION lorsque la page est quitté; 
        if (isset($_POST['Retour'])) {
            $_SESSION['tirets'] = null;
            $_SESSION['mots_debug'] = null;
            $_SESSION['mots'] = null;
            $_SESSION['Connexion'] = null;
            $_SESSION['nb_lettre_mots'] = null;
            $_SESSION['lettre_double'] = null;
            $_SESSION['nb_lettre_juste'] = null;
            $_SESSION['nb_essai'] = null;
            header("Location: index.php");
            exit();
        }

        if (isset($_POST['start'])) {
            $_SESSION['nb_essai'] = 10;                     //Nombre d'essaie avant de perdre
            $mots = selectRandomWord();
            $lettre = str_split($mots);
            $_SESSION['mots'] = $lettre;
            $tiret = array();
            for ($i = 0; $i < sizeof($lettre); $i++) {
                $tiret[$i] = "_ ";
            }
            $_SESSION['tirets'] = $tiret;
            $_SESSION['mots_debug'] = $mots;                //Contient la solution, le vrai mots à deviné (Outil de Debug)
            $_SESSION['nb_lettre_mots'] = sizeof($lettre);  //Nombre de lettre qui compose le mot
            $_SESSION['nb_lettre_juste'] = 0;               //Nombre de lettre devinée
            $_SESSION['lettre_double'] = " ";               //Hisotirique de toutes les lettres déjà utilisées
            $_SESSION['fin'] = false;                       //Si true, la variable arrete toutes les actions des boutons jusqu'à qu'un nouveau mot soit chosis; 
        }
        ?>
        <form method="POST">
            <p><label from="lettre"> Saisissez une lettre : <input class='label' type="texte" id="lettre" name="lettre" maxlength="1"></label>
                <br>
                <button class='bouton2' type="submit" name="valider">Valider</button>
            </p>
        </form>
        <?php
        if (isset($_POST['valider']) && !empty($_POST['lettre']) && isset($_SESSION['tirets']) && isset($_SESSION['mots']) && isset($_SESSION['nb_lettre_juste']) && isset($_SESSION['lettre_double']) && isset($_SESSION['fin']) && !$_SESSION['fin']) {
            $tiret = $_SESSION['tirets'];
            $lettre = $_SESSION['mots'];
            $op1 = true;        //Si false -> la lettre renseigné par le joueur a deja été utilisée; 
            $op2 = false;       //Si false -> la lettre renseigné par le joueur n'apparait pas dans le mots;
            $curseur = str_split($_SESSION['lettre_double']);
            if (!empty($curseur)) { //Boucle qui va chercher si la lettre a deja été utilisée; 
                for ($i = 0; $i < sizeof($curseur); $i++) {
                    if ($curseur[$i] === $_POST['lettre']) {
                        $op1 = false;
                    }
                }
            }
            if ($op1) {
                for ($i = 0; $i < sizeof($lettre); $i++) { //Boucle sur le nombre de lettre dans le mot, indice pa indice; 
                    if (strtoupper($lettre[$i]) === strtoupper($_POST['lettre'])) { //Si le mots contient une ou des lettres validé par l'utilisateur alors...
                        $tiret[$i] =  $lettre[$i]; //Change le caractère "_" par la lettre; 
                        $op2 = true;                //Change de valeur TRUE pour dire que le joueur a trouvé une bonne lettre; 
                        $_SESSION['nb_lettre_juste']++; //Ajout +1 de lettre dévinée; 
                    }
                    $_SESSION['tirets'] = $tiret;
                }
                if (!$op2) {    //Si le joueur a rentré une mauvaise lettre alors...
                    if ($_SESSION['nb_essai'] > 0) {
                        $_SESSION['nb_essai']--;        //Diminue le nombre d'essaie restant; 
                    }
                }
                $_SESSION['lettre_double'] = $_SESSION['lettre_double'] . " " . $_POST['lettre'];       //Ajoute la lettre utilisé dans la variable lettre double; 
            } else {
                echo "<p style='text-align: center; color: red'>Lettre déja utilisé</p>";
            }
        } elseif (isset($_POST['valider']) && empty($_POST['lettre']) && isset($_SESSION['fin']) && !$_SESSION['fin']) {
            echo "<p style='text-align: center; color: red'>Champ vide</p>";
        }
        ?>
        <p><?php
            //Affiche à nouveau le mots selon les lettres devinée et les tirest restants; 
            if (isset($_SESSION['mots_debug']) && isset($_SESSION['tirets'])) {
                $mots = $_SESSION['mots_debug'];
                $tiret = $_SESSION['tirets'];
                for ($i = 0; $i < sizeof($tiret); $i++) {
                    echo $tiret[$i];
                }
                echo "<br>";
                echo $mots;
            }
            ?></p>

        <p><?php
            //Affichage de la partie inférieur du jeu (nombre d'essaie restant, lettres utilisées); 
            if (isset($_SESSION['nb_essai']) && isset($_SESSION['nb_lettre_juste']) && isset($_SESSION['nb_lettre_mots']) && isset($_SESSION['lettre_double'])) {
                $texte_essai = "Votre nombre d'essais restant est : " . $_SESSION['nb_essai'];
                echo $texte_essai;
                echo "<br>";
                echo "Lettre déjà utilisée :" . $_SESSION['lettre_double'];
                $gameFin = " ";
                if ($_SESSION['nb_essai'] <= 0) { //Si le joueur n'a plus d'essaie possible; 
                    $_SESSION['fin'] = true;    //Fin du jeu (désactive les actions du bouton valider); 
                    echo "<br>";
                    $gameFin = "<h2 class = 'centrer'> Vous avez échoué " . getId() . ", le mots était " . $_SESSION['mots_debug'] . " <br> Appuyez sur Nouveau mots pour rejouer </h2>";
                } elseif ($_SESSION['nb_lettre_juste'] === $_SESSION['nb_lettre_mots']) { //Si le joueur a trouvé le bon mots; 
                    $_SESSION['fin'] = true;    //Fin du jeu (désactive les actions du bouton valider); 
                    //UPDATE DU SCORE = On prend le nombre de lettre qui compose le mots pui on le soustrait par le nombre d'erreur(obtenue en faisant la différence entre le nb d'essaie initiale et le nb d'essai restant);
                    $score = $_SESSION['nb_lettre_mots'] - (10 - $_SESSION['nb_essai']);
                    if (IDExistant(getId())) {
                        UpdateScore(getId(), $score);
                    }
                    echo "<br>";
                    $gameFin = "<h2 class = 'centrer'> Vous avez gagné avec un score de " . $score . " point " . getId() . "<br> Appuyez sur Nouveau mots pour rejouer </h2>";
                } elseif ($_SESSION['nb_essai'] > 0) {
                    $gameFin = " ";
                }
                echo $gameFin;
            }
            ?> </p>
    </div>
</body>

</html>