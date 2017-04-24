

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
			<?php if ($pagination['totalPages'] > 0): ?>
			<div class="paginationArea">
				Page : <a class="prev" href="javascript:goToPage('p');">&nbsp;</a>  
				<input type="number" onchange="updatePage(this);" value="<?php echo ($pagination['currentPage']+1); ?>" name="pageNumberTop" min="1" max="<?php echo $pagination['totalPages']; ?>" /> / <?php echo $pagination['totalPages']; ?> 
				<a class="next" href="javascript:goToPage('n');">&nbsp;</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<br class="clear"/>

	<?php foreach ($posts as $post) : ?>
	<div class="post row<?php echo $post['row']; ?>">
		<a id="mAnchor<?php echo $post['id']; ?>" name="#<?php echo $post['id']; ?>"/>
		<a class="member <?php echo $post['side']; ?>" href="<?php echo $post['user_url']; ?>" alt="Voir la fiche <?php echo $post['from_username']; ?>">
			<img class="avatar" src="<?php echo $post['avatar']; ?>" alt="<?php echo $post['username']; ?>"/>
			<p class="username"><?php echo $post['username']; ?></p>
			<p class="date"><?php echo $post['date']; ?></p>
		</a>
		<!--<div class="arrow <?php echo $post['side']; ?>"></div>-->
		<div class="message <?php echo $post['side']; ?> msg-rank-<?php echo $post['rank_number']; ?>"><?php echo $post['content']; ?> 
			<?php if ($user->auth === '*' || $post['poster_id'] === $user->user_id): ?>
			
				<div class="msgControl">
					<a class="editMsg" href="<?php echo base_url() . 'forum/edit/' . $post['id'] . '/' . $pagination['currentPage']; ?>">Editer</a>
					
					<?php if ($user->auth === '*' || ($user->user_id === $post['poster_id'] && $topic->last_post_id === $post['id'])) : ?>
					<a class="deleteMsg" href="<?php echo base_url() . 'forum/delete/' . $post['id']; ?>">Supprimer</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<br class="clear"/>
	</div>
	<?php endforeach; ?>

	<?php if ($pagination['totalPages'] > 0): ?>
	<br/>
	<div class="paginationArea" style="float:none!important; margin:auto; text-align: center;">
		<a class="prev" href="javascript:goToPage('p');">&nbsp;</a>  
		<input type="number" onchange="updatePage(this);" value="<?php echo ($pagination['currentPage']+1); ?>" name="pageNumberTop" min="1" max="<?php echo $pagination['totalPages']; ?>" /> / <?php echo $pagination['totalPages']; ?> 
		<a class="next" href="javascript:goToPage('n');">&nbsp;</a>
	</div>
	<?php endif; ?>


	<div id="answer" class="answer answerFeedback">
		<a href="javascript:startWritting('<?php echo $topic->topic_id; ?>');" alt="Commencer la rédaction d'un message">Répondre</a>
		<div id="smiliesShortcutsPanel" class="hidden">
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
					    echo "<a class=\"smileyShortcut\" onclick=\"insertEditorSmiley('$imgUrl');\"><img src=\"$imgUrl\" alt=\"\" /></a> ";
					    
					    ++$c;
					}
				?>
			</div>
		</div>
		<div id="editor"></div>
	</div>

	<br class="clear"/>


	<script type="text/javascript">
    // <![CDATA[
    	var currentTopicId = <?php echo $topicId; ?>;
		var currentPage = <?php echo $pagination['currentPage']; ?>;
		var totalPage = <?php echo $pagination['totalPages']; ?>;
        $(document).ready(function()
        {
            forumInit();
            <?php if ($needScrollToAnchor) echo "scrollTo(\"$scrollToAnchor\")"; ?>
        });
    // ]]>
    </script>
	
