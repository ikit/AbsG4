

	<h1>Changer de mot de passe</h1>
	
	<?php if (isset($error)): ?>
	<h2 class="error"><?php echo $error; ?></h2>
	<?php endif; ?>

	<div class="pwdFrame">
		<div class="illustration"></div>
		<form method="post" action="<?php echo site_url('user/changePassword'); ?>">

		    <label for="oldpwd">Ancien mot de passe</label>
		    <input type="password" name="oldpwd" value="" placeholder="Votre mot de passe actuel" />
		    <?php echo form_error('oldpwd'); ?>
		 
		    <label for="newpwd">Nouveau mot de passe</label>
		    <input type="password" name="newpwd" value="" placeholder="Votre nouveau mot de passe"/>
		    <?php echo form_error('newpwd'); ?>
		    <input type="password" name="newpwd2" value="" placeholder="Confirmer votre nouveau mot de passe"/>
		    <?php echo form_error('newpwd2'); ?>

		 
		    <input type="submit" value="Valider" />
		</form>
		<img src="<?php echo base_url(); ?>assets/theme/default/img/topSecret.png"/>
	</div>

	<br class="clear"/>

			

	
