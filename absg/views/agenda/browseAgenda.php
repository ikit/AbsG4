
<h1 class="agenda">L'agenda de la famille</h1>

<div class="notice">
	<p>Très grosse partie du site qui contiendra plusieurs modules qui fonctionneront ensemble.</p>

	<h2>Le répertoire</h2>
	<p>Classique, permet d'accéder aux informations relative à une personne grâce à un index alphabétique.</p>

	<h2>G néalogie</h2>
	<p>Les personnes de ce répertoire appartenant à un même famille pourront être associé via un arbre généalogique</p>

	<h2>Chronolo G et Voya G</h2>
	<p>Permettra d'ajouter des événements localisées dans le temps et l'espace et de les associer avec les personnes listées dans l'agenda.</p>
</div>


<div id="agendaControlsSlot">
	<div id="agendaControlsBar">
		<div class="browseArea">
			<a class="newEntry" href="javascript:displayNewForm();" title="Enregistrer une nouvelle personne">Nouvelle entrée</a>

			
			Parcourir : 
			<select id="filterSelector" style="margin-left: 10px;" onchange="updateFilter('<?php echo $currentSelector; ?>');">
				<?php
					$select = ($currentFilter == "-1") ? ' selected' : '';
					// Création de la liste des auteurs possible
					foreach($filters as $filter)
					{
						$select = ($currentFilter == $filter[0]) ? ' selected' : '';
						echo "<option value=\"{$filter[1]}\"{$select}>{$filter[1]}</option>\n\t\t\t\t\t";
					}
				?>
			</select>
			: 
			<?php foreach ($selectors as $id => $selector) : ?>
				<a id="select_<?php echo $id; ?>" href="<?php echo base_url() . "agenda/browseDirectory/" . $currentFilterFr . urlencode($id); ?>" title="Sélectionner <?php echo $id . " (" . $selector['count'] . ")" ; ?>" class="<?php if ($selector['enable']) echo "selectorEnable"; else echo "selectorDisable" ; if ($currentSelector == $id) echo " selectorActive";?>"><?php echo $id; ?></a> &nbsp; 
			<?php endforeach; ?>

		</div>
		<div class="filterArea">
			<input type="text" onchange="applyFilter(this, '<?php echo  $currentSelector; ?>');" value="" name="quickFilter" placeholder="Affiner votre recherche" /> 
		</div>
	</div>
</div>
<br class="clear"/>

	<div id="newEntry" class="hidden">
		<form action="<?php echo $newFormAction; ?>" method="post" enctype="multipart/form-data">
			<label for="newLastname">Nom :</label>
      <label class="small" for="newSex">Sexe :</label>
      <label class="small" for="newRootFamilly">Maison m�re:</label>
      <input type="text" id="newLastname" name="newLastname" placeholder="Son nom (ex : Létot)" />
			<select class="small" id="newSex" name="newSex">
        <option value="F">Femme</option>
        <option value="M">Homme</option>
      </select>
      <select class="small" id="newRootFamilly" name="newRootFamilly">
        <option value="none">Aucune</option>
        <option value="gueudelot">Gueudelot</option>
        <option value="guibert">Guibert</option>
        <option value="guyomard">Guyomard</option>
        <option value="létot">Létot</option>
      </select>
      <br class="clear"/>
      <label for="newFirstname">Prénom :</label>
      <label for="newPhoto">La photo :</label>
      <input type="text" id="newFirstname" name="newFirstname" placeholder="Son prénom usuel (ex : Raymond)"/>
			<input type="file" id="newPhoto" name="newPhoto" placeholder="Sélectionner sa photo"/>
      <br class="clear"/>
      <label for="newFirstname2">Prénoms :</label>
      <label class="small" for="newBirthday">Naissance :</label>
      <label class="small" for="newDeathday">Décé : </label>
			<input type="text" id="newFirstname2" name="newFirstname2" placeholder="Ses prénoms secondaires (ex : André)"/>
      <input class="small" type="date" id="newBirthday" name="newBirthday"/>
      <input class="small" type="date" id="newDeathday" name="newDeathday"/>
      <br class="clear"/>
      <label for="newSurname">Surnom :</label><br class="clear"/>
			<input type="text" id="newSurname" name="newSurname" placeholder="Un surnom courant (ex : Vati)"/>
      
      <br class="clear"/>
      <p>&nbsp;</p>
			
			<label for="newAddress">Adresse :</label>
      <label for="newPhone">Tel fixe :</label>
			<input type="text" id="newAddress" name="newAddress" placeholder="1 rue Toto - Porte B"/>
      <input type="tel" id="newPhone" name="newPhone" placeholder="Téléphone fixe (ex : +33 2 02 31 44 29 99)" pattern="^((\+\d{1,3}(-| )?\(?\d{1,2}\) (?(-| )?\d{2}){4}$"/>
			<br class="clear"/>
			<label for="newCity">Ville :</label>
      <label for="newMobile">Tel mobile :</label>
			<input type="text" id="newCity" name="newCity" placeholder="75000 Paris"/>
      <input type="tel" id="newMobile" name="newMobile" placeholder="Téléphone mobile (ex : +33 6 02 31 44 29 99)" pattern="^((\+\d{1,3}(-| )?\(?\d{1,2}\) (?(-| )?\d{2}){4}$"/>
			<br class="clear"/>
			<label for="newCountry">Pays :</label>
      <label for="newEmail">Email :</label>
			<input type="text" id="newCountry" name="newCountry" placeholder="France"/>
      <input type="email" id="newEmail" name="newEmail" placeholder="Adresse email"/>
			<br class="clear"/>
      <p>&nbsp;</p>
      <label for="newWebSite">Site web :</label>
			<label for="newSkype">Skype :</label>
      <input type="url" id="newWebSite" name="newWebSite" placeholder="http://"/>
			<input type="text" id="newSkype" name="newSkype" placeholder="Identifiant Skype"/>
      <br class="clear"/>
      <p>&nbsp;</p>
      <input type="submit" value="Enregistrer" />
      <br class="clear"/>
		</form>
   
	</div>



<?php if (count($entries) > 0) : ?>

<?php foreach ($entries as $id => $entry) : ?>
	<div class="entry">
    <?php if ($entry->noPhoto): ?>
    <img src="<?php echo $entry->photo ?>" title="<?php echo $entry->firstname; ?>" alt="<?php echo $entry->firstname; ?>" class="noPhoto" />
    <?php else : ?>
    <img src="<?php echo $entry->photo ?>" title="<?php echo $entry->firstname; ?>" alt="<?php echo $entry->firstname; ?>" class="portrait"/>
    <?php endif; ?>
    <h3><?php echo strtoupper($entry->lastname) . " " . $entry->firstname; if ($entry->firstname2 != null) echo " <span>{$entry->firstname2}</span>"; ?>
    <a class="edit" href="<?php echo base_url() . 'agenda/editEntry/' . $entry->people_id; ?>">Editer</a></h3>
    
    <div class="data">
  		<?php if ($entry->surname != null) echo "<p class=\"surname\">Dit : <span>" . $entry->surname . "</span></p>"; ?>
    		
      <h4>Age : </h4>
  		<p class="age">
        <?php if (isset($entry->age)) : ?>
  			<?php echo $entry->age; ?> 
  			( Né<?php if ($entry->sex =='F') echo "e"; ?> le <?php echo $entry->birthday; ?>
  			<?php if ($entry->deathday != null) : ?> - Décédé<?php if ($entry->sex =='F') echo "e"; ?> le <?php echo $entry->deathday; endif; ?> )
        <?php endif; ?>
  		</p>
     
      <h4>Adresse : </h4>
      <p class="address">
        
        <?php if (isset($entry->city)) : ?>
  			<?php if (isset($entry->address)) echo $entry->address . '<br/>' ; ?> 
        <?php echo $entry->city; if (isset($entry->country)) echo ' (' . $entry->country . ')'; ?> 
        <?php endif; ?>
  		</p>
      <h4>Fixe : </h4><p><?php if (isset($entry->phone)) echo $entry->phone; ?></p>
      <h4>Mobile : </h4><p><?php if (isset($entry->mobilephone)) echo $entry->mobilephone; ?></p>
      <h4>Email : </h4><p><?php if (isset($entry->email)) echo $entry->email; ?></p>
      <h4>Skype : </h4><p><?php if (isset($entry->skype)) echo $entry->skype; ?></p>
    </div>
    
    <?php if (isset($entry->username)) : ?>
    <div class="absgData">
      <p class="avatar"><img src="<?php echo $entry->avatar; ?>" alt="<?php echo $entry->username; ?>" title="<?php echo $entry->username; ?>" /> <?php echo $entry->username; ?></p>
      <h4>Rang : </h4>
      <p><?php echo $entry->title; ?> ( <?php echo $entry->notegTotal; ?> G )</p>
      
      <h4>Maison :</h4>
      <p><?php echo $entry->rootfamilly; ?></p>
      
      <h4>Evénements :</h4>
      <p>-</p>
    </div>
    <?php endif; ?>
  <br class="clear" />
	</div>
<?php endforeach; ?>


<?php else : ?>
	<p class="error">Aucun enregistrement disponible !</p>
<?php endif; ?>

	<script type="text/javascript">
	// <![CDATA[
		// Init variables
	    $(document).ready(function()
	    {
	        agendaInit();
	    });
	// ]]>
	</script>

