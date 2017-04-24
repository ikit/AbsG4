

	<div class="bgoverlay">&nbsp;</div>
	<div class="illustration r00">
		<div id="content">

			<h1>Changer de mot de passe</h1>
			
			<?php if (isset($error)) echo "<h2>$error</h2>"; ?>


			<form method="post" action="<?php echo site_url('user/changePassword'); ?>">

			    <label for="oldpwd">Ancien mot de passe : </label>
			    <input type="password" name="oldpwd" value="" placeholder="Ancien mot de passe" />
			    <?php echo form_error('oldpwd'); ?><br/>
			 
			    <label for="newpwd">Nouveau mot de passe :</label>
			    <input type="password" name="newpwd" value="" placeholder="Nouveau mot de passe"/>
			    <?php echo form_error('newpwd'); ?><br/>
			    <input type="password" name="newpwd2" value="" placeholder="Confirmer"/>
			    <?php echo form_error('newpwd2'); ?><br/>

			 
			    <input type="submit" value="Valider" />
			</form>


			<!--
			<h1>Vos informations</h1>
			
			<form method="post" action="<?php echo site_url('user/updateProfil'); ?>">

				<fieldset>
					<legend>Identité :</legend>
				    <label for="lastname">Nom : </label>
				    <input type="text" name="lastname" value="" placeholder="Votre nom de famille" />
				    <?php echo form_error('lastname'); ?>
				    <label for="firstname">Prénom :</label>
				    <input type="password" name="firstname" value="" placeholder="Votre nouveau mot de passe"/>
				    <?php echo form_error('firstname'); ?>
				    <label for="surname">Surnom :</label>
				    <input type="password" name="surname" value="" placeholder="Votre nouveau mot de passe"/>
				    <?php echo form_error('surname'); ?>
				    <label for="birthday">Date de naissance :</label>
				    <input type="password" name="birthday" id="birthday" value="" placeholder="Votre date de naissance"/>
				    <?php echo form_error('birthday'); ?>
				    <label for="sex">Sexe :</label>
				    <input type="radio" name="sex" value="M"/>Homme 
					<input type="radio" name="sex" value="F"/>Femme
					<input type="radio" name="sex" value="T"/>Va savoir
				    <?php echo form_error('sex'); ?>
				    <label for="rootfamilly">Maison mère :</label>
				    <select name="rootfamilly">
						<option value="Gueudelot">Gueudelot</option>
						<option value="Guibert">Guibert</option>
						<option value="Guyomard">Guyomard</option>
						<option value="Létot">Létot</option>
					</select>
					<?php echo form_error('rootfamilly'); ?>
			 	</fieldset>
			    <input type="submit" value="Enregistrer" />
			</form>
			-->
		</div>
	</div>

	<script type="text/javascript">
    // <![CDATA[
        
        // On exécute le code js quand le document HTML est complétement chargé
        $(document).ready(function()
			$( "#birthday" ).datepicker();
		});
	</script>
	

	
