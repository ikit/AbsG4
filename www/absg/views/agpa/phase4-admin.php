

<?php if ($computesScores_nextStep == 2): ?>

	<h1>1 - Vérifications et validation des votes</h1>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>
	
	<?php  foreach ($computeStep as $catId => $cat): ?>
		<a href="javascript:switchDisplay('Category_<?php echo $catId; ?>');" title="Afficher / Masquer les détails">
			<h2 style="color: <?php echo $categories[$catId]->color; ?>;"><?php echo $categories[$catId]->title; ?> (<?php echo $catId; ?>)</h2>
		</a>
		<div id="Category_<?php echo $catId; ?>" class="computeDetails" style="display:none">
	
		<?php  foreach ($cat as $userData): ?>
			<br/>
			<div class="check<?php if ($userData['error']): ?> bad<?php endif; ?>">
				<b><?php echo $userData['name']; ?></b> (<?php echo $userData['id']; ?>)<br/>
				<table width="100%">
				<tr><td><b>vote</b></td><td><b>id photo</b></td><td><b>auteur photo</b></td><td><b>cat photo</b></td><td><b>cat vote</b></td><td><b>annee photo</b></td></tr>
				
				<?php  foreach ($userData['votes'] as $voteId => $vote): ?>
					<tr><td><?php echo $vote['score']; ?></td>
					<td><?php echo $vote['photo_id']; ?></td>
					<td><?php echo $vote['photo_author']; ?></td>
					<td><?php echo $vote['photo_cat']; ?></td>
					<td><?php echo $vote['vote_cat']; ?></td>
					<td><?php echo $vote['photo_year']; ?></td>
					</tr>
				<?php endforeach; ?>

				</table>
				
				<?php if ($userData['error']): ?>
				<div><?php echo $userData['error_msg']; ?></div>
				<?php endif; ?>
			</div>

		<?php endforeach; ?>
		</div>

	<?php endforeach; ?>


	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>












<?php elseif ($computesScores_nextStep == 3): ?>


	<h1>2 - Décompte des votes validés et calcule des notes des photos</h1>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>

	<?php  foreach ($computeStep as $catId => $cat): ?>
		<?php if ($catId > 0): ?>
		<a href="javascript:switchDisplay('Category_<?php echo $catId; ?>');" title="Afficher / Masquer les détails">
			<h2 style="color: <?php echo $categories[$catId]->color; ?>;"><?php echo $categories[$catId]->title; ?> (<?php echo $catId; ?>)</h2>
		</a>
		<div id="Category_<?php echo $catId; ?>" class="computeDetails" style="display:none">
	

		<?php  foreach ($cat['users'] as $userId => $userData): ?>
			<b><?php echo $userData['name']; ?></b> (<?php echo $userData['id']; ?>)<br/>
			<?php  foreach ($userData['photos'] as $photo): ?>
				photo [<?php echo $photo['photo_id']; ?>] : note = <?php echo $photo['score']; ?> (<?php echo $photo['votes']; ?> votes) &rarr; <?php echo $photo['noteg']; ?> points. 
				<?php if ($photo['scoreTitle'] > 0): ?>Slection "Meilleur titre" : <?php echo $photo['scoreTitle']; ?> fois.<?php endif; ?><br/>
			<?php endforeach; ?>
			<br/>
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>


	<a href="javascript:switchDisplay('Category_-3');" title="Afficher / Masquer les détails">
		<h2 style="color: #aaa;">Meilleur titre (-3)</h2>
	</a>
	<div id="Category_-3" class="computeDetails" style="display:none">
	
		<?php  foreach ($computeStep[-3]['photos'] as $photo): ?>
			<b><?php echo $photo->photo_id; ?></b> <?php echo $photo->title; ?> -  <?php echo $photo->scoreTitle; ?> fois.<br/>
		<?php endforeach; ?>
	</div>


	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>








<?php elseif ($computesScores_nextStep == 4): ?>


	<h1>3 - Attributions AGPA (passe 1)</h1>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>

	<?php  foreach ($computeStep as $catId => $cat): ?>
		
		<a href="javascript:switchDisplay('Category_<?php echo $catId; ?>');" title="Afficher / Masquer les détails">
			<h2 style="color: <?php echo $categories[$catId]->color; ?>;"><?php echo $categories[$catId]->title; ?> (<?php echo $catId; ?>)</h2>
		</a>
		<div id="Category_<?php echo $catId; ?>" class="computeDetails" style="display:none">
		<?php if ($catId != -1 && $catId != -3): ?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<?php if ($photo['photo']->score == 0): ?><span style="color:#444"><?php endif; ?>
				<b><?php echo $photo['leaderbord_rank']; ?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['photo']->title; ?> [note:<?php echo $photo['photo']->g_score; ?>  &amp; votes:<?php echo $photo['photo']->votes; ?>  &rarr; <?php echo $photo['photo']->score; ?>  points]<br/>
				<?php if ($photo['photo']->score == 0): ?></span><?php endif; ?>
			<?php endforeach; ?>
		<?php elseif ($catId == -1): ?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<b><?php echo $photo['leaderbord_rank']; ?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['sum8']; ?> points [moyenne:<?php echo $photo['average']; ?>  &amp; votes:<?php echo $photo['votes_number']; ?>  &rarr; <?php echo $photo['sumpoints']; ?>  points]<br/>
			<?php endforeach; ?>
		<?php elseif ($catId == -3):?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<b><?php echo $photo['leaderbord_rank']; ?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['title']; ?> ( <?php echo $photo['scoreTitle']; ?> points )<br/>
			<?php endforeach; ?>
		<?php endif; ?>


		</div>

	<?php endforeach; ?>

	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Poursuivre le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>












<?php elseif ($computesScores_nextStep == 5): ?>


	<h1>4 - Attribution des AGPA (passe 2 - diamant)</h1>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Clore le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>

	<?php  foreach ($computeStep as $catId => $cat): ?>
		
		<a href="javascript:switchDisplay('Category_<?php echo $catId; ?>');" title="Afficher / Masquer les détails">
			<h2 style="color: <?php echo $categories[$catId]->color; ?>;"><?php echo $categories[$catId]->title; ?> (<?php echo $catId; ?>)</h2>
		</a>
		<div id="Category_<?php echo $catId; ?>" class="computeDetails" style="display:none">
		<?php if ($catId > 0): ?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<?php if ($photo['photo']->score == 0): ?><span style="color:#444"><?php endif; ?>
				<b<?php if (isset($photo['photo']->award)) echo ' style="color:red"' ?>><?php echo $photo['leaderbord_rank']; ?><?php if (isset($photo['photo']->award)) echo ' '. $photo['photo']->award ;?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['photo']->title; ?> [note:<?php echo $photo['photo']->g_score; ?>  &amp; votes:<?php echo $photo['photo']->votes; ?>  &rarr; <?php echo $photo['photo']->score; ?>  points]<br/>
				<?php if ($photo['photo']->score == 0): ?></span><?php endif; ?>
			<?php endforeach; ?>
		<?php elseif ($catId == -1): ?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<b<?php if ($photo['award'] != "") echo ' style="color:red"' ?>><?php echo $photo['leaderbord_rank']; ?><?php if ($photo['award'] != "") echo " ".$photo['award'] ;?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['sum8']; ?> points [moyenne:<?php echo $photo['average']; ?>  &amp; votes:<?php echo $photo['votes_number']; ?>  &rarr; <?php echo $photo['sumpoints']; ?>  points]<br/>
			<?php endforeach; ?>
		<?php elseif ($catId == -2):?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<?php if ($photo['photo']->score == 0): ?><span style="color:#444"><?php endif; ?>
				<b<?php if (isset($photo['photo']->awardPhoto)) echo ' style="color:red"' ?>><?php echo $photo['leaderbord_rank']; ?><?php if (isset($photo['photo']->awardPhoto)) echo ' '. $photo['photo']->awardPhoto ;?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['photo']->title; ?> [note:<?php echo $photo['photo']->g_score; ?>  &amp; votes:<?php echo $photo['photo']->votes; ?>  &rarr; <?php echo $photo['photo']->score; ?>  points]<br/>
				<?php if ($photo['photo']->score == 0): ?></span><?php endif; ?>
			<?php endforeach; ?>
		<?php elseif ($catId == -3):?>
			<?php  foreach ($cat['photos'] as $photo): ?>
				<b<?php if (isset($photo['photo']->awardTitle)) echo ' style="color:red"' ?>><?php echo $photo['leaderbord_rank']; ?><?php if (isset($photo['photo']->awardTitle)) echo ' '. $photo['photo']->awardTitle ;?></b> : <?php echo $photo['author']; ?> avec <?php echo $photo['photo']->title; ?> ( <?php echo $photo['photo']->scoreTitle; ?> points )<br/>
			<?php endforeach; ?>
		<?php endif; ?>


		</div>

	<?php endforeach; ?>

	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Clore le dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>


<?php elseif ($computesScores_nextStep == 6): ?>


	<h1>5 - Clôture de l'édition (save DB)</h1>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current">Revenir à l'édition courante.</a>


<?php endif; ?>





