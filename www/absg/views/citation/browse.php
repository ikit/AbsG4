
	<h1 class="citation">Les citations cultes <?php echo $fromAuthor; ?></h1>

	<div id="citationControlsSlot">
		<div id="citationControlsBar">
			<div class="filterArea">
				<a class="newCitation" href="javascript:displayNewForm();" title="Enregistrer une nouvelle citation">Nouvelle citation</a>

				Filtre : 
				<select style="margin-left: 10px;" onchange="updateFilter(this);">
					<?php
					$select = ($currentAuthor == "-1") ? ' selected' : '';
					echo "<option value=\"-1\"{$select}>Tout le monde</option>\n\t\t\t\t\t";
						// Création de la liste des auteurs possible
						foreach($authors as $author)
						{
							$select = ($currentAuthor == $author[0]) ? ' selected' : '';
							echo "<option value=\"{$author[0]}\"{$select}>{$author[1]}</option>\n\t\t\t\t\t";
						}
					?>
				</select>
				 ( <?php echo $totalCitations; ?> citations )
				
			</div>
			<div class="paginationArea">
				Page : <a class="prev" href="javascript:goToPage('p', '<?php echo  $currentAuthor; ?>');">&nbsp;</a>  
				<input type="number" onchange="updatePage(this, '<?php echo  $currentAuthor; ?>');" value="<?php echo ($currentPage+1); ?>" name="pageNumberTop" min="1" max="<?php echo $totalPage; ?>" /> / <?php echo $totalPage; ?> 
				<a class="next" href="javascript:goToPage('n', '<?php echo  $currentAuthor; ?>');">&nbsp;</a>
			</div>
		</div>
	</div>
	<br class="clear"/>

	<div id="newCitation" class="hidden">
		<div class="illustration"></div>
		<form action="<?php echo $newFormAction; ?>" method="post" enctype="multipart/form-data">
		    <input type="submit" value="Enregistrer" />

			<label for="newCit">La citation :</label>
			<p>N'oubliez pas les guillemets et pensez aux doubles parenthèses pour les précisions : "ma citation" ((ma précision))</p>
			<input type="text" id="newCit" name="newCit" placeholder="La citation"/>

			<label for="newAuthor">L'auteur :</label>
			<p>Veuillez bien utiliser les mêmes noms déjà utilisé pour les auteurs (majuscules et accents), sinon cela sera considéré <br/>comme une autre personne.</p>
			<input type="text" id="newAuthor" name="newAuthor" placeholder="L'auteur de la citation"/>
		</form>
	</div>



	
	<?php
		$i = 0;
		foreach($citations as $citation) 
		{
			// alternance de style pour une ligne sur deux pour la lisibilité
			$rowStyle = "row" . ($i % 2); ++$i;
			
			$cit = $citation->citation;
			$aut = (!empty($citation->surname)) ? $citation->surname : $citation->firstname;
			$cid = 0; //$citation->id;
			$first = ($i == 1) ? " id=\"citation1\"" : "";
			echo "<div{$first} class=\"citation {$rowStyle}\"><p>";
			echo "{$cit}</p> <p class=\"author\">{$aut}</p></div>\n\t";
		}
 	?>


	<script type="text/javascript">
	// <![CDATA[
		// Init variables
		var currentPage = <?php echo ($currentPage); ?>;
		var totalPage = <?php echo $totalPage - 1; ?>;
	    $(document).ready(function()
	    {
	        citationInit();
	    });
	// ]]>
	</script>