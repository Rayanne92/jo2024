<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $idAthlete = filter_input(INPUT_POST, 'idAthlete', FILTER_SANITIZE_NUMBER_INT);
    $idEpreuve = filter_input(INPUT_POST, 'idEpreuve', FILTER_SANITIZE_NUMBER_INT);
    $resultat = filter_input(INPUT_POST, 'resultat', FILTER_SANITIZE_STRING);

    // Vérifiez si les données sont valides
    if (empty($idAthlete) || empty($idEpreuve) || empty($resultat)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-result.php");
        exit();
    }

    try {
        // Requête pour ajouter un résultat
        $query = "INSERT INTO PARTICIPER (id_athlete, id_epreuve, resultat) VALUES (:idAthlete, :idEpreuve, :resultat)";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":idAthlete", $idAthlete, PDO::PARAM_INT);
        $statement->bindParam(":idEpreuve", $idEpreuve, PDO::PARAM_INT);
        $statement->bindParam(":resultat", $resultat, PDO::PARAM_STR);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le résultat a été ajouté avec succès.";
            header("Location: manage-results.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du résultat.";
            header("Location: add-result.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-result.php");
        exit();
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Ajouter un Résultat - Jeux Olympiques 2024</title>
    <style>
    select
    {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin-users/admin.php">Accueil Administration</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Résultat</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="add-result.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce résultat?')">
            <label for="idAthlete">Athlète :</label>
            <select name="idAthlete" id="idAthlete" required>
                <?php
                $queryAthletes = "SELECT * FROM ATHLETE";
                $statementAthletes = $connexion->query($queryAthletes);
                while ($rowAthlete = $statementAthletes->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowAthlete['id_athlete']}'>{$rowAthlete['nom_athlete']} {$rowAthlete['prenom_athlete']}</option>";
                }
                ?>
            </select>

            <label for="idEpreuve">Épreuve :</label>
            <select name="idEpreuve" id="idEpreuve" required>
                <?php
                $queryEpreuves = "SELECT * FROM EPREUVE";
                $statementEpreuves = $connexion->query($queryEpreuves);
                while ($rowEpreuve = $statementEpreuves->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowEpreuve['id_epreuve']}'>{$rowEpreuve['nom_epreuve']}</option>";
                }
                ?>
            </select>

            <label for="resultat">Résultat :</label>
            <input type="text" name="resultat" id="resultat" required>

            <input type="submit" value="Ajouter le Résultat">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-results.php">Retour à la gestion des résultats</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>