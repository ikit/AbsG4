
	<h1 class="immt">Les images du moment</h1>

	<div id="immtControlsSlot">
		<div id="immtControlsBar">
			<div class="filterArea">
				<a class="newImmt" href="javascript:displayNewForm();" title="Enregistrer une nouvelle photo du moment">Nouvelle image</a>
			</div>
			<div class="paginationArea">
				Page : <a class="prev" href="javascript:goToPage('p');">&nbsp;</a>  
				<input type="number" onchange="updatePage(this);" value="<?php echo ($currentPage+1); ?>" name="pageNumberTop" min="1" max="<?php echo $totalPage; ?>" /> / <?php echo $totalPage; ?> 
				<a class="next" href="javascript:goToPage('n');">&nbsp;</a>
			</div>
		</div>
	</div>
	<br class="clear"/>

	<div id="newImmt" class="hidden">
		<div class="illustration"></div>
		<form action="<?php echo $newFormAction; ?>" method="post" enctype="multipart/form-data">
		    <input type="submit" value="Enregistrer" />
			<label for="newImage">Votre image :</label>
			<input type="file" id="newImage" name="newImage" placeholder="Sélectionner votre image"/>

			<label for="newTitle">Le titre :</label>
			<input type="text" id="newTitle" name="newTitle" placeholder="Le titre de l'image"/>

		</form>
	</div>

	<ul class="immts">
	<?php
		$i = 0;
		foreach($immt as $img) 
		{
			// alternance de style pour une ligne sur deux pour la lisibilité
			$rowStyle = "row" . ($i % 2); 

			// Test si il faut débuter une nouvelle ligne
			if ($i%5 == 0)
			{
				echo "<li class=\"{$rowStyle}\">";
				
			}
			
			$fileName = $img->year . '_' . $img->day . '.jpg';
			$imgBig = base_url() . 'assets/img/immt/' . $fileName;
			$imgSmall = base_url() . 'assets/img/immt/mini/' . $fileName;

			echo "<div class=\"immt\"><p class=\"image\"><a href=\"{$imgBig}\" rel=\"lightbox[galeriP{$currentPage}]\" title=\"{$img->title}\"><img src=\"{$imgSmall}\" /></a></p><p class=\"title\">{$img->title}<br/><span class=\"date\">{$img->dateLabel}</span></p></div>";

			// Test si il faut terminer la ligne en cours
			if ($i%5 == 4)
			{
				echo "</li>\n\t\t\t\t\t\t";
				
			}
			++$i;	
		}

 	?>
 	</ul>
	

	<script type="text/javascript">
	// <![CDATA[
		// Init variables
		var currentPage = <?php echo ($currentPage); ?>;
		var totalPage = <?php echo $totalPage; ?>;
	    $(document).ready(function()
	    {
	        immtInit();
	    });
	// ]]>
	</script>