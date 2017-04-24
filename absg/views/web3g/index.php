
	<h1 class="web3g">Le web 3G</h1>

	<?php 
	$side = false;
	foreach ($sites as $site) : 
		$side = !$side;
	?>
	<div class="site <?php echo (($side)? 'left' : 'right'); ?>">
		<a href="<?php echo $site->url; ?>" target="blanck" alt="aller sur le site" onclick="javascript:clickOn('<?php echo $site->web_id; ?>');">
			<div class="illustration" style="background-image: url('<?php echo base_url();?>assets/img/web3g/<?php echo str_pad($site->web_id, 2, "0", STR_PAD_LEFT); ?>.png')"> </div>
			<h2><?php echo $site->title; ?></h2>
			<p class="description"><?php echo $site->description; ?></p>
			<h3>Dernière mise à jour : le <span><?php echo $site->last_update; ?></h3>
			<p class="notification">Notifiée par <?php echo $site->username; ?><br/><span class="note"/><?php echo $site->last_update_note; ?></span></p>
		</a>
		<div class="form">
			<form method="post" action="<?php echo $notification_url . $site->web_id; ?>">
			    <input type="submit" value="Notifier tout le monde !" />

				<label for="note">Prévenir les autres d'une mise à jour : </label>
			    <input type="text" name="note" value=""  placeholder="Commentaire (optionnel)" />
			   
			 
			</form> 
		</div>
	</div>
	<?php endforeach; ?>

	<br class="clear"/>
