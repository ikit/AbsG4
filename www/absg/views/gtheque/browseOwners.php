
	<div id="forumHeaderSlot">
		<h1 id="gthequeHeader" class="topTitle">
			<a class="pathRoot"><span>Accueil</span></a>
			<?php foreach ($path as $pahtItem) : ?>
			 > <a href="<?php echo $pahtItem['url']; ?>" title="Accéder à <?php echo $pahtItem['title']; ?>"><?php echo $pahtItem['title']; ?></a>
			<?php endforeach; ?>
		</h1>
	</div>

	<br class="clear"/>

	<?php foreach ($owners as $owner) : ?>
	<div>
		<div class="ownerWrapper"> <?php echo $owner['username']; ?></div>
		<div class="theques">
			<?php foreach ($owner["theques"] as $theque) : ?>
			<a class="block title" href="<?php echo $theque['url']; ?>" title="Consulter les éléments de la thèque <?php echo $theque['title']; ?>">
				<?php echo $theque['title']; ?> (<?php echo $theque['totalSet']; ?> collections, <?php echo $theque['totalElmt']; ?> elements)
			</a>
			
			<?php endforeach; ?>
		</div>
	</div>
	<?php endforeach; ?>


	<script type="text/javascript">
    // <![CDATA[

        $(document).ready(function()
        {
            forumInit();

        });
    // ]]>   'bd','manga','novel','book','movie','tvshow','videogame','boardgame','miscellaneous','custom','unknow'
    </script>


