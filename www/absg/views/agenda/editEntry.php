
<h1 class="agenda">L'agenda de la famille</h1>


	<div id="newEntry">
		<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data">
			<label for="newLastname">Nom :</label>
      <label class="small" for="newSex">Sexe :</label>
      <label class="small" for="newRootFamilly">Maison mère:</label>
      <input value="<?php echo $entry->lastname ; ?>" type="text" id="newLastname" name="newLastname" placeholder="Son nom (ex : Létot)"/>
			<select class="small" id="newSex" name="newSex">
        <option value="F"<?php if ($entry->sex == 'F') echo " selected"; ?>>Femme</option>
        <option value="M"<?php if ($entry->sex == 'M') echo " selected"; ?>>Homme</option>
      </select>
      <select class="small" id="newRootFamilly" name="newRootFamilly">
        <option value="none"<?php if (!isset($entry->rootfamilly)) echo " selected"; ?>>Aucune</option>
        <option value="gueudelot"<?php if ($entry->rootfamilly == 'gueudelot') echo " selected"; ?>>Gueudelot</option>
        <option value="guibert"<?php if ($entry->rootfamilly == 'guibert') echo " selected"; ?>>Guibert</option>
        <option value="guyomard"<?php if ($entry->rootfamilly == 'guyomard') echo " selected"; ?>>Guyomard</option>
        <option value="létot"<?php if ($entry->rootfamilly == 'létot') echo " selected"; ?>>Létot</option>
      </select>
      <br class="clear"/>
      <label for="newFirstname">Prénom :</label>
      <label class="small" for="newPhoto">La photo :</label>
      <?php if ($entry->noPhoto): ?>
      <img src="<?php echo $entry->photo ?>" title="<?php echo $entry->firstname; ?>" alt="<?php echo $entry->firstname; ?>" class="noPhoto" />
      <?php else : ?>
      <img src="<?php echo $entry->photo ?>" title="<?php echo $entry->firstname; ?>" alt="<?php echo $entry->firstname; ?>" class="portrait "/>
      <?php endif; ?>
 
      <input value="<?php echo $entry->firstname ; ?>" type="text" id="newFirstname" name="newFirstname" placeholder="Son prénom usuel (ex : Raymond)"/>
			<input class="small" type="file" id="newPhoto" name="newPhoto" placeholder="Sélectionner sa photo"/>

      <label for="newFirstname2">Prénoms :</label>
      <label class="small" for="newBirthday">Naissance :</label>
      
			<input value="<?php echo $entry->firstname2 ; ?>" type="text" id="newFirstname2" name="newFirstname2" placeholder="Ses prénoms secondaires (ex : André)"/>
      <input value="<?php if (isset($entry->birthday)) echo date("d/m/Y", $entry->birthday); ?>" class="small" type="text" placeholder="jj/mm/aaaa" id="newBirthday" name="newBirthday"/>
      

      <label for="newSurname">Surnom :</label>
      <label class="small" for="newDeathday">Décé : </label>
			<input value="<?php echo $entry->surname ; ?>" type="text" id="newSurname" name="newSurname" placeholder="Un surnom courant (ex : Vati)"/>
      <input value="<?php if (isset($entry->deathday)) echo date("d/m/Y", $entry->deathday) ; ?>" class="small" type="text" placeholder="jj/mm/aaaa" id="newDeathday" name="newDeathday"/>
      <br class="clear"/>
      <p>&nbsp;</p>
			
			<label for="newAddress">Adresse :</label>
      <label for="newPhone">Tel fixe :</label>
			<input value="<?php echo $entry->address ; ?>" type="text" id="newAddress" name="newAddress" placeholder="1 rue Toto - Porte B"/>
      <input value="<?php echo $entry->phone ; ?>" type="tel" id="newPhone" name="newPhone" placeholder="Téléphone fixe (ex : +33 2 02 31 44 29 99)" pattern="^((\+\d{1,3}(-| )?\(?\d{1,2}\) (?(-| )?\d{2}){4}$"/>
			<br class="clear"/>
			<label for="newCity">Ville :</label>
      <label for="newMobile">Tel mobile :</label>
			<input value="<?php echo $entry->city ; ?>" type="text" id="newCity" name="newCity" placeholder="75000 Paris"/>
      <input value="<?php echo $entry->mobilephone ; ?>" type="tel" id="newMobile" name="newMobile" placeholder="Téléphone mobile (ex : +33 6 02 31 44 29 99)" pattern="^((\+\d{1,3}(-| )?\(?\d{1,2}\) (?(-| )?\d{2}){4}$"/>
			<br class="clear"/>
			<label for="newCountry">Pays :</label>
      <label for="newEmail">Email :</label>
			<input value="<?php echo $entry->country ; ?>" type="text" id="newCountry" name="newCountry" placeholder="France"/>
      <input value="<?php echo $entry->email ; ?>" type="email" id="newEmail" name="newEmail" placeholder="Adresse email"/>
			<br class="clear"/>
      <p>&nbsp;</p>
      <label for="newWebSite">Site web :</label>
			<label for="newSkype">Skype :</label>
      <input value="<?php echo $entry->website ; ?>" type="url" id="newWebSite" name="newWebSite" placeholder="http://"/>
			<input value="<?php echo $entry->skype ; ?>" type="text" id="newSkype" name="newSkype" placeholder="Identifiant Skype"/>
      <br class="clear"/>
      <p>&nbsp;</p>
      <input type="hidden" value="<?php echo $entry->people_id ; ?>" id="newPeopleId" name="newPeopleId"/>
      <input type="submit" value="Enregistrer" />
      <br class="clear"/>
		</form>
   
	</div>




	<script type="text/javascript">
	// <![CDATA[
		// Init variables
		//var currentPage = <?php echo ($currentPage); ?>;
		//var totalPage = <?php echo $totalPage; ?>;
	    $(document).ready(function()
	    {
	        agendaInit();
	    });
	// ]]>
	</script>

