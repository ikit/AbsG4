
<h1 class="zaffa">Administration du site</h1>
  
  
<h2 class="zaffa">Outils / Debug</h2>
<ul>
    <li>mktime > date : </li>
    <li>date > mktime : </li>
</ul>


<h2 class="zaffa">Gestion globale du site</h2>
<dl>
    <dt><a href="<?php echo base_url(); ?>zaffa/phpinfo">PHP info</a></dt>
    <dd>Affiche la configuration php du site</dd>
    
    <dt>Activer/Désactiver le site</dt>
    <dd>Pour désactiver "proprement" le site. Interdit l'accès à tout les membres qui ne sont pas admin (auth='*'). Affiche un message d'erreur personalisable.</dd>
    
    <dt><a href="<?php echo base_url(); ?>zaffa/newActivity">New Activity</a></dt>
    <dd>Pour enregistrer une nouvelle activité sur le site, qui sera affiché sur la page d'accueil à gauche de la photo du moment</dd>
    
    <dt>Gérer les Zaffaneries</dt>
    <dd>Pour gérer le "planing" des illustrations "Zaffaneries" affichées sur la page d'accueil.</dd>
    
    <dt>Annuler Immt</dt>
    <dd>Supprime la dernière image du moment enregistrée</dd>
</dl>

<h2 class="zaffa">Gestion des utilisateurs</h2>
<ul>
    <li>Créer nouvel utilisateur</li>
    <li><a href="<?php echo base_url(); ?>zaffa/resetUsersData">ResetUsersData</a> : Recalcule les infos de tout le monde (noteG, rangs, notifications)</li>
    <li>zaffa/resetPassword/&lt;user_id&gt; : réinitialise le password de l'utilisateur avec le mot de passe 'toto'</li>
</ul>


<h2 class="zaffa">AGPA</h2>
<dl>
    <dt>Fichier cérémonie : <input type="text" placeholder="l'année..." /> <input type="submit" value="Télécharger"/> </dt>
    <dd>Pour récupérer le fichier "cérémonie" utilisé par le logiciel lors de la cérémonie pour afficher les nominés (et donc le classement pour chaque catégorie)</dd>
    
    <dt>Récupérer données (export csv compatible fichier excel de florent)</dt>
    <dt>Gérer dates des différentes phases </dt>
    <dt>Stats "express" de l'édition en cours</dt>

</dl>


http://absolumentg.fr/agpa/agpaCeremonyFile/<ANNEE>


Exporte au format CSV les données des photos (pour les fichiers excels de Florent)
http://absolumentg.fr/agpa/photosCSVExport/<ANNEE>

Exporte au format CSV les données des votes (pour les fichiers excels de Florent)
http://absolumentg.fr/agpa/votesCSVExport/<ANNEE>

Exporte au format CSV les données des votes (pour les fichiers excels de Florent)
http://absolumentg.fr/agpa/usersCSVExport/<ANNEE>

Récupérer les originaux sur le serveur ! (les zip atteingne souvent les 400Mo, ça peut prendre du temps à construire le zip avant que le téléchargement commence.
http://absolumentg.fr/agpa/getAndClearOriginals/<ANNEE>


<script type="text/javascript">
    // <![CDATA[
        var baseURL = "http://absolumentg.fr/";
        
        // On exécute le code js quand le document HTML est complétement chargé
        $(document).ready(function () 
        {
	    $('#myform').on('submit', function(e) 
	    {
		e.preventDefault();
		$.ajax({
		    url : $(this).attr('action') || window.location.pathname,
		    type: "GET",
		    data: $(this).serialize(),
		    success: function (data) 
		    {
			$("#form_output").html(data);
		    },
		    error: function (jXHR, textStatus, errorThrown) 
		    {
			alert(errorThrown);
		    }
		});
	    });
	});
    // ]]>
    </script>


