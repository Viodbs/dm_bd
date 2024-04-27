// Récupération de la liste déroulante
var select = document.getElementById('insee_com');

// Écouter les changements dans la liste déroulante
select.addEventListener('change', function() {
    // Récupération de la valeur sélectionnée
    var selectedOption = select.options[select.selectedIndex].value;

    // Requête AJAX pour récupérer le nombre de parcelles
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_nombre_parcelles.php?insee_com=' + selectedOption, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Affichage du nombre de parcelles dans le paragraphe
            document.getElementById('nombre_parcelles').textContent = 'Nombre de parcelles : ' + xhr.responseText;
        }
    };
    xhr.send();
});