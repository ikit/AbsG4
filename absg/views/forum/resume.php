
	<h1 class="forum">Les dernières activités sur le forum</h1>


	<div class="rubrique <?php echo ($lastActivities === 0) ? 'rubEmpty' : 'rubNew'; ?>">
		<div class="visu <?php echo ($lastActivities === 0) ? 'rubEmpty' : 'rubNew'; ?>">&nbsp;</div>
		<?php if ($lastActivities !== 0) : ?>
		<?php foreach ($lastActivities as $activity) : ?>
		<a class="activity" href="<?php echo $activity['url']; ?>" title="Accéder directement au sujet en cours">
			<span class="path">
				<span class="section"><?php echo $activity['data']->name; ?> > </span><span class="topic"><?php echo $activity['data']->title; ?></span>
			</span>
			<span class="resume">
			<?php if ($activity['count'] == 1) : ?>
			Nouvelle réponse de <span class="user-<?php echo $activity['data']->last_poster_id; ?>"><?php echo $activity['data']->last_poster_name; ?></span>
			<?php else : ?>
			<?php echo $activity['count']; ?> nouvelles réponses
			<?php endif; ?>
			</span>
			<span class="date"><?php echo $activity['date']; ?> - <?php echo $activity['time']; ?></span>
			<br class="clear"/>
		</a>
		<?php endforeach; ?>
		<?php else : ?>
		<span style="line-height: 120px; color:#555; text-shadow: 0 1px rgba(255, 255, 255, 0.7);">Pas de nouveau message sur le forum ...</span>
		<?php endif; ?>
		<br class="clear"/>
	</div>
	
	<h1 class="forum">Les forums</h1>

	<div class="rubrique rubPublic">
		<div class="visu rubPublic">&nbsp;</div>
		<?php foreach ($forums['public'] as $forum) : ?>
		<div class="forum">
			<a class="block section" href="<?php echo base_url() . 'forum/browse/' . $forum->forum_id; ?>" title="Voir les sujets dans <?php echo $forum->name; ?>"><h2><?php echo $forum->name; ?></h2></a>
			<div class="block desc"><span><?php echo $forum->description; ?></span></div>
			<a class="block last" href="<?php echo base_url() . 'forum/read/' . $forum->topic_id . '/last'; ?>" title="Accéder au dernier sujet dans <?php echo $forum->name; ?>">
				<span><span class="user-<?php echo $forum->last_poster_id; ?>"><?php echo $forum->last_poster_name; ?></span><br/> le <?php echo $this->layout->displayed_date($forum->last_post_time); ?></span>
			</a>
		</div>
		<?php endforeach; ?>
	</div>

	<?php if (count($forums['private']) > 0): ?>
	<div class="rubrique rubPrivate">
		<div class="visu rubPrivate">&nbsp;</div>
		<?php foreach ($forums['private'] as $forum) : ?>
		<div class="forum">
			<a class="block section" href="<?php echo base_url() . 'forum/browse/' . $forum->forum_id; ?>" title="Voir les sujets dans <?php echo $forum->name; ?>"><h2><?php echo $forum->name; ?></h2></a>
			<div class="block desc"><span><?php echo $forum->description; ?></span></div>
			<a class="block last" href="<?php echo base_url() . 'forum/read/' . $forum->topic_id . '/last'; ?>" title="Accéder au dernier sujet dans <?php echo $forum->name; ?>">
				<span><span class="user-<?php echo $forum->last_poster_id; ?>"><?php echo $forum->last_poster_name; ?></span><br/> le <?php echo $this->layout->displayed_date($forum->last_post_time); ?></span>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="rubrique rubArchives">
		<div class="visu rubArchives">&nbsp;</div>
		<a class="activity" href="<?php echo base_url() . 'forum/search/'; ?>">
			<h2 class="block">Trouver</h2>
			<span class="block desc">Effectuer une recherche précise</span>
		</a>
		<a class="activity" href="<?php echo base_url() . 'forum/archives'; ?>">
			<h2 class="block">Fouiller</h2>
			<span class="block desc">Se balader dans les forums des années précédentes</span>
		</a>
		<a class="activity" href="<?php echo base_url() . 'forum/random'; ?>">
			<h2 class="block">Flanner</h2>
			<span class="block desc">Revivre une discussion piochée au hasard dans les archives</span>
		</a>
		<a class="activity" href="<?php echo base_url() . 'forum/stats'; ?>">
			<h2 class="block">Compter</h2>
			<span class="block desc">Les bonnes vieilles stats à tonton Florent</span>
		</a>
	</div>
