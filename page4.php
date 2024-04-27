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

// Requête SQL 1
$query1 = "DO $$
    DECLARE
        total_surface_m2 numeric := 0;
        prix_par_m2 numeric := 0.906; -- Prix par mètre carré, constante
        montant_bientot_gagne numeric := 0;
    BEGIN
        -- Calcul du total des surfaces des parcelles concernées par des cessions suspendues
        SELECT SUM(ST_Area(ST_Transform(p.geom, 2154)))
        INTO total_surface_m2
        FROM cessions c
        JOIN parcelles p ON c.id_unique = p.id_unique
        JOIN occupation_du_sol o ON ST_Intersects(c.geom, o.geom)
        WHERE c.statut_ces = 'EN COURS'
        AND o.libelle_81 IN ('Bois ou forêts', 'Prairies');

        -- Calcul du montant d'argent perdu
        montant_bientot_gagne := total_surface_m2 * prix_par_m2;

        -- Affichage du résultat
        RAISE NOTICE 'Le montant d''argent bientôt gagné par des cessions de terres végétalisées est de % €.', montant_bientot_gagne;
    END $$;
";

// Exécution de la requête SQL 1
$result1 = pg_query($dbh, $query1);
if (!$result1) {
    die("Erreur lors de l'exécution de la requête 1 : " . pg_last_error());
}

// Récupération de la première notice
$notice1 = pg_last_notice($dbh);

// Requête SQL 2
$query2 = "DO $$
DECLARE
    total_surface_m2 numeric := 0;
    prix_par_m2 numeric := 6000; -- Prix par mètre carré, constante
    montant_bientot_gagne numeric := 0;
BEGIN
    -- Calcul du total des surfaces des parcelles concernées par des cessions suspendues
    SELECT SUM(ST_Area(ST_Transform(p.geom, 2154)))
    INTO total_surface_m2
    FROM cessions c
    JOIN parcelles p ON c.id_unique = p.id_unique
    JOIN occupation_du_sol o ON ST_Intersects(c.geom, o.geom)
    WHERE c.statut_ces = 'SUSPENDU'
    AND o.libelle_81 IN ('Habitat collectif', 'Activité', 'Equipement'); -- Exclure les types de surface spécifiés

    -- Calcul du montant d'argent gagné
    montant_bientot_gagne := total_surface_m2 * prix_par_m2;

    -- Affichage du résultat
    RAISE NOTICE 'Le montant d''argent pour les ventes en Habitat collectif, Activité, Equipement est %.', montant_bientot_gagne;
END $$;
";

// Exécution de la requête SQL 2
$result2 = pg_query($dbh, $query2);
if (!$result2) {
    die("Erreur lors de l'exécution de la requête 2 : " . pg_last_error());
}

// Récupération de la deuxième notice
$notice2 = pg_last_notice($dbh);

// Requête SQL pour récupérer les surfaces par département
$query_departements = "SELECT com.departemen AS departement,
                              ROUND(SUM(ST_Area(ST_Transform(p.geom, 2154))::numeric), 2) AS surface_totale_m2
                       FROM commune com
                       JOIN parcelles p ON p.insee_com = com.insee_com
                       GROUP BY com.departemen
                       ORDER BY surface_totale_m2 DESC";

$result_departements = pg_query($dbh, $query_departements);
if (!$result_departements) {
    die("Erreur lors de l'exécution de la requête pour récupérer les surfaces par département : " . pg_last_error());
}


$query_compensation = "SELECT LEFT(CAST(insee AS TEXT), 2) AS dept_num, libelle_81
FROM occupation_du_sol
WHERE LEFT(CAST(insee AS TEXT), 2) = '77' -- Filtrer les communes avec les deux premiers chiffres '77' pour le département 77
GROUP BY libelle_81, insee
ORDER BY COUNT(*) DESC
LIMIT 3";

$result_compensation = pg_query($dbh, $query_compensation);
if (!$result_compensation) {
    die("Erreur lors de l'exécution de la requête pour récupérer les compensations potentielles : " . pg_last_error());
}

$query_surface_engazonnee = "SELECT SUM(ST_Area(ST_Transform(geom, 2154))) AS surface_engazonnee
FROM occupation_du_sol
WHERE LEFT(CAST(insee AS TEXT), 2) = '77'
AND libelle_81 = 'Surfaces engazonnées avec ou sans arbustes'";

$result_surface_engazonnee = pg_query($dbh, $query_surface_engazonnee);
if (!$result_surface_engazonnee) {
    die("Erreur lors de l'exécution de la requête : " . pg_last_error());
}


$query_surface_engazonnee_commune = 
"SELECT c.nom_commun AS commune, 
SUM(ST_Area(ST_Transform(o.geom, 2154))) AS surface_engazonnee
FROM commune c
JOIN occupation_du_sol o ON c.insee_com = CAST(o.insee AS TEXT)
WHERE LEFT(c.insee_com, 2) = '77'
AND o.libelle_81 = 'Surfaces engazonnées avec ou sans arbustes'
GROUP BY c.nom_commun, c.insee_com
ORDER BY surface_engazonnee DESC
LIMIT 3";


$result_surface_engazonnee_commune = pg_query($dbh, $query_surface_engazonnee_commune);
if (!$result_surface_engazonnee_commune) {
    die("Erreur lors de l'exécution de la requête : " . pg_last_error());
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
  <h1>L'occupation du sol qui fait varier les prix </h1>
  <div class="notice-texte">
  <p> Si en Ile-de-France le foncier est déjà plutôt cher, les types d'occupation du sol peuvent être déterminants dans 
    le coût final d'une parcelle.
  </p></div>
  <h3> Combien empoche t'on avec des ventes de surfaces végétalisées ? </h3>
  <div class="notice-texte"> <p> Cette série d'instuction permet d'intersecter les surfaces dont la composition est végétalisée avec les parcelles cadastrales. 
On va pouvoir estimer le prix que l'on obtenir des surfaces concernées grâce à la déclaration d'une constante (le prix au m²).
  </p></div>
  <div class="sql-container">
    <pre><code>
    <?php echo $query1; ?>
    </code></pre>
  </div>
  <div class="notice-container">
    <p class="notice-text"><?php echo $notice1; ?></p>
  </div>
  <div class="notice-texte">
  <p>On veut s'assurer qu'on ne perd pas d'argent avec des projets qui concerne de l'habitat et qui rapport le plus, avec une moyenne de 6000 euros au m².</p>
</div>
  <div class="sql-container">
    <pre><code>
    <?php echo $query2; ?>
    </code></pre>
  </div>

  <div class="notice-container">
    <p class="notice-text"><?php echo "$notice2 NULL"; ?></p>
  </div>
  <div class="notice-texte">
  <p>Le résultat est null, il n'y a aucun terrain qu'on pourrait vendre très cher qui est en suspension de cession, les 
    chargés d'affaires ont bien travaillés !
  </p>
</div>
  <!-- Tableau des surfaces par département -->
  <h2>Surfaces par département</h2>
  <div class="notice-texte">
  <p>On veut savoir quel département à le plus de foncier tout type confondu. Cela nous permettra de cibler un département
  </p>
</div>
  <div class="sql-container">
  <pre><code>
<?php echo "$query_departements"; ?>
  </code></pre>
  </div>

  <table>
      <tr>
          <th>Département</th>
          <th>Surface totale (m²)</th>
      </tr>
      <?php
      // Affichage des résultats de la requête des surfaces par département dans le tableau
      while ($row_departements = pg_fetch_assoc($result_departements)) {
          echo "<tr>";
          echo "<td>" . $row_departements['departement'] . "</td>";
          echo "<td>" . $row_departements['surface_totale_m2'] . "</td>";
          echo "</tr>";
      }
      ?>


  </table>

  <div class="notice-texte">
  <p>L'entreprise a décider de s'intérésser aux compensations écologiques comme autre type de valorisation du foncier.
    La compensation passe par la vente de terrain ayant des atouts écologiques pour compenser des travaux qui altèrent les écosystèmes
  </p>

</div>
<h2>Quelles occupations majoritaires dans le 77 ? </h2>

<div class="sql-container">
  <pre><code>
<?php echo "$query_compensation"; ?>
  </code></pre>
  </div>
  <table>
    <tr>
        <th>dept_num</th>
        <th>libelle_81</th>
    </tr>
    <?php
    // Affichage des résultats de la requête de compensation dans le tableau
    while ($row_compensation = pg_fetch_assoc($result_compensation)) {
        echo "<tr>";
        echo "<td>" . $row_compensation['dept_num'] . "</td>";
        echo "<td>" . $row_compensation['libelle_81'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>


<div class="notice-texte">
  <p>On veut connaître le stock des surfaces engazonnées.
  </p>
  </div>

  <div class="sql-container">
  <pre><code>
<?php echo "$query_surface_engazonnee"; ?>
  </code></pre>
  </div>

<?php


$row_surface_engazonnee = pg_fetch_assoc($result_surface_engazonnee);
$surface_engazonnee = $row_surface_engazonnee['surface_engazonnee'];

echo '<div class="notice-container">';
// Affichage de la notice avec le résultat
echo "La surface engazonnée dans le département 77 est de " . $surface_engazonnee . " m².";
echo '</div>';
?>

</body>
</html>



<div class="notice-texte">
  <p>Pour envoyer des écologues sur le terrain, on souhaite identifier les communes avec le plus de surfaces engazonnées
  </p>
  </div>

  <div class="sql-container">
  <pre><code>
<?php echo "$query_surface_engazonnee_commune"; ?>
  </code></pre>
  </div>
  <table>
    <tr>
        <th>Communes</th>
        <th>Surfaces engazonnées</th>
    </tr>
    <?php
    // Affichage des résultats de la requête de compensation dans le tableau
    while ($row = pg_fetch_assoc($result_surface_engazonnee_commune)) {
        echo "<tr>";
        echo "<td>" . $row['commune'] . "</td>";
        echo "<td>" . $row['surface_engazonnee'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<div class="notice-texte">
  <p>C'est clairement la commune de Claye-Souilly qui mérite d'être visitée pour y rechercher des atouts écologiques
  </p>
  </div>


<?php
// Fermer la connexion à la base de données
pg_close($dbh);
?>
