
<h1>Edition <?php echo $current_phase_year; ?> des AGPA</h1>

<div id="incipit">
	<div class="illustration phase3">&nbsp;</div>
	<h2 style="color:#178FFF">Phase 3 : <span>Votes</span></h2> 
	<div id="frise">
		<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
	</div>
	<dl>
		<dt>Période</dt>
		<dd> Du 17 décembre <?php echo $current_phase_year; ?> au 21 décembre <?php echo $current_phase_year; ?>.<br/>
		    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 4.
		</dd>
		<?php if (!$show_category): ?>
		<dt>Description : </dt>
		<dd>Vous pouvez maintenant voter pour vos photos préférées. Pour cela, vous devez attribuer des étoiles (dont le nombre est limité) aux photos que vous préférez : <ul>
			<li>+1 étoile aux photos que vous aimez bien;</li>
			<li>+2 étoiles aux photos qui, selon vous, méritent un AGPA;</li>
		</ul>
		L'AGPA d'or sera décerné à la photo ayant reçu le <b>plus</b> d'étoiles'.<br/>
		Vous devez également sélectionner <?php echo ceil($max_feather/2.0) . " à $max_feather"; ?> photos dont le titre mérite un AGPA.</dd>

		
		<?php else: ?>
		<dt style="color: #f55">Réglement</dt>
		<dd> <ul style="margin-left:-50px;"><li>[...] Voter pour les meilleures photos en ajoutant une ou deux étoiles supplémentaires à ses photos préférées, dans la limite d’un nombre d’étoiles égal à la moitié du nombre de photos de la catégorie.</li>
			 <li>[...] Voter pour les meilleurs titres, toutes catégories confondues, en sélectionnant quatre à huit titres au choix parmi l’ensemble des photos (sans limite par catégorie).</li>
			 <li>[...] Les candidats doivent attribuer au moins la moitié de leurs étoiles. S'ils ne le font pas, pour les catégories concernées, leurs votes partiels ne seront pas pris en compte. </li>
			 <li>[...] Toutes les photos qui n'ont pas été refusées lors de la phase précédente de vérification, sont considérées comme valables.</li> 
			 <li>[...] Les candidats ne peuvent pas voter pour leurs propres œuvres et les votes sont anonymes. </li>
			 <li><i>Pour connaître tout les détails, référez vous au <a href="<?php echo base_url() . 'agpa/rules'; ?>" title="Lire le réglement en ligne">réglement</a>. </i></li>
		</dd>
		<?php endif; ?>
	</dl>
	<br class="clear"/>
</div>







<?php if (!$show_category): ?>


<?php  foreach ($categories as $cat): ?>
	
	<?php if ($cat->category_id > 0): ?>
	<a class="category" href="<?php echo base_url(); ?>agpa/current/<?php echo $cat->category_id; ?>">
		<h2 id="category_<?php echo $cat->category_id; if ($cat->category_id==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $cat->color; ?>;"><?php echo $cat->title; ?></h2>
		<p class="details">
			<span class="photos"><?php echo $cat->nbr_photo; ?></span>
			<span class="authors"><?php echo count($cat->authors); ?></span>
			<span class="stars"><?php echo $cat->star_used . " / " . $cat->star_available; ?> <?php if ($cat->star_ok): ?><img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-yes.png'; ?>"/><?php else: ?> <img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-no.png'; ?>"/><?php endif; ?></span>
		</p>
		<div class="minislideshow">  
			<img id="minislideshow<?php echo $cat->category_id; ?>" src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . array_shift(array_values($photos[$cat->category_id]))->filename; ?>" />
			<div id="minislideshow<?php echo $cat->category_id; ?>Vote" class="vote0"> &nbsp;</div>
        </div>

		<br class="clear" />
	</a>

	<?php elseif ($cat->category_id == -3): ?>
	<a class="category" href="<?php echo base_url(); ?>agpa/current/<?php echo $cat->category_id; ?>">
		<h2 id="category_<?php echo $cat->category_id; ?>" style="color: <?php echo $cat->color; ?>;"><?php echo $cat->title; ?></h2>
		<p class="details">
			<span id="title_details"><?php echo $cat->feather; ?> / <?php echo $max_feather; if ($cat->feather >= $max_feather/2 && $cat->feather <= $max_feather): ?> <img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-yes.png'; ?>"/><?php else: ?> <img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-no.png'; ?>"/><?php endif; ?></span>
		</p>
		<?php if (count($photos[-3]) > 0): ?>
		<div class="minislideshow">  
			<img id="minislideshow-3" src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . array_shift(array_values($photos[-3]))->filename; ?>" />
        </div>
        <?php endif; ?>
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
				$i=0; $filearray = '';
				foreach ($photos[$cat->category_id] as $p)
				{
					$filearray .= '{ "filename": "' . $p->filename . '", "vote": "';
					if (isset($p->user_vote)) $filearray .= $p->user_vote; else $filearray .= '0';
					$filearray .= '"}'; 

					++$i; 
					$filearray .= ($i%count($photos[$cat->category_id]) != 0) ? ',' : ''; 
				}
				echo $filearray; ?>);
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
            		var photo = cat_<?php echo $cat->category_id; ?>[imgIdx % cat_<?php echo $cat->category_id; ?>.length];
		            $("#minislideshow<?php echo $cat->category_id; ?>").attr("src", root_path + photo.filename);
		            $("#minislideshow<?php echo $cat->category_id; ?>Vote").attr("class", 'vote' + photo.vote);
		        })
	        	.fadeIn(200);
                <?php endif; endforeach; ?>
			}, 5000);
        });
    // ]]>
    </script>

<?php else:  ?>

	<div id="voteResumeSlot">
		<div id="voteResumePanel" class="category">
			<h2 id="category_<?php echo $show_category; if ($show_category==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $categories[$show_category]->color; ?>;"><?php echo $categories[$show_category]->title; ?></h2>
			<p class="details">
				<span class="photos"><?php echo $categories[$show_category]->nbr_photo; ?></span>
				<span class="authors"><?php echo count($categories[$show_category]->authors); ?></span>
				<?php if ($show_category != -3): ?>
				<span class="stars"><b id="starUsedCounter" class="<?php if ($categories[$show_category]->star_ok) echo 'good'; else echo 'bad'; ?>"><?php echo $categories[$show_category]->star_used . "</b> / " . $categories[$show_category]->star_available; ?></span>
				<?php endif; ?>
				<span class="feather"><b id="featherCounter" class="<?php if ($categories[-3]->feather_ok) echo 'good'; else echo 'bad'; ?>"><?php echo $categories[-3]->feather; ?></b> / <?php echo $max_feather; ?></span>
			</p>
		</div>
	</div>
	<br class="clear" style="margin-bottom: 50px;"/>

	<?php $pUser=array(); foreach ($photos[$show_category] as $p): if ($p->user_id != $user->user_id && $p->error === null): ?>
	<div class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
		</div>
		<p class="title"><?php echo $p->title; ?></p>
		<div class="pannel">
			<span class="voteStar selected">&nbsp;</span>
			<?php if ($show_category != -3): ?>
			<a id="v1_<?php echo $p->number; ?>" class="voteStar <?php if (isset($p->user_vote)) echo 'selected'; ?>" onclick="javascript:votePhoto(1, '<?php echo $p->photo_id; ?>', '<?php echo $p->number; ?>');" title="Ajouter une étoile">&nbsp;</a>
			<a id="v2_<?php echo $p->number; ?>" class="voteStar <?php if (isset($p->user_vote) && $p->user_vote > 1) echo 'selected'; ?>" onclick="javascript:votePhoto(2, '<?php echo $p->photo_id; ?>', '<?php echo $p->number; ?>');" title="Ajouter deux étoiles">&nbsp;</a>
			<?php else: ?>
			<span class="voteStar <?php if (isset($p->user_vote)) echo 'selected'; ?>" >&nbsp;</span>
			<span class="voteStar <?php if (isset($p->user_vote) && $p->user_vote > 1) echo 'selected'; ?>">&nbsp;</span>
			<?php endif; ?>
			<a id="vt_<?php echo $p->number; ?>" class="voteFeather <?php if (isset($p->title_selection) || $show_category == -3) echo 'selected'; ?>" onclick="javascript:votePhoto(0, '<?php echo $p->photo_id; ?>', '<?php echo $p->number; ?>');" title="Sélectionner pour les meilleurs titres">&nbsp;</a>
			<br class="clear"/>	
		</div>
	</div>
	<?php else: $pUser[] = $p; endif; endforeach; ?>

	<br class="clear" style="margin-bottom: 50px;"/>

<?php if ($show_category != -3): ?>
	<h2>Les photos pour lesquelles vous ne pouvez pas voter :</h2>

	<?php foreach ($pUser as $p):?>
	<div id="o_<?php echo $p->number; ?>" class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
		</div>
		<p class="title"><?php echo $p->title; ?></p>
	</div>
	<?php endforeach; ?>
	<br class="clear" style="margin-bottom: 50px;"/>
<?php endif; ?>




	<!-- La page d'attente -->
	<div id="MessageDialog" title="&nbsp;">
	</div>

	<!-- iframe poubelle pour les redirections ajax des forms -->
	<iframe id="hiddeniframe" name="hiddeniframe" style="display:none;" src="about:blank"></iframe>


	<script type="text/javascript">
    // <![CDATA[

        $(document).ready(function()
        {
            agpaPhase3Init(<?php echo $categories[$show_category]->star_used . ', ' . $categories[$show_category]->star_available . ', ' . $categories[-3]->feather; ?>);

        });
    // ]]>
    </script>
<?php endif; ?>














	


