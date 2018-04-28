<h1>Edition <?php echo $current_phase_year; ?> des AGPA</h1>

<div id="incipit">
	<div class="illustration phase4">&nbsp;</div>
	<h2 style="color:#178FFF">Phase 4 : <span>Dépouillement</span></h2> 
	<div id="frise">
		<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
	</div>
	<dl>
		<dt>Période</dt>
		<dd> Du 21 décembre <?php echo $current_phase_year; ?> au 24 décembre <?php echo $current_phase_year; ?>.<br/>
		    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 5.
		</dd>
		<?php if (!$show_category): ?>
		<dt>Description : </dt>
	<dd>Les votes enregistrés par les jurés vont être vérifiés, puis les notes des photos vont être calculées afin de sélectionner les meilleurs de chaque catégorie.<br/>
	C'est aussi durant cette période qu'est préparé la cérémonie de AGPA où seront remis les récompenses aux auteurs des plus belles photos.
	</dd>

	<dt style="color: #f55">Réglement</dt>
	<dd>Au sein de chaque catégorie, les photos sont classées en fonction de leur nombre total de points (classement décroissant).<br/>
	En cas d'égalité, le nombre total de votes reçus fait la différence (classement décroissant), ainsi que la présence ou non d'un titre.<br/>
	<i>Pour connaître tout les détails sur les calculs effectués pour départager les photos et pour attribuer les AGPA d'or du meilleur photographe et de la meilleur photographie, merci de vous référer <a href="{ROOT_PATH}agpa.php?section=rules&amp;sid={_SID}#chap5.2" title="Lire le réglement en ligne">au réglement</a> </i>. 
	</dd>
	<?php endif; ?>
	</dl>
	<br class="clear"/>
</div>




<?php if ($is_admin): ?>
	<a class="computesScores" href="<?php echo base_url(); ?>agpa/current/computesScores/<?php echo $computesScores_nextStep; ?>">[ADMIN] Procéder au dépouillement des votes (étape <?php echo $computesScores_nextStep; ?> / 5)</a>
<?php endif; ?>


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
		<h2 id="category_<?php echo $cat->category_id;  ?>" style="color: <?php echo $cat->color; ?>;"><?php echo $cat->title; ?></h2>
		<p class="details">
			<span id="title_details"><?php echo $cat->feather; ?> / 10 <?php if ($cat->feather >= 4 && $cat->feather <= 10): ?><img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-yes.png'; ?>"/><?php else: ?> <img src="<?php echo base_url() . 'assets/theme/agpa/img/icon-no.png'; ?>"/><?php endif; ?></span>
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


<?php else: ?>

	<div id="voteResumeSlot">
		<div id="voteResumePanel" class="category">
			<h2 id="category_<?php echo $show_category; if ($show_category==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $categories[$show_category]->color; ?>;"><?php echo $categories[$show_category]->title; ?></h2>
			<p class="details">
				<span class="photos"><?php echo $categories[$show_category]->nbr_photo; ?></span>
				<span class="authors"><?php echo count($categories[$show_category]->authors); ?></span>
				<?php if ($show_category != -3): ?>
				<span class="stars"><b id="starUsedCounter" class="<?php if ($categories[$show_category]->star_used >=$categories[$show_category]->star_available / 2) echo 'good'; else echo 'bad'; ?>"><?php echo $categories[$show_category]->star_used . "</b> / " . $categories[$show_category]->star_available; ?></span>
				<?php endif; ?>
				<span class="feather"><b id="featherCounter" class="<?php if ($categories[-3]->feather >= 4 && $categories[-3]->feather <= 10) echo 'good'; else echo 'bad'; ?>"><?php echo $categories[-3]->feather; ?></b> / 10 </span>
			</p>
		</div>
	</div>
	<br class="clear" style="margin-bottom: 50px;"/>

	<?php $pUser=array(); foreach ($photos[$show_category] as $p): if ($p->user_id != $user->user_id): ?>
	<div class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
			<div class="vote<?php if (isset($p->user_vote)) echo $p->user_vote; else echo '0'; ?>" >&nbsp;</div>
		</div>
		<p class="title"><?php echo $p->title; ?></p>

			

	</div>
	<?php else: $pUser[] = $p; endif; endforeach; ?>

	<br class="clear" style="margin-bottom: 50px;"/>

<?php if ($show_category != -3): ?>
	<h2>Vos Photos :</h2>

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














	


