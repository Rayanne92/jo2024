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
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom de l'épreuve est vide
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($heureEpreuve)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-event.php");
        exit();
    }

    try {
        // Vérifiez si l'épreuve existe déjà
        $queryCheck = "SELECT id_epreuve FROM EPREUVE WHERE nom_epreuve = :nomEpreuve";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'épreuve existe déjà.";
            header("Location: add-event.php");
            exit();
        } else {

            // Requête pour ajouter une épreuve
            $query = "INSERT INTO EPREUVE (nom_epreuve, date_epreuve, heure_epreuve, id_lieu, id_sport) VALUES (:nomEpreuve, :dateEpreuve, :heureEpreuve, :idLieu, :idSport)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
            $statement->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
            $statement->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);

            $statement->bindParam(":idLieu", $_POST['lieuEpreuve'], PDO::PARAM_INT);
            $statement->bindParam(":idSport", $_POST['sportEpreuve'], PDO::PARAM_INT);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le lieu a été ajouté avec succès.";
                header("Location: manage-events.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du lieu.";
                header("Location: add-event.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-event.php");
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
    <title>Ajouter une Épreuve - Jeux Olympiques 2024</title>
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
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="manage-gender.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter une épreuve</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="add-event.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cette épreuve?')">

            <label for=" nomEpreuve">Nom de l'épreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve" required>

            <label for=" dateEpreuve">Date de l'épreuve :</label>
            <input type="date" name="dateEpreuve" id="dateEpreuve" required>

            <label for=" heureEpreuve">Heure de l'épreuve :</label>
            <input type="time" name="heureEpreuve" id="heureEpreuve" required>

            <label for=" lieuEpreuve">Lieu de l'épreuve :</label>
            <select name="lieuEpreuve" id="lieuEpreuve" required>
                <?php
                // Récupérer la liste des lieux depuis la base de données
                $queryLieux = "SELECT id_lieu, nom_lieu FROM LIEU ORDER BY nom_lieu";
                $statementLieux = $connexion->prepare($queryLieux);
                $statementLieux->execute();

                // Afficher les options du menu déroulant pour les lieux
                while ($rowLieu = $statementLieux->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowLieu['id_lieu']}'>{$rowLieu['nom_lieu']}</option>";
                }
                ?>
            </select>

            <label for=" sportEpreuve">Sport de l'épreuve :</label>
            <select name="sportEpreuve" id="sportEpreuve" required>
                <?php
                // Récupérer la liste des sports depuis la base de données
                $querySports = "SELECT id_sport, nom_sport FROM SPORT ORDER BY nom_sport";
                $statementSports = $connexion->prepare($querySports);
                $statementSports->execute();

                // Afficher les options du menu déroulant pour les sports
                while ($rowSport = $statementSports->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$rowSport['id_sport']}'>{$rowSport['nom_sport']}</option>";
                }
                ?>
            </select>

            <br><br>

            <input type="submit" value="Ajouter l'épreuve">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-events.php">Retour à la gestion des lieux</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>