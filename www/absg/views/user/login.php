

	<div class="bgoverlay">&nbsp;</div>
	<div class="illustration r00">
		<div id="content">

			<h1>Veuillez vous identifier</h1>
			<p style="visibility:<?php if ($attempt > 1)  echo 'visible'; else echo 'hidden'; ?>">
				Nom d'utilisateur ou mot de passe incorrect.</br>
				Essai n°<?php echo $attempt; ?>.
			</p>

			<form method="post" action="<?php echo site_url('user/attempt'); ?>">
			    <label for="username">Nom utilisateur : </label>
			    <input type="text" name="username" value="" placeholder="Login" />
			    <?php echo form_error('username'); ?>
			 
			    <label for="mdp">Mot de passe :</label>
			    <input type="password" name="mdp" value="" placeholder="Mot de passe"/>
			    <?php echo form_error('mdp'); ?>
			 
			    <input type="submit" value="Envoyer" />
			</form>
		</div>
	</div>


	

	
