
<div style="float: left; width: 400px; text-align: justify; ">
<i>
Depuis leur création en 2006, les Absolument&nbsp;G Photo Awards récompensent les meilleures photos de l’année parmi celles proposées par les membres du forum Absolument&nbsp;<sup>3</sup>G.<br/><br/>

Les meilleures photos seront élues par les membres du forum eux-mêmes, qui pourront voter pour leurs photos préférées à la fin de l'année, en se rendant ici même. <br/><br/>
Depuis 2006, <span style="color:#f55; font-weight:bold"><?php echo $nbr_editions; ?></span> éditions des AGPA ont été réalisées. Soit un total de <span style="color:#f55; font-weight:bold"><?php echo $nbr_photos; ?></span> photos, proposées par <span style="color:#f55; font-weight:bold"><?php echo $nbr_authors; ?></span> photographes !
</i>
</div>


<div style="float: right; width: 200px; text-align: center;">
	<a href="{PHOTO_URL_FULLSCR}" rel="lightbox_images" title="Agrandir !">
	  <img src="{PHOTO_URL_THUMB}" alt="photo aléatoire" style="border: 1px solid #000; background: #fff; padding: 1px;"/>
	</a><br/>
	<b>{PHOTO_TITLE}</b><br/>
	par {PHOTO_AUTHOR} (AGPA {PHOTO_YEAR}).
</div>

<br class="clear" style="margin: 20px;"/>


<div id="frise">
	<div id="frise_100" style="width:<?php echo $phase_timeline_progression; ?>px">&nbsp;</div>
</div>
<br class="clear" />
<div class="pannel" style="margin-top: 10px; margin-right:10px;">
	<div class="top">&nbsp;</div>
<?php if ($current_phase == 1): ?>
	<h2 style="color:#9FDBFF">Phase 1 : <span>Enregistrement des photos</span></h2> 
	<dl>
	<dt>Période</dt>
	<dd> Du 15 décembre <?php echo $previous_year; ?> au 15 décembre <?php echo $actual_phase_year; ?>.<br/>
	<!-- IF REMAINING_TIME -->
	    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 2.
	<!-- ELSE -->
	    L'enregistrement des photos est terminé. La phase 2 sera bientôt activée.
	<!-- ENDIF -->
	</dd>
	<dt>Description</dt> 
	<dd>Durant cette première phase du concours, vous pouvez enregistrer vos photos. Bien que ce concours soit sans obligation, toute participation (aussi modeste soit-elle) est la bienvenue, ceci afin de rendre plus intéressant le concours.</dd>
	
	<dt style="color: #f55">Réglement</dt>
	<dd> Vos photos doivent impérativement avoir été prises par vous même (pas forcément avec votre appareil photo) et au cours de l'année {ACTUAL_YEAR}. De même, depuis les AGPA 2007, elles doivent obligatoirement posséder un titre.<br/>Pour plus de détails, vous pouvez lire <a href="{ROOT_PATH}agpa.php?section=rules&amp;sid={_SID}" title="Lire le réglement en ligne">le réglement</a>. </dd>

	<dt>Astuce</dt>
	<dd> Il existe actuellement 2 moyens pour enregistrer vos photos :
	<ul><li>En cliquant sur les espaces libres sur cette page, vous pourrez y insérer une photo.<br/><span style="color: #888">(Attention cependant, le systême ne fonctionne pas avec le nevigateur web <i>Internet Explorer</i>. De plus les photos ne doivent pas excéder 1,5Mo en taille, et 2500 pixels en hauteur ou largeur)</span></li>
	    <!-- <li>En utilisant le logiciel <i><a href="girouette.php?sid={_SID}" title="Accéder à la page web du logiciel">Girouette</a></i>.<br/><span style="color: #888">(Aucune limite de taille, c'est le moyen le plus simple pour enregistrer vos photos)</span></li>-->
	    <li>En les envoyant par mail à Olivier : <a class="mailto" href="mailto:gueudelotolive@gmail.com?AGPA {ACTUAL_YEAR} - Envoie de photos" title="Ecrire à Olivier">gueudelotolive@gmail.com</a>.<br/><span style="color: #888">(N'ayez pas peur, il aime bien ça)</span></li>
	</ul>
	</dd>
	</dl>
	<br class="clear" style="margin: 10px;"/>
	<p>Pour accéder à la phase 1 :<a style="color: #fff" href="{ROOT_PATH}agpa.php?section=actual&amp;sid={_SID}" title="Accéder à la phase 1"><b> cliquez ici</b></a>.</p>

<?php elseif ($current_phase == 2): ?>
	<h2 style="color:#5CB6FF">Phase 2 : <span>Vérifications des photos</span></h2>
	<dl>
	<dt>Période : </dt>
	<dd> Du 15 décembre <?php echo $actual_phase_year; ?> au 17 décembre <?php echo $actual_phase_year; ?>.<br/>
	<!-- IF REMAINING_TIME -->
	    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 3.
	<!-- ELSE -->
	    La vérification des photos est terminée. La phase 3 sera bientôt activée.
	<!-- ENDIF -->
	</dd>
	<dt>Description : </dt>
	<dd>Maintenant que les photos sont enregistrées, il est nécessaire avant de procéder aux votes, de vérifier si elles respectent le <a href="{ROOT_PATH}agpa.php?section=rules&amp;sid={_SID}" title="Lire le réglement en ligne">réglement</a>. Pour toutes photos entrant en contradiction avec le réglement ou si plusieurs membres ont postés des photos trop similaires, un échange de photo sera permis aux membres qui en feront la demande au responsable actuelle du concours : Olivier.</dd>
	</dl>
	<br class="clear" style="margin: 10px;"/>
	<p>Pour accéder à la phase 2 :<a style="color: #fff" href="{ROOT_PATH}agpa.php?section=actual&amp;sid={_SID}" title="Accéder à la phase 2"><b> cliquez ici</b></a>.</p>

<?php elseif ($current_phase == 3): ?>
	<h2 style="color:#178FFF">Phase 3 : <span>Votes</span></h2>
	<dl>
	<dt>Période : </dt>
	<dd> Du 17 décembre <?php echo $actual_phase_year; ?> au 21 décembre <?php echo $actual_phase_year; ?>.<br/>
	<!-- IF REMAINING_TIME -->
	    Il vous reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant de passer à la phase 4.
	<!-- ELSE -->
	    Les votes sont clos. Si tout se passe bien, la cérémonie aura lieu fin décembre.
	<!-- ENDIF -->
	</dd>
	<dt>Description : </dt>
	<dd>Vous pouvez maintenant voter pour vos photos préférées. Pour cela, pour chaque catégorie, vous devez attribuer 6 points à trois photos, de la manière suivante : <ul>
		<li>3 points pour votre photo préférée ;</li>
		<li>2 points pour la deuxième ;</li>
		<li>1 point pour une troisième.</li>
	</ul>L'AGPA d'or sera décerné à la photo ayant reçu le <b>plus</b> de points.</dd>
	</dl>
	<br class="clear" style="margin: 10px;"/>
	<p>Pour accéder à la phase 3 :<a style="color: #fff" href="{ROOT_PATH}agpa.php?section=actual&amp;sid={_SID}" title="Accéder à la phase 3"><b> cliquez ici</b></a>.</p>

<?php elseif ($current_phase == 4): ?>
	<h2 style="color:#BA5660">Phase 4 : <span>Dépouillement</span></h2>
	<dl>
	<dt>Période : </dt>
	<dd> Du 21 décembre <?php echo $actual_phase_year; ?> au 24 décembre <?php echo $actual_phase_year; ?>.<br/>
	<!-- IF REMAINING_TIME -->
	    Il reste <span style="color:#f77; font-weight: 500;"><?php echo $phase_timeleft; ?></span> avant la cérémonie des AGPA.
	<!-- ELSE -->
	    La cérémonie aura lieu fin décembre.
	<!-- ENDIF -->
	</dd>
	<dt>Description : </dt>
	<dd>Les votes enregistrés par les jurés vont être vérifiés, puis les notes des photos vont être calculées afin de sélectionner les meilleurs de chaque catégorie.<br/>
	C'est aussi durant cette période qu'est préparé la cérémonie de AGPA où seront remis les récompenses aux auteurs des plus belles photos.
	</dd>
	</dl>
	<br class="clear" style="margin: 10px;"/>
	<p>En attendant, vous pouvez visionner à loisir les photos de l'édition {ANNEE} des AGPA : <a style="color: #fff" href="{ROOT_PATH}agpa.php?section=actual&amp;sid={_SID}" title="Accéder à la phase 3"><b> cliquez ici</b></a>.</p>

<?php elseif ($current_phase == 5): ?>
	<h2 style="color:#F3E8B0">Phase 5 : <span>Résultats du concours</span></h2>
	<dl>
	<dt>Description : </dt>
	<dd>Ca y est, l'édition <?php echo $actual_phase_year; ?> est terminée !<br/>
	Encore toutes nos félicitations aux participants qui font vivre ce concours. Et maintenant, place aux révélations. 
	</dd>
	</dl>
	<br class="clear" style="margin: 10px;"/>
	<p>Pour connaître les résultats de l'édition <?php echo $actual_phase_year; ?> : <a style="color: #fff" href="{ROOT_PATH}agpa.php?section=actual&amp;sid={_SID}" title="Accéder à la phase 3"><b> cliquez ici</b></a>.</p>
<?php endif; ?>

	<br/>
	<table width="100%" style="margin-top: 10px;"><tbody>
	<tr><th style="color:#666" colspan="2">Etat de votre participation</th><th style="width: 150px">&nbsp;</th><th style="color:#666" colspan="2">Participation générale</th></tr>

	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat1_color; ?>;"><?php echo $cat1_name; ?> :</div></td><td> <b><?php echo $cat1_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat1_color; ?>;"><?php echo $cat1_name; ?> :</div></td><td> <b><?php echo $cat1_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat2_color; ?>;"><?php echo $cat2_name; ?> :</div></td><td> <b><?php echo $cat2_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat2_color; ?>;"><?php echo $cat2_name; ?> :</div></td><td> <b><?php echo $cat2_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat3_color; ?>;"><?php echo $cat3_name; ?> :</div></td><td> <b><?php echo $cat3_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat3_color; ?>;"><?php echo $cat3_name; ?> :</div></td><td> <b><?php echo $cat3_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat4_color; ?>;"><?php echo $cat4_name; ?> :</div></td><td> <b><?php echo $cat4_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat4_color; ?>;"><?php echo $cat4_name; ?> :</div></td><td> <b><?php echo $cat4_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat5_color; ?>;"><?php echo $cat5_name; ?> :</div></td><td> <b><?php echo $cat5_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat5_color; ?>;"><?php echo $cat5_name; ?> :</div></td><td> <b><?php echo $cat5_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat6_color; ?>;"><?php echo $cat6_name; ?> :</div></td><td> <b><?php echo $cat6_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat6_color; ?>;"><?php echo $cat6_name; ?> :</div></td><td> <b><?php echo $cat6_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat7_color; ?>;"><?php echo $cat7_name; ?> :</div></td><td> <b><?php echo $cat7_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat7_color; ?>;"><?php echo $cat7_name; ?> :</div></td><td> <b><?php echo $cat7_global_participation; ?>&nbsp;photos</b></td></tr>
	<tr><td><div class="CatTitleTrimmed" style="color:<?php echo $cat8_color; ?>;"><?php echo $cat8_name; ?> :</div></td><td> <b><?php echo $cat8_user_participation; ?>&nbsp;/&nbsp;2</b></td><td>&nbsp;</td>
	    <td><div class="CatTitleTrimmed" style="color:<?php echo $cat8_color; ?>;"><?php echo $cat8_name; ?> :</div></td><td> <b><?php echo $cat8_global_participation; ?>&nbsp;photos</b></td></tr>
	</tbody></table>


	<div class="bottom">&nbsp;</div>
</div>



	


