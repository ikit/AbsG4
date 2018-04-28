
<h1>Archives : <?php echo $mainTitle; ?></h1>



<?php if ($filterLevel == 1) : ?>




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



	
<?php else : ?>




<?php endif; ?>

