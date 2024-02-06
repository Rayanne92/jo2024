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
    <title>Gestion Résultats - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
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
        <h1>Tableau des résultats</h1>
        <div class="action-buttons">
            <button onclick="openAddResultForm()">Ajouter un résultat</button>
        </div>



        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des genres depuis la base de données
            $query = "SELECT ATHLETE.id_athlete, nom_athlete, prenom_athlete, nom_pays, nom_sport, nom_epreuve, resultat
            FROM ATHLETE
            INNER JOIN PAYS ON ATHLETE.id_pays = PAYS.id_pays
            INNER JOIN PARTICIPER ON ATHLETE.id_athlete = PARTICIPER.id_athlete
            INNER JOIN EPREUVE ON PARTICIPER.id_epreuve = EPREUVE.id_epreuve
            INNER JOIN SPORT ON EPREUVE.id_sport = SPORT.id_sport
            ORDER BY nom_athlete";
            // L'erreur "SQLSTATE[23000]: Integrity constraint violation: 1052 Column 'id_athlete' in field list is ambiguous" signifie que la colonne 'id_athlete' est ambiguë dans la liste des champs. Cela se produit généralement lorsque la colonne est présente dans plusieurs tables incluses dans la requête SQL sans être spécifiée de manière non ambiguë.
            // En spécifiant la table 'ATHLETE' avec 'ATHLETE.id_athlete', vous évitez l'ambiguïté et la requête devrait fonctionner correctement. Assurez-vous de faire la même correction dans votre code PHP.
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table>
                <tr>
                <th>Nom Athlète</th>
                <th>Prénom Athlète</th>
                <th>Pays</th>
                <th>Sport</th>
                <th>Epreuves</th>
                <th>Résultats</th>
                <th>Modifier</th>
                <th>Supprimer</th>
                </tr>";

                

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    // Assainir les données avant de les afficher
                    echo "<td>" . htmlspecialchars($row['nom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_pays']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_sport']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['resultat']) . "</td>";

                    echo "<td><button onclick='openModifyResultForm({$row['id_athlete']})'>Modifier</button></td>";
                    echo "<td><button onclick='deleteResultConfirmation({$row['id_athlete']})'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun genre trouvé.</p>";
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
        function openAddResultForm() {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification
            // L'URL contient un paramètre "id"
            window.location.href = 'add-result.php';
        }

        function openModifyResultForm(id_athlete) {
            // Ajoutez ici le code pour afficher un formulaire stylisé pour modifier un genre
            // alert(id_genre);
            window.location.href = 'modify-result.php?id_athlete=' + id_athlete;
        }

        function deleteResultConfirmation(id_athlete) {
            // Ajoutez ici le code pour afficher une fenêtre de confirmation pour supprimer un genre
            if (confirm("Êtes-vous sûr de vouloir supprimer ce résultat?")) {
                // Ajoutez ici le code pour la suppression du genre
                // alert(id_genre);
                window.location.href = 'delete-result.php?id_athlete=' + id_athlete;
            }
        }
    </script>
</body>

</html>
