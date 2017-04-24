


	<div class="stats">
		<div class="col1">
			<h3>Quelques chiffres : </h3>
			<p><span class="label">Images enregistrées :</span> <span class="value"><?php echo $stats['maxImmt']; ?></span></p>
			<p><span class="label">Nombre de posteurs différents :</span> <span class="value"><?php echo $stats['maxCommiters']; ?></span></p>
			<p><span class="label">En moyenne, une photo tout les :</span> <span class="value"><?php echo $stats['average'] . " jours"; ?></span></p>
		</div>
		<div class="col2">
			<h3>Posteurs prolifiques :</h3>
			<div id="graphic"></div>
			<div class="legend">
				<div class="legendBox" style="background-color:#876;"></div><p><span class="label"><?php echo $stats['commiters'][0]['name']; ?> :</span> <span class="value"><?php echo $stats['commiters'][0]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#a98;"></div><p><span class="label"><?php echo $stats['commiters'][1]['name']; ?> :</span> <span class="value"><?php echo $stats['commiters'][1]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#cba;"></div><p><span class="label"><?php echo $stats['commiters'][2]['name']; ?> :</span> <span class="value"><?php echo $stats['commiters'][2]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#edc;"></div><p><span class="label"><?php echo $stats['commiters'][3]['name']; ?> :</span> <span class="value"><?php echo $stats['commiters'][3]['value']; ?></span></p>
			</div>
		</div>
		<a class="col3" href="<?php echo $stats['userRankUrl']; ?>">
			<h3>Rang G : </h3>
			<img src="<?php echo $stats['rank']['src']; ?>" title="<?php echo $stats['rank']['name']; ?>" alt="<?php echo $stats['rank']['name']; ?>"/>
			<p><span class="label" style="width: 100px;">N°<?php echo $stats['rank']['number']; ?> :</span>  <span class="value"><?php echo $stats['rank']['name']; ?></span><br/>
				<span class="label" style="width: 100px;">Images : </span>  <span class="value"><?php echo $stats['rank']['nbrImmt']; ?> (<?php echo $stats['rank']['noteg']; ?> G)</span><br/>
				<span class="label" style="width: 100px;">Prochain pallier : </span> <span class="value"><?php echo $stats['rank']['nextStep']; ?> (<?php echo $stats['rank']['nextReward']; ?>)</span></p>
			<br style="clear:left"/>
			<div id="progressChart">
				<span id="boundMin"><?php echo $stats['rank']['boundMin']; ?> G</span>
				<div id="progressBar">
					<div id="progression" style="<?php echo $stats['rank']['progression']; ?>"></div>
					<span id="progressionValue"><?php echo $stats['rank']['progressionValue']; ?>%</span>
				</div>
				<span id="boundMax"><?php echo $stats['rank']['boundMax']; ?> G</span>
			</div>
		</a>
	</div>

	<!-- Display pie chart -->
	<script type="text/javascript">
    // <![CDATA[
        <?php echo $stats['jqPlotData']; ?>

        $(document).ready(function()
        { 
        	$.jqplot('graphic', [graphData], 
	        {
	        	seriesColors: [ "#876", "#a98", "#cba", "#edc"],
		        grid: 
		        {
		            drawBorder: false, 
		            drawGridlines: false,
		            background: 'transparent',
		            shadow:false
		        },
		        axesDefaults: { },
		        seriesDefaults:
		        {
		        	shadow: false,
		            renderer:$.jqplot.PieRenderer,
		            rendererOptions: { showDataLabels: true, padding:0}
		        },
		        legend:{show:false}      
		    });   
		});
    // ]]>
    </script>