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


$query_cess_signe = "SELECT statut_ces, id_unique
FROM cessions
WHERE statut_ces = 'SIGNE'";

$result_cess_signe = pg_query($dbh, $query_cess_signe);
if (!$result_cess_signe) {
    die("Erreur lors de l'exécution de la requête pour récupérer les cessions signées : " . pg_last_error());
}

$query_cess_comp = "SELECT *
FROM cessions
WHERE statut_ces = 'COMPENSATION'";

$result_cess_comp = pg_query($dbh, $query_cess_comp);
if (!$result_cess_comp) {
    die("Erreur lors de l'exécution de la requête pour récupérer les cessions signées : " . pg_last_error());
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour</title>
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

<h2>Suppression des parcelles vendues</h2>
  <div class="notice-texte">
  <p>Les projets signés sont des cessions achevées donc il s'agit de parcelles n'étant plus dans le patrimoine de l'entreprise
  </p>
</div>
  <div class="sql-container">
  <pre><code>
<?php echo "$query_cess_signe"; ?>
  </code></pre>
  </div>




<table>
      <tr>
          <th>Statut</th>
          <th>Identifiant</th>
      </tr>
<?php


      // Affichage des résultats de la requête des cessions signees par département dans le tableau
      while ($row_cess_signee = pg_fetch_assoc($result_cess_signe)) {
          echo "<tr>";
          echo "<td>" . $row_cess_signee['statut_ces'] . "</td>";
          echo "<td>" . $row_cess_signee['id_unique'] . "</td>";
          echo "</tr>";
      }
      ?>

    </table>

<div class="notice-texte">
  <p> On veut donc supprimer ces données mais elles sont dépendantes de la table parcelles au sein de laquelle se trouvent toutes les propriétés.
    On ira donc supprimer les données dans la table parcelles les supprimer de la table cessions. (elles ne sont plus visibles a cause de la requete qui suit)
  </p>
</div>


  <div class="sql-container">
  <pre><code>
DELETE FROM cessions
WHERE statut_ces = 'SIGNE';
  </code></pre>
  </div>
  <div class="notice-texte">
  <p> On veux ajouter des données dans la table cessions avec le statut compensation pour les parcelles identifiées à Clay-Souilly. </p>
  </div>
  <div class="sql-container">
  <pre><code>
INSERT INTO cessions (id_unique, statut_ces)
SELECT id_unique, 'COMPENSATION' AS statut_ces
FROM parcelles
WHERE insee_com = '77118';

-- On fait une selection pour voir si ça a fonctionné 

SELECT *
FROM cessions
WHERE statut_ces = 'COMPENSATION';

</code></pre>
  </div>
 


  <table>
      <tr>
          <th>Statut</th>
          <th>Identifiant</th>
      </tr>
<?php


      // Affichage des résultats de la requête des cessions signees par département dans le tableau
      while ($row_cess_comp = pg_fetch_assoc($result_cess_comp)) {
          echo "<tr>";
          echo "<td>" . $row_cess_comp['statut_ces'] . "</td>";
          echo "<td>" . $row_cess_comp['id_unique'] . "</td>";
          echo "</tr>";
      }
      ?>

    </table>
</div>