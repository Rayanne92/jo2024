<?php
session_start(); // Démarre la session PHP pour stocker des variables de session.

require_once("database.php"); // Inclut le fichier de connexion à la base de données.

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Vérifie si la requête est une méthode POST (formulaire soumis).
    $login = $_POST["login"]; // Récupère la valeur du champ "login" du formulaire.
    $password = $_POST["password"]; // Récupère la valeur du champ "password" du formulaire.

    // Prépare la requête SQL pour vérifier si le login existe déjà.
    $checkQuery = "SELECT COUNT(*) FROM UTILISATEUR WHERE login = :login";
    $checkStmt = $connexion->prepare($checkQuery);
    $checkStmt->bindParam(":login", $login, PDO::PARAM_STR);
    $checkStmt->execute();

    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['error'] = "Le login existe déjà.";
        header("location: ../pages/admin/admin-users/manage-users.php"); // Redirige vers la page de login avec un message d'erreur.
        exit();
    }

    // Le login n'existe pas, on peut procéder à l'insertion.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prépare la requête SQL d'insertion.
    $insertQuery = "INSERT INTO UTILISATEUR (nom_utilisateur, prenom_utilisateur, login, password) VALUES (:nom_utilisateur, :prenom_utilisateur, :login, :password)";
    $insertStmt = $connexion->prepare($insertQuery);
    
    // Définir les valeurs des paramètres de la requête.
    $nom_utilisateur = $_POST["nom_utilisateur"]; // Ajoutez cette ligne si vous avez un champ "nom_utilisateur" dans le formulaire.
    $prenom_utilisateur = $_POST["prenom_utilisateur"]; // Ajoutez cette ligne si vous avez un champ "prenom_utilisateur" dans le formulaire.
    
    $insertStmt->bindParam(":nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);
    $insertStmt->bindParam(":prenom_utilisateur", $prenom_utilisateur, PDO::PARAM_STR);
    $insertStmt->bindParam(":login", $login, PDO::PARAM_STR);
    $insertStmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

    if ($insertStmt->execute()) { // Exécute la requête d'insertion.
        $_SESSION['success'] = "Utilisateur ajouté avec succès.";

        header("location: ../pages/admin/admin-users/manage-users.php"); // Redirige vers la page de login avec un message de succès.

        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur.";

        header("location: ../pages/admin/admin-users/manage-users.php"); // Redirige vers la page de login avec un message d'erreur.

        exit();
    }

    unset($checkStmt); // Libère la ressource associée à la requête de vérification.
    unset($insertStmt); // Libère la ressource associée à la requête d'insertion.
}

unset($connexion); // Ferme la connexion à la base de données.

header("location: ../pages/admin/admin-users/manage-users.php"); // Redirige vers la page de login par défaut.
exit(); // Termine le script.
?>
