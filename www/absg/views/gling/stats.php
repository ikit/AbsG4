
	
	
	<div class="stats">
		<div class="col1">
			<h3>Quelques chiffres : </h3>
			<p><span class="label">Membres enregistrées :</span> <span class="value"><?php echo $stats['nbrMembres']; ?></span></p>
			<!--<p><span class="label"> - Femmes : </span> <span class="value"><?php echo $stats['nbrF']; ?></span></p>
			<p><span class="label"> - Hommes :</span> <span class="value"><?php echo $stats['nbrM']; ?></span></p>-->
			<p><span class="label"> - Gueudelot : </span> <span class="value"><?php echo $stats['gueudelot']; ?></span></p>
			<p><span class="label"> - Guibert :</span> <span class="value"><?php echo $stats['guibert']; ?></span></p>
			<p><span class="label"> - Guyomard : </span> <span class="value"><?php echo $stats['guyomard']; ?></span></p>
		</div>
		<div class="col2">
			<h3>Fréquentation :</h3>
			<p><span class="label">Max en ligne simultané :</span> <span class="value"><span style="font-weight:bold;"><?php echo $stats['maxOnline']; ?></span>  &nbsp;le <?php echo $stats['maxOnlineDate']; ?></span></p>
			<p><span class="label">Max visiteur par jour :</span> <span class="value"><span style="font-weight:bold;"><?php echo $stats['maxVisitor']; ?></span>  &nbsp;le <?php echo $stats['maxVisitorDate']; ?></span></p>
		</div>
		<a class="col3" href="<?php echo $stats['userRankUrl']; ?>">

			<h3>Rang G : </h3>
			<img src="<?php echo $stats['rank']['src']; ?>" title="<?php echo $stats['rank']['name']; ?>" alt="<?php echo $stats['rank']['name']; ?>"/>
			<p><span class="label">N°<?php echo $stats['rank']['number']; ?> :</span>  <span class="value"><?php echo $stats['rank']['name']; ?></span><br/>
			   <span class="label">Note G : </span>  <span class="value"><?php echo $stats['rank']['noteg']; ?> G</span></p>
			<div id="progressChart">
				<span id="boundMin"><?php echo $stats['rank']['boundMin']; ?> G</span>
				<div id="progressBar">
					<div id="progression" style="<?php echo $stats['rank']['progression']; ?>"></div>
					<span id="progressionValue"><?php echo $stats['rank']['progressionValue']; ?>%</span>
				</div>
				<span id="boundMax"><?php echo $stats['rank']['boundMax']; ?> G</span>
			</div>
			<p><span class="label" style="width: 100px;">Prochain rang : </span> <span class="value"><?php echo $stats['rank']['nextAward']; ?></span></p>
		</a>
	</div>

