<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'épreuve est fourni dans l'URL
if (!isset($_GET['id_epreuve'])) {
    $_SESSION['error'] = "ID du lieu manquant.";
    header("Location: manage-events.php");
    exit();
} else {
    $id_epreuve = filter_input(INPUT_GET, 'id_epreuve', FILTER_VALIDATE_INT);
    // Vérifiez si l'ID de l'épreuve est un entier valide
    if (!$id_epreuve && $id_epreuve !== 0) {
        $_SESSION['error'] = "ID de l'épreuve invalide.";
        header("Location: manage-events.php");
        exit();
    } else {
        try {
            // Récupérez l'ID de l'épreuve à supprimer depuis la requête GET
            $id_epreuve = $_GET['id_epreuve'];
            // Préparez la requête SQL pour supprimer l'œuvre
            $sql = "DELETE FROM EPREUVE WHERE id_epreuve = :id_epreuve";
            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_epreuve', $id_epreuve, PDO::PARAM_INT);
            $statement->execute();
            // Redirigez vers la page précédente après la suppression
            header('Location: manage-events.php');
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>