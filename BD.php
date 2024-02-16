<?php
session_start();
if (!isset($_SESSION['nomJoueur1'])) {
    $_SESSION['nomJoueur1'] = " ";
}
//Ouvrir la base de donnée (fonction utilisé dans toutes les autres fonctions); 
function OpenBD()
{
    $user = 'root';
    $pass = '';
    try {
        $bd = new PDO('mysql:host=localhost;dbname=pendu', $user, $pass);
        return $bd;
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
        return null;
    }
}
//Selectionner un mots aléatoire dans la base de donnée pour le jeu du pendu; 
function selectRandomWord()
{
    $BD = OpenBD();
    $row = $BD->query("SELECT ortho FROM lexique ORDER BY RAND() LIMIT 1");
    if ($row) {
        $result = $row->fetch(PDO::FETCH_ASSOC);
        return $result['ortho'];
    } else {
        return null;
    }
}

//Création d'un nouveau compte (paramètre : nouvel identifiant, nouveau mdp, et score initialiser à 0); 
function CreationCompte($new_id, $new_mdp, $new_score)
{
    $BD = OpenBD();
    try {
        $BD->beginTransaction();
        $sql = "INSERT INTO joueur (Joueur_id, mdp, score) values (:joueur_id,:mdp, :score)";
        $stmt = $BD->prepare($sql);
        $stmt->bindParam(':joueur_id', $new_id);
        $stmt->bindParam(':mdp', $new_mdp);
        $stmt->bindParam(':score', $new_score);
        $stmt->execute();
        $BD->commit();
    } catch (PDOException $e) {
        $BD->rollBack();
        echo "Erreur lors de l'insertion : " . $e->getMessage();
    }
}
//Vérifie si l'utilisateur donnée en paramètre existe déja
//-Renvoi True s'il existe; 
//-Renvoie False s'il n'existe pas; 
function IDExistant($new_id)
{
    $BD = OpenBD();
    $row = $BD->query("SELECT Joueur_id from joueur where Joueur_id = '$new_id'");
    if ($row !== false && $row->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

//Cette fonction prend en paramètre l'identification et le mot de passe
//  *Elle vérifie dans un premier  temps si l'identifiant existe et renvoi FALSE si il n'existe pas
//  *
function IdentificationCorrecte($id, $mdp)
{
    $BD = OpenBD();
    if (IDExistant($id)) {  //Si le joeur mentionné existe; 
        $row = $BD->query("SELECT mdp from joueur where Joueur_id = '$id'"); //Obtenir le vraie mdp du joeur;
        $result = $row->fetch(PDO::FETCH_ASSOC);
        if ($mdp === $result['mdp']) {  //Si le mdp mentionné est correcte; 
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
//Cette fonction afficher le classement des joueur par rapoort à leur score; 
function Classement()
{
    $BD = OpenBD();
    $result = $BD->query("SELECT * FROM joueur ORDER BY score desc"); //Classer les joueurs par ordre décroissant selon leur score; 
    $place = 1;
    foreach ($result as $row) {
        echo $place . ". " . $row['Joueur_id'] . " ----> " . $row['score'] . " Points ____ Date : " . $row['DateScore'];
        echo "<br>";
        $place++;
    }
}

//Cette fonction effectue un UPDATE du score dans la table joueur si le score est meilleur que le précédent; 
function UpdateScore($id, $new_score)
{
    $BD = OpenBD();
    $row = $BD->query("SELECT score from joueur where Joueur_id = '$id'"); //Obtenir le score actuelle du joueur; 
    $result = $row->fetch(PDO::FETCH_ASSOC);
    if ($new_score > $result['score']) {    //si le nouveau score est meilleur que le précédent; 
        $BD->beginTransaction();
        $sql = "UPDATE joueur SET score = :score WHERE Joueur_id = :Joueur_id"; //UPDATE du score; 
        $stmt = $BD->prepare($sql);
        $stmt->bindParam(':score', $new_score);
        $stmt->bindParam(':Joueur_id', $id);
        $stmt->execute();
        $BD->commit();
        UpdateDate($id);
    }
}
//Cette fonction change la date de l'obtention du score; 
function UpdateDate($id)
{
    $dateActuelle = date("Y-m-d");
    $BD = OpenBD();
    $BD->beginTransaction();
    $sql = "UPDATE joueur SET DateScore = :DateScore WHERE Joueur_id = :Joueur_id"; //UPDATE de la date; 
    $stmt = $BD->prepare($sql);
    $stmt->bindParam(':DateScore', $dateActuelle);
    $stmt->bindParam(':Joueur_id', $id);
    $stmt->execute();
    $BD->commit();
}
//Cette fonction sert à changer l'id du joueur pour obtenir l'id de celui qui est entrain de jouer; 
function setId($new_id)
{
    $_SESSION['nomJoueur1'] = $new_id;
}
//Cette fonction sert obtenir l'id du joueur qui est entrain de jouer; 
function getId()
{
    return $_SESSION['nomJoueur1'];
}
