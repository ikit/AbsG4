


	<div class="stats">
		<div class="col1">
			<h3>Quelques chiffres : </h3>
			<p><span class="label">Nombre de sujets :</span> <span class="value"><?php echo $stats['maxTopics']; ?></span></p>
			<p><span class="label">Nombre de messages :</span> <span class="value"><?php echo $stats['maxPosts']; ?></span></p>

			<p><span class="label">Records</span> <span class="value"><?php echo $stats['maxCommiters']; ?></span></p>
			<p><span class="label"> - Sujet :</span> <span class="value"><?php echo $stats['maxCommiters']; ?></span></p>
			<p><span class="label"> - P :</span> <span class="value"><?php echo $stats['maxCommiters']; ?></span></p>
		</div>
		<div class="col2">
			<h3>Répartition des messages :</h3>
			<div id="graphic"></div>
			<div class="legend">
				<div class="legendBox" style="background-color:#357;"></div><p><span class="label"><?php echo $stats['posts'][0]['name']; ?> :</span> <span class="value"><?php echo $stats['posts'][0]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#579;"></div><p><span class="label"><?php echo $stats['posts'][1]['name']; ?> :</span> <span class="value"><?php echo $stats['posts'][1]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#79b;"></div><p><span class="label"><?php echo $stats['posts'][2]['name']; ?> :</span> <span class="value"><?php echo $stats['posts'][2]['value']; ?></span></p>
				<div class="legendBox" style="background-color:#9bd;"></div><p><span class="label"><?php echo $stats['posts'][3]['name']; ?> :</span> <span class="value"><?php echo $stats['posts'][3]['value']; ?></span></p>
			</div>
		</div>
		<div class="col3">
			<h3>Rang G : </h3>
			<img src="<?php echo $stats['rank']['src']; ?>" title="<?php echo $stats['rank']['name']; ?>" alt="<?php echo $stats['rank']['name']; ?>"/>
			<p>N°<?php echo $stats['rank']['number']; ?>  - <?php echo $stats['rank']['name']; ?><br/>
				<span class="label" style="width: 100px;">Messages : </span>  <span class="value"><?php echo $stats['rank']['nbrPosts']; ?> (<?php echo $stats['rank']['noteg']; ?> G)</span><br/>
				<span class="label" style="width: 100px;">Prochain pallier : </span> <span class="value"><?php echo $stats['rank']['nextStep']; ?> (<?php echo $stats['rank']['nextReward']; ?>)</span></p>
			<br style="clear:left"/>
			<div id="progressChart">
				<span id="boundMin"><?php echo $stats['rank']['boundMin']; ?></span>
				<div id="progressBar">
					<div id="progression" style="<?php echo $stats['rank']['progression']; ?>"></div>
					<span id="progressionValue"><?php echo $stats['rank']['progressionValue']; ?>%</span>
				</div>
				<span id="boundMax"><?php echo $stats['rank']['boundMax']; ?></span>
			</div>
		</div>
	</div>

	<!-- Display pie chart -->
	<script type="text/javascript">
    // <![CDATA[
        <?php echo $stats['jqPlotData']; ?>

        $(document).ready(function()
        { 
        	$.jqplot('graphic', [graphData], 
	        {
	        	seriesColors: [ "#357", "#579", "#79b", "#9bd"],
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