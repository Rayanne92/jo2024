<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['prenom_utilisateur'];
$prenom_utilisateur = $_SESSION['nom_utilisateur'];
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
    <title>Gestion Calendrier - Jeux Olympiques 2024</title>
    <style>
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
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
                <li><a href="manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Calendrier des Épreuves</h1>
        <div class="action-buttons">
            <button onclick="openAddEpreuveForm()">Ajouter une épreuve</button>
        </div>

        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des lieux depuis la base de données
            $query = "SELECT * FROM EPREUVE ORDER BY nom_epreuve";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Épreuve</th><th>Date de l'épreuve</th><th>Heure de l'épreuve</th><th>Lieu</th><th>Sport</th><th>Modifier</th><th>Supprimer</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    // Assainir les données avant de les afficher
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['heure_epreuve']) . "</td>";

                    // Remplacer "ID du lieu" par le nom du lieu
                    $idLieu = $row['id_lieu'];
                    $queryLieu = "SELECT nom_lieu FROM LIEU WHERE id_lieu = :idLieu";
                    $statementLieu = $connexion->prepare($queryLieu);
                    $statementLieu->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);
                    $statementLieu->execute();
                    $lieu = $statementLieu->fetch(PDO::FETCH_ASSOC);
                    echo "<td>" . htmlspecialchars($lieu['nom_lieu']) . "</td>";

                    // Remplacer "ID du sport" par le nom du sport
                    $idSport = $row['id_sport'];
                    $querySport = "SELECT nom_sport FROM SPORT WHERE id_sport = :idSport";
                    $statementSport = $connexion->prepare($querySport);
                    $statementSport->bindParam(":idSport", $idSport, PDO::PARAM_INT);
                    $statementSport->execute();
                    $sport = $statementSport->fetch(PDO::FETCH_ASSOC);
                    echo "<td>" . htmlspecialchars($sport['nom_sport']) . "</td>";

                    echo "<td><button onclick='openModifyEpreuveForm({$row['id_epreuve']})'>Modifier</button></td>";
                    echo "<td><button onclick='deleteEpreuveConfirmation({$row['id_epreuve']})'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucune épreuve trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        // Afficher les erreurs en PHP
        // (fonctionne à condition d’avoir activé l’option en local)
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ?>
        <p class="paragraph-link">
            <a class="link-home" href="../admin-users/admin.php">Accueil administration</a>
        </p>

    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
    <script>
        function openAddEpreuveForm() {
            window.location.href = 'add-event.php';
        }

        function openModifyEpreuveForm(id_epreuve) {
            window.location.href = 'modify-event.php?id_epreuve=' + id_epreuve;
        }

        function deleteEpreuveConfirmation(id_epreuve) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette épreuve?")) {
                window.location.href = 'delete-event.php?id_epreuve=' + id_epreuve;
            }
        }
    </script>
</body>

</html>