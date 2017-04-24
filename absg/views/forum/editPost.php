

	<div id="forumHeaderSlot">
		<div id="forumHeader">
			<h1 class="topTitle">
				<a class="pathRoot" href="<?php echo base_url(); ?>/forum" title="Retour à l'accueil du forum"><span>Accueil</span></a>
				<div>
					<?php foreach ($path as $pahtItem) : ?>
					 > <a href="<?php echo $pahtItem['url']; ?>" title="Accéder à <?php echo $pahtItem['title']; ?>"><?php echo $pahtItem['title']; ?></a>
					 > Modifier le message
					<?php endforeach; ?>
				</div>
			</h1>
		</div>
	</div>
	<br class="clear"/>


	<div id="smiliesShortcutsPanel">
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
				    echo "<a href=\"#\" onclick=\"insertEditorSmiley('$imgUrl');\"><img src=\"$imgUrl\" alt=\"\" /></a> ";
				    
				    ++$c;
				}
			?>
		</div>
	</div>
	<div id="editor">
		<form method="post" action="<?php echo $formAction; ?>" onsubmit="return validateForm()">
			<?php if ($post->post_id == $post->first_post_id) : ?>
				<input type="text" name="title" id="title" placeholder="Le titre du sujet" value="<?php echo $post->title; ?>"/>
			<?php endif; ?>
			<textarea name="message" id="message"><?php echo $post->text; ?></textarea>
			<input type="hidden" name="topicId" id="topicId" value="<?php echo $post->topic_id; ?>"/>
			<input type="hidden" name="page" id="page" value="<?php echo $currentPage; ?>"/>
			<input type="submit" value="Sauvegarder la modification"/>
		</form>
	</div>

	<br class="clear"/>


	<script type="text/javascript">
    // <![CDATA[

        $(document).ready(function()
        {
            initCustomTinyPlugins();

			// On crée l'éditeur wysiwyg
			tinymce.init(
		    {
		        selector: "textarea",
		        theme: "modern",
		        width: 770,
		        height: 300,
		        margin: 'auto',
		        menubar: 'edit insert view format table tools',
		        language : 'fr_FR',
		        plugins: [
		             "advlist autolink link image lists charmap preview hr",
		             "searchreplace visualblocks visualchars code media nonbreaking",
		             "table contextmenu paste textcolor localautosave",
		             "gggSmilies"
		       ],
		       content_css: "../../../assets/theme/default/css/forum.css",
		       toolbar: "insertfile undo redo | size | bold italic | forecolor backcolor | gggSmilies link image media | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | localautosave", 
		       theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
		       style_formats: [
		            {title: 'Texte géant', inline: 'span', classes: 'bb-XL'},
		            {title: 'Texte gros', inline: 'span', classes: 'bb-L'},
		            {title: 'Texte petit', inline: 'span', classes: 'bb-S'},
		            {title: 'Texte minuscule', inline: 'span', classes: 'bb-XS'},
		            {title: 'Citation', block: 'div', classes: 'bb-quote'},
		            {title: 'Console', block: 'div', classes: 'bb-shell'},
		            {title: 'Information', block: 'div', classes: 'bb-info'},
		            {title: 'Avertissement', block: 'div', classes: 'bb-warning'},
		            {title: 'Article', block: 'div', classes: 'bb-article'},
		            {title: 'Agenda', block: 'div', classes: 'bb-calendar'},
		            {title: 'Brouillon', block: 'div', classes: 'bb-todo'}
		        ]
		     }); 
        });
    // ]]>
    </script>
	
