<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du pays est fourni dans l'URL
if (!isset($_GET['id_utilisateur'])) {
    $_SESSION['error'] = "ID du pays manquant.";
    header("Location: manage-users.php");
    exit();
} else {
    $id_utilisateur = filter_input(INPUT_GET, 'id_utilisateur', FILTER_VALIDATE_INT);
    // Vérifiez si l'ID du pays est un entier valide
    if (!$id_utilisateur && $id_utilisateur !== 0) {
        $_SESSION['error'] = "ID du pays invalide.";
        header("Location: manage-users.php");
        exit();
    } else {
        try {
            // Récupérez l'ID du pays à supprimer depuis la requête GET
            $id_utilisateur = $_GET['id_utilisateur'];
            // Préparez la requête SQL pour supprimer le pays
            $sql = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id_utilisateur";
            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $statement->execute();
            // Redirigez vers la page précédente après la suppression
            header('Location: manage-users.php');
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>