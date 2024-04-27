<?php
// Connexion à la base de données PostgreSQL
$host = 'localhost'; // Adresse du serveur PostgreSQL
$dbname = 'foncier_sncf'; // Nom de votre base de données
$user = 'postgres'; // Nom d'utilisateur PostgreSQL
$pass = 'Viodbs1998'; // Mot de passe PostgreSQL

// Chaîne de connexion PostgreSQL
$conn_string = "host=$host dbname=$dbname user=$user password=$pass";

// Connexion à la base de données
$dbh = pg_connect($conn_string);
if (!$dbh) {
    die("Erreur de connexion à la base de données : " . pg_last_error());
}
?>









<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats des cessions suspendues par commune</title>
    <link rel="stylesheet" href="css_html/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Calibri+Light&display=swap" rel="stylesheet">

</head>
<body>
<div class="bar">
    <nav class="nav">
        <a class="nav-link" href="connexion.php">Contexte</a>
        <a class="nav-link" href="page2.php">Cessions par numéro INSEE</a>
        <a class="nav-link" href="page3.php">Statuts cessions</a>
        <a class="nav-link" href="page4.php">Etude par occupation du sol</a>
        <a class="nav-link" href="page5.php">Mise à jour des données</a>
    </nav>
</div><br><br><br>
<div class="container">




<?php
// Requête SQL pour sélectionner les communes avec le nombre de projets suspendus
$sql = "SELECT com.nom_commun AS commune, COUNT(*) AS nombre_projets_suspendus
        FROM cessions c
        JOIN parcelles p ON c.id_unique = p.id_unique
        JOIN commune com ON p.insee_com = com.insee_com
        WHERE c.statut_ces = 'SUSPENDU'
        GROUP BY com.nom_commun
        HAVING COUNT(*) > 3
        ORDER BY COUNT(*) DESC";

// Exécution de la requête SQL
$result = pg_query($dbh, $sql);

// Affichage des résultats dans une table HTML
echo "<h1> Analyse du foncier SNCF en Ile-de France : Synthèse sur les types de valorisation grâce aux requêtes SQL </h1>";
echo '<iframe src="http://localhost/bd_foncier_sncf/qgis2web_2024_04_26-21_22_09_784025"></iframe>';

echo '<div class="sql-container">
    <pre><code>
CREATE TABLE Commune(
   insee_com VARCHAR(50),
   nom_commune VARCHAR(50) NOT NULL,
   departement VARCHAR(50) NOT NULL,
   surface_com DECIMAL(15,2) NOT NULL,
   geometry TEXT NOT NULL,
   PRIMARY KEY(insee_com)
);

CREATE TABLE Parcelles(
   id_unique VARCHAR(50),
   reference_cadastrale VARCHAR(50) NOT NULL,
   insee_com VARCHAR(50) NOT NULL,
   prefixe VARCHAR(50) NOT NULL,
   numero VARCHAR(50) NOT NULL,
   geometry TEXT NOT NULL,
   insee_com_1 VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_unique),
   FOREIGN KEY(insee_com_1) REFERENCES Commune(insee_com)
);

CREATE TABLE Occupation_du_sol_principale(
   fid VARCHAR(50),
   type_occupation VARCHAR(50) NOT NULL,
   mos2021 VARCHAR(50) NOT NULL,
   insee VARCHAR(50) NOT NULL,
   libelle_81 VARCHAR(50) NOT NULL,
   code_81 VARCHAR(50) NOT NULL,
   code_47 VARCHAR(50) NOT NULL,
   libelle_47 VARCHAR(50) NOT NULL,
   code_24 VARCHAR(50) NOT NULL,
   libelle_24 VARCHAR(50),
   code_11 VARCHAR(50) NOT NULL,
   libelle_11 VARCHAR(50) NOT NULL,
   geometry TEXT NOT NULL,
   id_unique VARCHAR(50) NOT NULL,
   PRIMARY KEY(fid),
   FOREIGN KEY(id_unique) REFERENCES Parcelles(id_unique)
);

CREATE TABLE Cessions(
   id_unique VARCHAR(50),
   statut_cession VARCHAR(50) NOT NULL,
   geometry TEXT,
   id_unique_1 VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_unique),
   FOREIGN KEY(id_unique_1) REFERENCES Parcelles(id_unique)
);
    </code></pre>
</div>';

echo "<div class='notice-texte'><p> Ce devoir a été réalisé à partir d'informations géographiques de plusieurs éléments relatifs au foncier </p></div>";
echo "<br>";
echo '<img src="modele_ER.png" alt="Modèle Entité-Relation" width="100%">';

// Fermeture de la connexion à la base de données
pg_close($dbh);
?>
    <script src="app.js"></script>
</body>
</html>
