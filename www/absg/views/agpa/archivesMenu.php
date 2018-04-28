
<h1>Archives des AGPA</h1>

<div id="incipit">
	<div class="illustration archives">&nbsp;</div>
	<dl>
		<dt>Quelques chiffres</dt>
		<dd>9 éditions (de 2006 à 2014);<br/>
			25 participants toutes années confondues;<br/>
			1375 photos proposées.<br/>
		</dd>
		<dt>En ce qui vous concerne</dt>
		<dd>Vous avez participé aux 9 éditions;<br/>
			Vous avez proposé 125 photos;<br/>
			Reçu 34 récompenses (dont 5 en or);<br/>
			Vous êtes 5èm au classement général.
		</dd>
	</dl>
	<br class="clear"/>
</div>



<?php  foreach ($archiveMenu as $year => $edition): ?>
	<a class="category" href="<?php echo base_url(); ?>agpa/archives/a<?php echo $year; ?>">
		<h2 id="category_<?php $g = ($year-2006)%10 -3; echo (($g >= 0)? $g+1:$g); ?>"><?php echo $year ?></h2>
		<p class="winners">
			<img src="<?php echo $edition['winners'][0]['avatar']; ?>" title="<?php echo $edition['winners'][0]['name'] . ' (' . $edition['winners'][0]['award']; ?>)" height="80px"/> &nbsp;
			<img src="<?php echo $edition['winners'][1]['avatar']; ?>" title="<?php echo $edition['winners'][1]['name'] . ' (' . $edition['winners'][1]['award']; ?>)" height="50px"/> &nbsp;
			<img src="<?php echo $edition['winners'][2]['avatar']; ?>" title="<?php echo $edition['winners'][2]['name'] . ' (' . $edition['winners'][2]['award']; ?>)" height="50px"/>
		</p>
		<div class="minislideshow">  
			<img id="minislideshow<?php echo $year; ?>" src="<?php echo base_url() . 'assets/img/agpa/' . $year . '/mini/vignette_' . $edition['bestPhoto_filename']; ?>" title="<?php echo $edition['bestPhoto_title']; ?>"/>
        </div>
		<br class="clear" />
	</a>
<?php endforeach; ?>











	


