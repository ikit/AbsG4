
<script type="text/javascript">
// <![CDATA[
	var form_name = 'postform';
	var text_name = 'message';
// ]]>
</script>

<div class="panel bg2" style="font-size: 1.2em">
	<div class="inner">
		<form id="smile_selector" method="post" action="<?php echo base_url() . "forum/browseSmilies"; ?>" style="text-align: center;">
			<select name="listRubriques" onchange="switchRub(this.form)">
			<?php echo $rubrique_selector; ?>
			</select>
			&nbsp; &rarr; &nbsp;
			<select name="listSections">
			<?php echo $section_selector; ?>
			</select>
			<input class="button1" type="submit" id="afficher" name="afficher" value="Afficher les smilies" />
		</form>
	</div>
</div>
<br/>
<div class="panel">
	<div style="line-height: 40px; text-align: center;">
		<?php
			$c = 0;
			for ($s = 0; $s < sizeof($smilies); $s++)
			{

			    // Passer à la ligne suivante :
			    if ($c%5 == 0)
			    {
			    	echo '</div><div style="line-height: 40px; margin-top: 5px; text-align: center;">';
			    } 

			    // Afficher le smiley
			    $imgUrl = $smileyBaseUrl . $smilies[$s];
			    echo "<a href=\"#\" onclick=\"insert_smiley('$imgUrl'); \"><img src=\"$imgUrl\" alt=\"\" /></a> &nbsp;&nbsp;&nbsp;";
			    
			    ++$c;
			}
		?>
	</div>
</div>