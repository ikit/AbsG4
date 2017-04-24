
<h1>Palmares <?php echo $palmaresUserData['forYear'] . " " . $palmaresUserData['fromUser']; ?></h1>

<div id="resumePalmares">
    <div class="avatar">
	<img src="<?php echo $palmaresUserData['avatar']; ?>" alt="<?php echo $palmaresUserData['username']; ?>" /><br/>
	<span class="username"><?php echo $palmaresUserData['username']; ?></span><br/>
	<span class="period"><?php if ($filterYear === 0) echo "De 2006 à "; echo $maxYear; ?></span><br/>
	<span class="scoreTotal"><?php echo $resumeTotal . " <span>pt"; ?><?php if ($resumeTotal > 1) echo "s"; ?></span></span>
    </div>
    <table>
	
	<tr><th colspan="6">&nbsp;</th><th colspan="2" class="totalHHead">Total</th></tr>
	<tr><th>&nbsp;</th><th class="hColumn">Nominé</th><th class="hColumn">Bronze</th><th class="hColumn">Argent</th><th class="hColumn">Or</th><th class="hColumn">Diamant</th><th class="totalHead left hColumn">AGPA</th><th class="totalHead right hColumn">Points</th></tr>
	<?php foreach ($resume as $cat_id => $cat): ?>
	<tr style="color:<?php echo $categories[$cat_id]->color; ?>">
	    <th><?php echo $categories[$cat_id]->title; ?></th>
	    <td class="left<?php if ($cat_id == -1) echo " bottom"; ?>"><?php echo ((isset($cat['lice'])) ? count($cat['lice']) : "<span class=\"zero\">0</span>"); ?></td>
	    <td<?php if ($cat_id == -1) echo " class=\"bottom\""; ?>><?php echo ((isset($cat['bronze'])) ? count($cat['bronze']) : "<span class=\"zero\">0</span>"); ?></td>
	    <td<?php if ($cat_id == -1) echo " class=\"bottom\""; ?>><?php echo ((isset($cat['argent'])) ? count($cat['argent']) : "<span class=\"zero\">0</span>"); ?></td>
	    <td<?php if ($cat_id == -1) echo " class=\"bottom\""; ?>><?php echo ((isset($cat['or'])) ? count($cat['or']) : "<span class=\"zero\">0</span>"); ?></td>
	    <td<?php if ($cat_id == -1) echo " class=\"bottom\""; ?>><?php echo ((isset($cat['diamant'])) ? count($cat['diamant']) : "<span class=\"zero\">0</span>"); ?></td>
	    <td class="totalHead left<?php if ($cat_id == -1) echo " bottom"; ?>"><?php echo (($cat['totalAgpa'] > 0) ? $cat['totalAgpa'] : "<span class=\"zero\">0</span>"); ?></td>
	    <td class="totalHead right<?php if ($cat_id == -1) echo " bottom"; ?>"><?php echo (($cat['totalPoints'] > 0) ? $cat['totalPoints'] : "<span class=\"zero\">0</span>"); ?></td></tr>
	<?php endforeach; ?>
    </table>
    <br class="clear"/>
</div>


<div class="palmaresNavbar">
<form method="post" action="<?php echo site_url('agpa/switchPalmares'); ?>">
    <select id="feature" name="feature">
	<?php foreach($filterMenus['features'] as $key => $value): ?>
	<option value="<?php echo $key; ?>"<?php if ($filterMenus['select']['features'] === $key) echo " selected";?>><?php echo $value; ?></option>
	<?php endforeach; ?>
    </select>
    
    <label for="userFilter">Personnes : </label>
    <select id="userFilter" name="userFilter">
	<option class="rootfamilly" value="gueudelot"<?php if ($filterMenus['select']['userFilter'] == 'gueudelot') echo " selected";?>><b>Gueudelot</b></option>
	<?php foreach($filterMenus['userFilter']['gueudelot'] as $key => $value): ?>
	<option value="<?php echo $key; ?>"<?php if ($filterMenus['select']['userFilter'] == $key) echo " selected";?>><?php echo $value; ?></option>
	<?php endforeach; ?>
	
	<option class="rootfamilly" value="guibert"<?php if ($filterMenus['select']['userFilter'] == 'guibert') echo " selected";?>><b>Guibert</b></option>
	<?php foreach($filterMenus['userFilter']['guibert'] as $key => $value): ?>
	<option value="<?php echo $key; ?>"<?php if ($filterMenus['select']['userFilter'] == $key) echo " selected";?>><?php echo $value; ?></option>
	<?php endforeach; ?>
	
	<option class="rootfamilly" value="guyomard"<?php if ($filterMenus['select']['userFilter'] == 'guyomard') echo " selected";?>><b>Guyomard</b></option>
	<?php foreach($filterMenus['userFilter']['guyomard'] as $key => $value): ?>
	<option value="<?php echo $key; ?>"<?php if ($filterMenus['select']['userFilter'] == $key) echo " selected";?>><?php echo $value; ?></option>
	<?php endforeach; ?>
    </select>
    
    <label>Années : </label>
    <select id="yearFilter" name="yearFilter">
	<?php foreach($filterMenus['yearFilter'] as $key => $value): ?>
	<option value="<?php echo $key; ?>"<?php if ($filterMenus['select']['yearFilter'] == $key) echo " selected";?>><?php echo $value; ?></option>
	<?php endforeach; ?>
    </select>
    <input type="submit" value="Valider" />
</form>
</div>



<?php foreach ($categories as $cat_id => $cat): ?>
	
	<?php if ($resume[$cat_id]['totalAgpa'] > 0): ?>
	<h2 id="category_<?php echo $cat->category_id; ?>" style="color: <?php echo $cat->color; ?>;">
	    <span class="title"><?php echo $cat->title; ?></span>
	    <span class="score"><?php echo $resume[$cat_id]['totalPoints']; ?> <span class="scorePts">pts</span></span>
	</h2>

	<div class="coverflow">
	<?php 
	    $awardType = array("bronze", "argent", "or", "diamant");
	    foreach($awardType as $award):
	    if (isset($resume[$cat_id][$award])) : 
		foreach ($resume[$cat_id][$award] as $catKey => $catData):  ?>
		
		<?php if ($cat_id != -1): ?>
		
		<div class="oeuvre">
			<div class="photo">
				<a href="<?php echo base_url() . 'assets/img/agpa/' . $catData['year'] . '/mini/' . $catData['filename']; ?>" data-lightbox="lightbox[agpa]" title="<?php echo $catData['title']; ?>">
				<img src="<?php echo base_url() . 'assets/img/agpa/' . $catData['year'] . '/mini/vignette_' . $catData['filename']; ?>" />
				</a>
			</div>
			<p class="title"><?php echo $catData['year'];  if ($palmaresUserData['displayAuthor'])  echo "<br/>( {$catData['username']} )"; ?></p>
			<img class="award" src="<?php echo base_url() . 'assets/theme/agpa/img/cupes/'; if ($cat_id == 8) echo $catData['year'] . '/' ; echo "c$cat_id-$award.png"; ?>" title="Agpa <?php echo $award;?>" />
		</div>
		
		<?php else : ?>
		<div class="bestAuthor">
			<?php if ($palmaresUserData['displayAuthor']) : ?>
			<img class="author" src="<?php echo $catData['avatar']; ?>" title="<?php echo $catData['username'];?>" />
			<?php endif; ?>
			<img class="award" src="<?php echo base_url() . "assets/theme/agpa/img/cupes/c-1-$award.png"; ?>" title="Agpa <?php echo $award;?>" />
			<p><?php echo $catData['year']; ?></p>
		</div>
		<?php endif; ?>
		
	<?php endforeach; endif; endforeach;  ?>
	
	</div>

	<br class="clear" />
	

	
	<?php endif; ?>
<?php endforeach; ?>








	


