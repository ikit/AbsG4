<h1>Edition <?php echo $current_phase_year; ?> des AGPA</h1>

<div id="incipit">
	<div class="illustration phase5">&nbsp;</div>
	<h2 style="color:#178FFF">Phase 5 : <span>Cérémonie</span></h2> 
	<div id="frise">
		<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
	</div>
	<dl>
		<dt>Période</dt>
		<dd>A partir du 25 décembre <?php echo $current_phase_year; ?>.</dd>
		<dt>Description : </dt>
		<dd>Ca y est, la cérémonie officielle a eu lieu, les résultats du concours sont désormais publiques. Bravo à tous et à l'année prochaine !!
		</dd>
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
		</p>
		<div class="minislideshow">  
			<img id="minislideshow<?php echo $cat->category_id; ?>" src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . array_shift(array_values($photos[$cat->category_id]))->filename; ?>" />
			<div id="minislideshow<?php echo $cat->category_id; ?>Vote" class="vote0"> &nbsp;</div>
        </div>

		<br class="clear" />
	</a>

	<?php elseif ($cat->category_id < 0): ?>
	<a class="category" href="<?php echo base_url(); ?>agpa/current/<?php echo $cat->category_id; ?>">
		<h2 id="category_<?php echo $cat->category_id; if ($cat->category_id==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $cat->color; ?>;"><?php echo $cat->title; ?></h2>
		<p class="details">
			<span class="photos"><?php echo $cat->nbr_photo; ?></span>
			<span class="authors"><?php echo count($cat->authors); ?></span>
		</p>
		<?php if ($cat->category_id < -1) : ?>

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
			<h2 id="category_<?php echo $show_category; if ($cat->category_id==5 && $current_phase_year > 2013) echo "2"; ?>" style="color: <?php echo $categories[$show_category]->color; ?>;"><?php echo $categories[$show_category]->title; ?></h2>
			<p class="details">
				<span class="photos"><?php echo $categories[$show_category]->nbr_photo; ?></span>
				<span class="authors"><?php echo count($categories[$show_category]->authors); ?></span>
			</p>
		</div>
	</div>
	<br class="clear" style="margin-bottom: 50px;"/>

	<?php foreach ($photos[$show_category] as $p): ?>
	<div class="oeuvre">
		<div class="photo">
			<a href="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/' . $p->filename; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $p->title; ?>">
			<img src="<?php echo base_url() . 'assets/img/agpa/' . $current_phase_year . '/mini/vignette_' . $p->filename; ?>" /></a>
			<?php if (count($p->award) > 0) : ?>
			<div class="awards">
				<?php foreach ($p->award as $award_cat => $award): if ($award != 'lice'): ?>
				<?php if ($award_cat == 8): ?>
				<img src="<?php echo base_url() . 'assets/theme/agpa/img/cupes/' . $current_phase_year . '/c' . $award_cat . '-' . $award . '.png'; ?>" class="award"/>&nbsp;
				<?php else: ?>
				<img src="<?php echo base_url() . 'assets/theme/agpa/img/cupes/c' . $award_cat . '-' . $award . '.png'; ?>" class="award"/>&nbsp;
				<?php endif; ?>
				<?php endif; endforeach; ?>
			</div>
			<?php endif;?>
		</div>
		<p class="title"><?php echo $p->title; ?><br/>(<?php echo $p->username; ?>)</p>

			

	</div>
	<?php endforeach; ?>

	<br class="clear" style="margin-bottom: 50px;"/>




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














	


