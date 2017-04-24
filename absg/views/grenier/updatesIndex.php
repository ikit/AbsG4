
	<h1 class="grenier">Les mises à jours du site</h1>


	<p>Vous trouverez ici la liste des mises à jours du site avec les principales évolutions apportées :</p>
	<ul>
	<?php foreach ($files as $file) : ?>
		<li><a href="<?php echo $file["url"]; ?>"><?php echo $file["title"]; ?></a></li>

	<?php endforeach; ?>
	</ul>