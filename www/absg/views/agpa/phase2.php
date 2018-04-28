
<h1>Edition <?php echo $current_phase_year; ?> des AGPA</h1>

<div id="incipit">
	<div class="illustration phase2">&nbsp;</div>
	<h2 style="color:#9FDBFF">Phase 2 : <span>Vérification des photos</span></h2> 
	<div id="frise">
		<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
	</div>
	<dl>
		<dt>Période</dt>
		<dd> Du 15 décembre <?php echo $current_phase_year; ?> au 17 décembre <?php echo $current_phase_year; ?>.<br/>
		    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 3.
		</dd>
	<?php if (!$show_category): ?>
		<dt>Description </dt>
		<dd> Le but de cette phase est de prendre le temps de regarder les photos de tout le monde et de s'assurer qu'elles sont en règles avant de passer aux votes. 
			Si vous repérez des erreurs telles que: 
			<ul>
				<li> des photos qui ne respectent pas le réglement ;</li>
				<li> des photos qui se ressemblent trop (ce qui nuie à la qualité du concours) ;</li>
				<li> des erreurs concernant l'envoie de vos photos (fichier illisible, erreur dans le titre, mauvaise catégorie, etc.);</li>
				<li> ou tout autre problème ...</li>
			</ul>
			Vous devez en parler soit sur le forum, soit directement avec Olivier ou Florent (mail, téléphone, sms, etc.). 
		</dd>

		<dt style="color: #f55">Réglement</dt>
		<dd> Les photos doivent impérativement avoir été prise au cours de l'année <?php echo $current_phase_year; ?> par celui qui les propose (peut importe l'appareil photo). 
			De même, depuis les AGPA 2007, les photos doivent obligatoirement posséder un titre.<br/>
			<i>Pour plus de détails, vous pouvez lire <a href="<?php echo base_url() . 'agpa/rules'; ?>" title="Lire le réglement en ligne">le réglement</a>. </i>
		</dd>
	<?php endif; ?>
	</dl>
	<br class="clear"/>
</div>



<?php if ($have_photos_error): ?>
	<div class="pbHeader">
		<h3>Information</h3>
		Les jurés se posent des questions sur les photos suivantes. Elles risquent d'être annulées.<br/> 
		Vous êtes invité à en discuter sur le forum, ou directement avec Olivier et/ou Florent. 
	</div>
	<div class="pbBody">
	<br/>
	<?php foreach ($photos_error as $p): ?>
	<div id="perror_<?php echo $p->photo_id; ?>_<?php echo $p->number; ?>" class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
		</div>
		<p class="title"><?php echo $p->title; ?></p>
		<div class="pannel canceled">
		    <?php echo $p->error; ?>
		</div>
	</div>
	<?php endforeach; ?>
	<br class="clear"/>
	</div>
<?php endif; ?>





<?php if (!$show_category): ?>


<?php  foreach ($categories as $cat): ?>
	<?php if ($cat->category_id > 0): ?>
	<a class="category" href="agpa/current/<?php echo $cat->category_id; ?>">
		<h2 id="category_<?php echo $cat->category_id; if ($cat->category_id==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $cat->color; ?>;"><?php echo $cat->title; ?></h2>
		<p class="details">
			<span class="photos"><?php echo $cat->nbr_photo; ?></span>
			<span class="authors"><?php echo count($cat->authors); ?></span>
		</p>
		<div class="minislideshow">  
			<img id="minislideshow<?php echo $cat->category_id; ?>" src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . array_shift(array_values($photos[$cat->category_id]))->filename; ?>" />
        </div>

		<br class="clear" />
	</a>
	<?php endif; ?>
<?php endforeach; ?>


<script type="text/javascript">
    // <![CDATA[

    	var root_path = '<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_'; ?>';
    	<?php  foreach ($categories as $cat): ?>
    	<?php if ($cat->category_id > 0): ?>
    	var cat_<?php echo $cat->category_id; ?> = new Array (
    		<?php 
				$i=0; $filearray = "";
				foreach ($photos[$cat->category_id] as $p)
				{
					$filearray .= "'" . $p->filename; 
					++$i; 
					$filearray .= ($i%count($photos[$cat->category_id]) != 0) ? "'," : "'"; 
				}
				echo $filearray; ?>
				);
        <?php endif; ?>
		<?php endforeach; ?>

		var imgIdx = 0;

        // On exécute le code js quand le document HTML est complétement chargé
        $(document).ready(function()
        {
            setInterval(function()
            {   
            	++imgIdx;

            	<?php foreach ($categories as $cat): if ($cat->category_id > 0): ?>
            	$("#minislideshow<?php echo $cat->category_id; ?>").fadeOut(200, function() {
		            $("#minislideshow<?php echo $cat->category_id; ?>").attr("src", root_path + cat_<?php echo $cat->category_id; ?>[imgIdx % cat_<?php echo $cat->category_id; ?>.length]);
		        })
	        	.fadeIn(200);
                <?php endif; endforeach; ?>
			}, 5000);
        });
    // ]]>
    </script>


<?php else: ?>

	<div class="category">
		<h2 id="category_<?php echo $show_category; if ($show_category==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $categories[$show_category]->color; ?>;"><?php echo $categories[$show_category]->title; ?></h2>
		<p class="details">
			<span class="photos"><?php echo $categories[$show_category]->nbr_photo; ?></span>
			<span class="authors"><?php echo count($categories[$show_category]->authors); ?></span>
		</p>
		<p><?php echo $categories[$show_category]->description; ?></p>
	</div>
	<br class="clear" style="margin-bottom: 50px;"/>

	<?php foreach ($photos[$show_category] as $p): ?>
		<div id="o_<?php echo $p->number; ?>" class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
		</div>
		<p class="title"><?php echo $p->title; ?></p>
		<div class="pannel">
			<a class="reportPhoto" onclick="javascript:showReportPhoto('<?php echo $p->photo_id; ?>', 'o_<?php echo $p->number; ?>');" title="Signaler un problème">Signaler / poser une question</a>
			<br class="clear"/>	
		</div>
		</div>
	<?php endforeach; ?>

	<br class="clear" style="margin-bottom: 50px;"/>






	<!-- Le formulaire pour signaler un problème sur une photo -->
	<div id="ReportPhoto">
		<p><img src="" alt="aperçu"/><br/>
		<input type="text" name="report" id="report" maxlength="100" tabindex="1" class="inputbox"/><br/>
		<a class="buttonYes" onclick="javascript:reportPhoto('yes');">Signaler</a> <a class="buttonNo" onclick="javascript:reportPhoto('no');">Annuler</a>
		</p>
	</div>

	<!-- La page d'attente -->
	<div id="MessageDialog" title="&nbsp;">
	</div>

	<!-- iframe poubelle pour les redirections ajax des forms -->
	<iframe id="hiddeniframe" name="hiddeniframe" style="display:none;" src="about:blank"></iframe>
<?php endif; ?>














	


