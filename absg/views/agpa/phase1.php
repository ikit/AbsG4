
<h1>Edition <?php echo $current_phase_year; ?> des AGPA</h1>

<div id="incipit">
	<div class="illustration phase1">&nbsp;</div>
	<h2 style="color:#9FDBFF">Phase 1 : <span>Enregistrement des photos</span></h2> 
	<div id="frise">
		<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
	</div>
	<dl>
		<dt>Période</dt>
		<dd>
			Du 15 décembre <?php echo ($current_phase_year -1); ?> au 15 décembre <?php echo $current_phase_year; ?>.<br/>
		    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 2.
		</dd>
		<dt>Description</dt> 
		<dd>
			Durant cette première phase du concours, vous pouvez enregistrer vos photos. 
			Bien que ce concours soit sans obligation, toute participation (aussi modeste soit-elle) est la bienvenue, ceci afin de rendre plus intéressant le concours.
		</dd>
		<dt style="color: #f55">Réglement</dt>
		<dd>
			Vos photos doivent impérativement avoir été prises par vous même (pas forcément avec votre appareil photo) et au cours de l'année <?php echo $current_phase_year; ?>. 
			De même, depuis les AGPA 2007, elles doivent obligatoirement posséder un titre.<br/>
			<i>Pour plus de détails, vous pouvez lire <a href="<?php echo base_url() . 'agpa/rules'; ?>" title="Lire le réglement en ligne">le réglement</a>.</i>
		</dd>
	</dl>
	<br class="clear"/>
</div>

<div id="infogen" class="pannel canceled" <?php if($have_photos) echo 'style="display:none;"'; ?>>
	<h3>Information</h3>
	Vous n'avez pour le moment proposé aucune photo. Bien que ce concours soit sans obligation, toute participation (aussi modeste soit-elle) est la bienvenue, ceci afin de rendre plus intéressant le concours.<br/><br/>
	Merci d'avance pour votre participation.
</div>

<?php  $i = 0; foreach ($categories as $cat): ?>
	<?php if ($cat->category_id > 0): ?>
	<div class="category <?php echo (($i % 2 == 0) ? 'left' : 'right'); $i++; ?>">
		<h2 id="category_<?php echo $cat->category_id; if ($cat->category_id==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $cat->color; ?>;"><?php   if ($cat->category_id == 8) echo $cat->vtitle; else echo $cat->title; ?></h2>
		<p style="color: <?php echo $cat->color; ?>;">
			<?php if ($cat->category_id == 8) echo $cat->vdescription; else echo $cat->description; ?>
		</p>
		
		<?php foreach ($cat->photos as $p): ?>
			<div id="o_<?php echo $cat->category_id; ?>_<?php echo $p['num']; ?>" class="oeuvre">
			<div class="photo">
				<?php if ($p['empty']) : ?>
				<a class="emptySlot" onclick="javascript:showNewImage('<?php echo $cat->category_id; ?>', 'o_<?php echo $cat->category_id; ?>_<?php echo $p['num']; ?>');" title="Cliquez ici pour insérer une photo">&nbsp;</a>
				<?php else : ?>
				<a href="<?php echo $p['url_fullscreen']; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p['title']; ?>">
				<img src="<?php echo $p['url_thumb']; ?>" /></a>
				<?php endif; ?>
			</div>
			<?php if (!$p['empty']) : ?>

			<p class="title"><?php echo $p['title']; ?></p>
			<div class="pannel">
			    <a class="deletePhoto" onclick="javascript:showDeleteImage('<?php echo $p['id']; ?>', 'o_<?php echo $cat->category_id; ?>_<?php echo $p['num']; ?>');" title="Supprimer la photo ?">Supprimer</a>
			    <a class="editPhotoTitle" onclick="javascript:showEditTitle('<?php echo $p['id']; ?>', 'o_<?php echo $cat->category_id; ?>_<?php echo $p['num']; ?>');" title="Modifier le titre de la photo ?">Modifier le titre</a>
			    <br class="clear"/>
			</div>
			<?php endif; ?>
			</div>
		<?php endforeach; ?>

	</div>
	<?php if ($i % 2 == 0) : ?><br class="clear" style="margin: 10px 0 20px 0;"/><?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>
<br class="clear" style="margin-bottom: 50px;"/>






<!-- Le formulaire pour les nouvelles image -->
<div id="NewImage" title="Enregistrer une nouvelle photo">
	<form action=" <?php echo base_url() . 'agpa/newPhoto'; ?>" method="post" enctype="multipart/form-data" target="hiddeniframe">
		<div>
		<dl>
			<dt><label for="newTitle">Titre</label><br/><span>Le titre est obligatoire. Sinon la photo sera refusée lors de la phase 2.</span></dt>
			<dd><input type="text" name="newTitle" id="newTitle" maxlength="100" tabindex="1" class="inputbox"/></dd>
			
			<dt><label for="newPhoto">Image</label><br/><span>Veuillez indiquer le chemin où se trouve votre photo afin que nous la rappatrions sur le serveur des AGPA. Une photo, pour être acceptée, doit être au format jpg.</span></dt>
			<dd><input type="file" name="newPhoto" id="newPhoto" tabindex="2" class="inputbox"/></dd>
		</dl>
		<br class="clear"/>
		<input type="hidden" name="newCategory" id="newCategory" value=""/>
		<br/>
		<div>
			<input type="submit" value="Enregistrer la photo pour l'édition  <?php echo $current_phase_year; ?> des AGPA" onclick="javascript:submitImage();" tabindex="4" class="button1"/>
		</div>
		</div>
	</form>
</div>

<!-- Le formulaire pour les supprimer une image -->
<div id="DeleteImage" title="Suppression d'une photo">
	<p>Etes vous sûr de vouloir supprimer définitivement cette photo ?<br/><img src="" alt="aperçu"/><br/>
	<a class="buttonYes" onclick="javascript:deletePhoto('yes');">Supprimer</a> <a class="buttonNo" onclick="javascript:deletePhoto('no');">Annuler</a>
	</p>
</div>

<!-- Le formulaire pour les modifier le titre d'une image -->
<div id="EditTitle" title="Edition d'un titre">
	<p><img src="" alt="aperçut"/><br/>
	<input type="text" name="editTitleInput" id="editTitleInput" maxlength="100" tabindex="1" class="inputbox"/><br/>
	<a class="buttonYes" onclick="javascript:editPhotoTitle('yes');">Modifier</a> <a class="buttonNo" onclick="javascript:editPhotoTitle('no');">Annuler</a>
	</p>
</div>

<!-- La page d'attente -->
<div id="MessageDialog" title="&nbsp;">
</div>

<!-- iframe poubelle pour les redirections ajax des forms -->
<iframe id="hiddeniframe" name="hiddeniframe" style="display:none;" src="about:blank"></iframe>











	


