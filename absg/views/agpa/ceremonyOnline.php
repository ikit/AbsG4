
<h1>Cérémonie <?php echo $ceremonyYear; ?> des AGPA</h1>

<?php if ($ceremonyStep==0): // Présentation ?>
<h2 class=""><?php if ($categories[$ceremonyCat]->has_variants) echo $categories[$ceremonyCat]->vtitle; else echo $categories[$ceremonyCat]->title; ?></h2>
<img src="http://absolumentg.fr/assets/theme/agpa/img/cupesMaxi/<?php if ($ceremonyCat ==8) echo "$ceremonyYear/"; echo "c$ceremonyCat.jpg"; ?>" />


<?php elseif ($ceremonyStep==1): // Les nominés ?>
<h3 class="">Les nominés de la catégorie : <?php if ($categories[$ceremonyCat]->has_variants) echo $categories[$ceremonyCat]->vtitle; else echo $categories[$ceremonyCat]->title; ?></h3>
<?php shuffle($photoData); foreach ($photoData as $p): ?>
	<div class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
		</div>
		<p class="title"><?php echo $p->title; ?><br/>(<?php echo $p->username; ?>)</p>
	</div>
<?php endforeach; ?>


<?php elseif ($ceremonyStep==2): // L'AGPA de bronze ?>


<?php elseif ($ceremonyStep==3): // L'AGPA d'argent ?>


<?php elseif ($ceremonyStep==4): // L'AGPA d'or ?>


<?php else: // Accueil ?>


<?php endif; ?>
<br class="clear" style="margin-bottom: 50px;"/>
















	


