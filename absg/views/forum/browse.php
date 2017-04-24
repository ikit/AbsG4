
	<div id="forumHeaderSlot">
		<div id="forumHeader">
			<h1 class="topTitle">
				<a class="pathRoot" href="<?php echo base_url(); ?>/forum" title="Retour à l'accueil du forum"><span>Accueil</span></a>
				<div>
					<?php foreach ($path as $pahtItem) : ?>
					 > <a href="<?php echo $pahtItem['url']; ?>" title="Accéder à <?php echo $pahtItem['title']; ?>"><?php echo $pahtItem['title']; ?></a>
					<?php endforeach; ?>
				</div>
			</h1>
			<div class="controlsArea">
				<a class="newTopic" href="<?php echo $newTopicUrl; ?>">Nouveau</a> 
			</div>
		</div>
	</div>

	<br class="clear"/>

	<?php foreach ($topics as $topic) : ?>
	<div>
		<div class="topicWrapper <?php echo $topic['icon']; ?>">&nbsp;</div>
		<div class="topic row<?php echo $topic['row']; ?>">
			<a class="block title" href="<?php echo base_url() . 'forum/read/' . $topic['id']; ?>/first" title="Lire le sujet <?php echo $topic['title']; ?>">
				<h3><?php echo $topic['title']; ?></h3>
				<span>
					Par <span class="user-<?php echo $topic['from_userId']; ?>"><?php echo $topic['from_username']; ?></span>
					le <?php echo $topic['from_date']; ?>
				</span>
			</a>
			<div class="block desc"><span><?php echo $topic['replies']; ?></span></div>
			<a class="block last" href="<?php echo base_url() . 'forum/read/' . $topic['id'] . '/last'; ?>" title="Lire la dernière réponse">
				<span>
					Dernier message :<br/>
					<span class="user-<?php echo $topic['last_userId']; ?>"><?php echo $topic['last_username']; ?></span> le <?php echo $topic['last_date']; ?>
				</span>
			</a>
		</div>
	</div>
	<?php endforeach; ?>


	<script type="text/javascript">
    // <![CDATA[

        $(document).ready(function()
        {
            forumInit();

        });
    // ]]>
    </script>