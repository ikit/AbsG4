
	



	<div class="col1">
		<img id="zaffanerie" src="<?php echo $zaffanerie['url']; ?>" title="<?php echo $zaffanerie['alt']; ?>" />
		<h1>Dernières activités</h1>
		<?php echo $lastActivities; ?>
		<a id="seeLogs" href="<?php echo base_url(); ?>grenier/updates" title="Voir l'historique des activités du site" >Voir l'historique</a>
	</div>


	<div class="col2">
		<?php echo $immt; ?>
	</div>

	<br class="clear"/>
	<p class="passag">Les visiteurs du jour !</p>
	<div class="passag">
	<table>
		<tr>
			<?php $previousStyle=''; foreach ($presence as $hour => $data): ?>
			<th class="schedule <?php echo $data['style']; if ($previousStyle != explode(' ', $data['style'])[0]) echo ' f'; $previousStyle = explode(' ', $data['style'])[0]; ?>">
				<?php if ($previousStyle != explode(' ', $data['style'])[0]) echo ' f'; $previousStyle = explode(' ', $data['style'])[0]; ?>
				<?php echo $hour; ?>
			</th>
			<?php endforeach; ?>
		</tr>
		<tr>
			<?php $previousStyle=''; foreach ($presence as $hour => $data): ?>
			<td class="schedule <?php echo $data['style']; if ($previousStyle != explode(' ', $data['style'])[0]) echo ' f'; $previousStyle = explode(' ', $data['style'])[0]; ?>">
				<?php foreach ($data['users'] as $id => $userData): ?>
				<img src="<?php echo $userData['avatar']; ?>" title="<?php echo $userData['username']; ?>"/>
				<?php endforeach; ?>
			</td>
			<?php endforeach; ?>
		</tr>
	</table>
	</div>
