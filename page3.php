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
<div class="notice-texte">
<p> Ici figurent des informations relatives aux projets de cessions suspendus. Une cession suspendue est une vente à l'arrêt. 
    Il s'agit potentiellement d'une perte d'argent conséquente.  </p></div><br>
    <img src="communes_cess_sus.png" alt="communes_cess_sus">

</div>

<div class="container">

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
echo '<div class="notice-texte"><p> Voici la requête SQL qui permet d\'obtenir les noms des communes ayant un nombre de cession suspendues supérieure à 3. Il s\'agit de communes qui doivent faire l\'objet d\'une attention particulière pour les chargés de vente. </p></div>';
echo '<div class="sql-container">';
echo '<pre>';

echo "SELECT com.nom_commun AS commune, COUNT(*) AS nombre_projets_suspendus
FROM cessions c
JOIN parcelles p ON c.id_unique = p.id_unique
JOIN commune com ON p.insee_com = com.insee_com
WHERE c.statut_ces = 'SUSPENDU'
GROUP BY com.nom_commun
HAVING COUNT(*) > 3
ORDER BY COUNT(*) DESC;";

echo '</pre>';
echo '</div>';


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


echo "<h2>Résultats des cessions suspendues par commune</h2>";
echo "<table border='1'>
        <tr>
            <th>Commune</th>
            <th>Nombre de projets suspendus</th>
        </tr>";
while ($row = pg_fetch_assoc($result)) {
    echo "<tr>
            <td>" . $row['commune'] . "</td>
            <td>" . $row['nombre_projets_suspendus'] . "</td>
          </tr>";
}
echo "</table>";
echo "<br>";

echo "<h2> Visualisation des 4 communes les plus touchées par les cessions suspendues <h2/>";
echo '<iframe src="http://localhost/bd_foncier_sncf/qgis2web_2024_04_22-12_37_43_432807/index.html"></iframe>';
?>
</div>


<?php


$sql = "SELECT com.nom_commun AS commune,
COUNT(*) AS nombre_projets_suspendus,
ROUND(SUM(ST_Area(ST_Transform(p.geom, 2154))::numeric), 2) AS surface_m2
FROM cessions c
JOIN parcelles p ON c.id_unique = p.id_unique
JOIN commune com ON p.insee_com = com.insee_com
WHERE c.statut_ces = 'SUSPENDU'
GROUP BY com.nom_commun
ORDER BY surface_m2 DESC
LIMIT 10";
$result = pg_query($dbh, $sql);
?>

<div class="container">
<h1> QUID des surfaces de cessions ? </h1>
<div class="notice-texte"><p> Il est important de noter que les communes ayant le plus de parcelles concernées par des cessions suspendus
  ne sont pas synomyme de surfaces importantes. Si la commune de Mitry-Mory est effectivement celle avec le plus de surfaces concernées par 
  des dossiers suspendus, ce n'est pas le cas de Saint-Ouen-l'Aumône, Sevran et Dampmart qui ne sont même pas dans le top 10 des surfaces les plus grandes !
</p></div>
<?php
echo "<h3> Voici la requête SQL pour comparer les surfaces  </h3>";
echo '<div class="sql-container">';
echo '<pre>';

echo "SELECT com.nom_commun AS commune,
COUNT(*) AS nombre_projets_suspendus,
ROUND(SUM(ST_Area(ST_Transform(p.geom, 2154))::numeric), 2) AS surface_m2
FROM cessions c
JOIN parcelles p ON c.id_unique = p.id_unique
JOIN commune com ON p.insee_com = com.insee_com
WHERE c.statut_ces = 'SUSPENDU'
GROUP BY com.nom_commun
ORDER BY surface_m2 DESC
LIMIT 10";

echo '</pre>';
echo '</div>';
?>
    <h2>Résultats des 10 communes avec les surfaces les plus importantes</h2>
    <table border='1'>
        <tr>
            <th>Commune</th>
            <th>Nombre de projets suspendus</th>
            <th>Surface totale (m²)</th>
        </tr>
        <?php
        // Affichage des résultats dans un tableau HTML
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row['commune'] . "</td>
                    <td>" . $row['nombre_projets_suspendus'] . "</td>
                    <td>" . $row['surface_m2'] . "</td>
                  </tr>";
        }
        ?>
    </table>
    <script src="app.js"></script>

</body>
</html>
