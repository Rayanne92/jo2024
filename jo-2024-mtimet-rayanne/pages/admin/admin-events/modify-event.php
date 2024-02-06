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
    $_SESSION['error'] = "ID de l'épreuve manquant.";
    header("Location: manage-events.php");
    exit();
}

$id_epreuve = filter_input(INPUT_GET, 'id_epreuve', FILTER_VALIDATE_INT);

// Vérifiez si l'ID de l'épreuve est un entier valide
if (!$id_epreuve && $id_epreuve !== 0) {
    $_SESSION['error'] = "ID de l'épreuve invalide.";
    header("Location: manage-events.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);
    $lieuEpreuve = filter_input(INPUT_POST, 'lieuEpreuve', FILTER_VALIDATE_INT);
    $sportEpreuve = filter_input(INPUT_POST, 'sportEpreuve', FILTER_VALIDATE_INT);


    // Vérifiez si le nom de l'épreuve est vide
    if (empty($nomEpreuve)) {
        $_SESSION['error'] = "Le nom de l'épreuve ne peut pas être vide.";
        header("Location: modify-event.php?id_epreuve=$id_epreuve");
        exit();
    }

    try {
        // Vérifiez si l'épreuve existe déjà
        $queryCheck = "SELECT id_epreuve FROM EPREUVE WHERE nom_epreuve = :nomEpreuve AND id_epreuve <> :idEpreuve";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementCheck->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'épreuve existe déjà.";
            header("Location: modify-event.php?id_epreuve=$id_epreuve");
            exit();
        }

        // Requête pour mettre à jour l'épreuve
        $query = "UPDATE EPREUVE SET nom_epreuve = :nomEpreuve, date_epreuve = :dateEpreuve, heure_epreuve = :heureEpreuve, id_lieu = :lieuEpreuve, id_sport = :sportEpreuve WHERE id_epreuve = :idEpreuve";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":lieuEpreuve", $lieuEpreuve, PDO::PARAM_INT);
        $statement->bindParam(":sportEpreuve", $sportEpreuve, PDO::PARAM_INT);
        $statement->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'épreuve a été modifiée avec succès.";
            header("Location: manage-events.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'épreuve.";
            header("Location: modify-event.php?id_epreuve=$id_epreuve");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-event.php?id_epreuve=$id_epreuve");
        exit();
    }
}

// Récupérez les informations de l'épreuve pour affichage dans le formulaire
try {
    $queryEpreuve = "SELECT nom_epreuve, date_epreuve, heure_epreuve FROM EPREUVE WHERE id_epreuve = :idEpreuve";
    $statementEpreuve = $connexion->prepare($queryEpreuve);
    $statementEpreuve->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);
    $statementEpreuve->execute();

    if ($statementEpreuve->rowCount() > 0) {
        $epreuve = $statementEpreuve->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Épreuve non trouvée.";
        header("Location: manage-events.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-events.php");
    exit();
}
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
    <title>Modifier une Épreuve - Jeux Olympiques 2024</title>
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
                <li><a href="manage-epreuve.php">Gestion Épreuves</a></li>
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
        <h1>Modifier une Épreuve</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="modify-event.php?id_epreuve=<?php echo $id_epreuve; ?>" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cette épreuve?')">
            <label for="nomEpreuve">Nom de l'Épreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve" value="<?php echo htmlspecialchars($epreuve['nom_epreuve']); ?>" required>

            <label for="dateEpreuve">Date de l'Épreuve :</label>
            <input type="date" name="dateEpreuve" id="dateEpreuve" value="<?php echo htmlspecialchars($epreuve['date_epreuve']); ?>" required>

            <label for="heureEpreuve">Heure de l'Épreuve :</label>
            <input type="time" name="heureEpreuve" id="heureEpreuve" value="<?php echo htmlspecialchars($epreuve['heure_epreuve']); ?>" required>



            <label for="lieuEpreuve">Lieu de l'Épreuve :</label>
            <select name="lieuEpreuve" id="lieuEpreuve" required>
                <?php
                // Sélectionnez tous les lieux disponibles dans la base de données
                $queryLieux = "SELECT id_lieu, nom_lieu FROM LIEU";
                $stmtLieux = $connexion->query($queryLieux);
                while ($rowLieu = $stmtLieux->fetch(PDO::FETCH_ASSOC)) {
                    // Si le lieu de l'épreuve correspond à ce lieu, le sélectionner par défaut
                    $selected = ($rowLieu['id_lieu'] == $id_lieu) ? 'selected' : '';
                    echo '<option value="' . $rowLieu['id_lieu'] . '" ' . $selected . '>' . $rowLieu['nom_lieu'] . '</option>';
                }
                ?>
            </select>

            <label for="sportEpreuve">Sport de l'Épreuve :</label>
            <select name="sportEpreuve" id="sportEpreuve" required>
                <?php
                // Sélectionnez tous les sports disponibles dans la base de données
                $querySports = "SELECT id_sport, nom_sport FROM SPORT";
                $stmtSports = $connexion->query($querySports);
                while ($rowSport = $stmtSports->fetch(PDO::FETCH_ASSOC)) {
                    // Si le sport de l'épreuve correspond à ce sport, le sélectionner par défaut
                    $selected = ($rowSport['id_sport'] == $id_sport) ? 'selected' : '';
                    echo '<option value="' . $rowSport['id_sport'] . '" ' . $selected . '>' . $rowSport['nom_sport'] . '</option>';
                }
                ?>
            </select>


            <input type="submit" value="Modifier l'Épreuve">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-events.php">Retour à la gestion des épreuves</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>
