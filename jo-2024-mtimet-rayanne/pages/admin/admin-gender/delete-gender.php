<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du genre est fourni dans l'URL
if (!isset($_GET['id_genre'])) {
    $_SESSION['error'] = "ID du genre manquant.";
    header("Location: manage-gender.php");
    exit();
} else {
    $id_genre = filter_input(INPUT_GET, 'id_genre', FILTER_VALIDATE_INT);
    // Vérifiez si l'ID du genre est un entier valide
    if (!$id_genre && $id_genre !== 0) {
        $_SESSION['error'] = "ID du genre invalide.";
        header("Location: manage-gender.php");
        exit();
    } else {
        try {
            // Récupérez l'ID du genre à supprimer depuis la requête GET
            $id_genre = $_GET['id_genre'];
            // Préparez la requête SQL pour supprimer le genre
            $sql = "DELETE FROM GENRE WHERE id_genre = :id_genre";
            // Exécutez la requête SQL avec le paramètre
            $statement = $connexion->prepare($sql);
            $statement->bindParam(':id_genre', $id_genre, PDO::PARAM_INT);
            $statement->execute();
            // Redirigez vers la page précédente après la suppression
            header('Location: manage-gender.php');
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>