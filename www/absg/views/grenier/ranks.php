
	<h1 class="grenier">Les rangs</h1>


	<p>Un système de rangs a été mis en place sur le site afin d'égayer le site en l'agrémentant de jolis dessins qui changent au fur et à mesure qu'on utilise le site.</p>
	<p>Chaque membre a donc un rang en fonction de son activité. Au début tout le monde commence au rang 0 : "Fi G", et évolue petit à petit en fonction de sa contribution. 
	   Des rangs spéciaux existes pour les membres qui ont un statut spécial sur le site. Par exemple Olive est le "Grand Gourou", ou bien Fred et Vati qui ne sont plus des membres actif sont des "Pote à G" ...</p>


	<br/>
	<ul class="ranks">
		<?php $i=0; foreach ($ranks as $idx => $rank) : ?>
		<li class="<?php echo "row" . ($i % 2); ?>">


			<div class="rank">
				<p class="image">
				<?php if ($rank['active'] == true) : ?>
				<a href="<?php echo $rank['maxi']; ?>" rel="lightbox[rankGallery]" title="<?php echo $rank['title']; ?>">
					<img src="<?php echo $rank['mini']; ?>" />
				</a>
				<?php endif; ?>
				</p>
			</div>
			<div class="infos">
				<p class="title"><span>Titre : </span><?php echo $rank['title']; ?></p>
				<p class="noteg"><span>Score G : </span><?php echo $rank['gnote']; ?></p>
				<p class="users"><span>Membres : </span><p>
				<?php foreach ($rank['users'] as $user) : ?>
					<img src="<?php echo $user['avatar']; ?>" title="<?php echo $user['name']; ?> (score G = <?php echo $user['gnote']; ?>)" height="50px"/>
				<?php endforeach; ?>
			</div>
			<br class="clear"/>
		</li>
		<?php ++$i; endforeach;  ?>
	</ul>


