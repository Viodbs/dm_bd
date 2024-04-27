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
        <a class="nav-link" href="index.php">Contexte</a>
        <a class="nav-link" href="page2.php">Cessions par numéro INSEE</a>
        <a class="nav-link" href="page3.php">Statuts cessions</a>
        <a class="nav-link" href="page4.php">Etude par occupation du sol</a>
        <a class="nav-link" href="page5.php">Mise à jour des données</a>
    </nav>
</div><br><br><br>
<div class="container">

<h1> Resultat de l'étude</h1>
<div class="notice-texte"><p> Ici figurent des informations relatives aux projets de cessions. Il s'agit des ventes en cours ou suspendues
    Organisées par numéro INSEE, cela nous permet de voir quelles sont les communes les plus dynamiques en termes de vente du foncier SNCF
Cette information peut être précieuse dans un contexte prospectif. </p>
</div>
</div>

<div class="container">
<h2>Résultats des cessions par communes INSEE</h2>
<img src="parcelles_insee.png" alt="parcelles_insee" width="100%">
<?php

// Requête SQL pour sélectionner le nombre de parcelles par commune avec des projets en cours
$sql = "SELECT p.insee_com, COUNT(*) AS nombre_parcelles
        FROM parcelles p
        JOIN cessions c ON p.id_unique = c.id_unique
        WHERE c.statut_ces = 'EN COURS'
        GROUP BY p.insee_com
        ORDER BY COUNT(*) DESC";

// Exécution de la requête SQL
$result = pg_query($dbh, $sql);

echo '<div class="notice-texte"><p>Voici la requête SQL qui permet d\'obtenir toutes les cessions en cours. Les cessions en cours sont des projets actuels de vente. </p></div>';

echo '<div class="sql-container">';
echo '<pre>';
echo 'SELECT p.insee_com, COUNT(*) AS nombre_parcelles
      FROM parcelles p
      JOIN cessions c ON p.id_unique = c.id_unique
      WHERE c.statut_ces = \'EN COURS\'
      GROUP BY p.insee_com
      ORDER BY COUNT(*) DESC';
echo '</pre>';
echo '</div>';

echo '<div class="notice-texte"><p>Le 93 arrive en premier avec le code INSEE de la commune 93066 qui est Saint-Denis. Saint-Denis connaît des dynamiques de population très rapides.</p></div><br>';


// Affichage des résultats dans des tableaux enveloppés dans des divs
echo "<table border='1'>
        <tr>
            <th>Commune (INSEE)</th>
            <th>Nombre de parcelles</th>
        </tr>";
while ($row = pg_fetch_assoc($result)) {
    echo "<tr>
            <td>" . $row['insee_com'] . "</td>
            <td>" . $row['nombre_parcelles'] . "</td>
          </tr>";
}
echo "</table>";


pg_close($dbh);
?>
    <script src="app.js"></script>
</body>
</html>
