

var editorOpen = false;
var forumHeaderPosition;
var forumHeader;


// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function forumInit()
{
    // Init des variables pour gérer le menu header flottant
    forumHeaderPosition = $('#forumHeader').offset().top - 40; // -40 = la taille de la toolbar
    forumHeader = $('#forumHeader');

    // Sur le scroll on va mettre à jour le style du mainmenu 
    $(window).scroll(floatableForumHeader);

	// ajout d'une popup de confirmation sur les liens pour supprimer
	$('.deleteMsg').on('click', function () 
	{
        return confirm("Etes-vous sûr de vouloir supprimer ce message ?\nL'action est irréversible.");
    });
}

// Fonction qui fixe ou non le header en fonction du scroll
function floatableForumHeader() 
{
    if ($(window).scrollTop() >=  forumHeaderPosition) 
    {
        // fixed
        forumHeader.addClass("fixed");
    } 
    else 
    {
        // relative
        forumHeader.removeClass("fixed");
    }
}

// Fonction pour scroller directement au niveau de l'anchor spécifié
function scrollTo(hash) 
{
    //location.hash = "#" + hash; // ne marche pas à cause de l'url rewritting de codeignitier qui fait que le hash est interprété comme un paramètre...
    var anchor =$("#mAnchor" + hash);
    var offset = anchor.offset().top;
    $("body,html").scrollTop(offset - 120);
}






// Met en place l'éditeur WYSIWYG pour la rédaction de post
function startWritting(action)
{
	if (editorOpen) return false;

	// On ajoute le formulaire au document html
	$('#editor').append( '<form method="post" action="'+baseURL+'forum/newp/'+action+'"><textarea name="message" id="message"></textarea><input type="submit" value="Envoyer ma réponse"/></form><br/>' );

    // On initialise les plugins persos
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
            //{title: 'Example 2', inline: 'span', classes: 'example2'},
            //{title: 'Table styles'},
            //{title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ]
     }); 

	// On supprime les feedbacks visuels du "répondre"
	$('#answer').removeClass('answerFeedback');

    // On affiche les racourcis smilies
    $('#smiliesShortcutsPanel').removeClass('hidden');

	// On s'assure qu'en cliquant à nouveau sur répondre on ne recrée pas de nouvel éditeur
	editorOpen = true;
}


// Méthode appelé depuis la popup "smilies" pour insérer un smiley dans l'éditeur
function insertEditorSmiley(path)
{
    tinymce.activeEditor.insertContent('<img src="' + path + '"/>');
}


// Méthode pour initialiser un "plugins" special pour les smilies dans l'éditeur tinyMCE
function initCustomTinyPlugins()
{
    tinymce.PluginManager.add('gggSmilies', function(editor, url) 
    {
        // Add a button that opens a window
        editor.addButton('gggSmilies', {
            icon: 'emoticons',
            tooltip:"Smilies",
            onclick: function() {
                // Open window with a specific url
                editor.windowManager.open({
                    title: 'Smilies G',
                    url: baseURL + 'forum/browseSmilies',
                    width: 200,
                    height: 400,
                    scrollbars: false,
                    buttons: [{
                        text: 'Close',
                        onclick: 'close'
                    }]
                });
            }
        });

        // Adds a menu item to the tools menu
        /*
        editor.addMenuItem('gggSmilies', {
            text: 'Example plugin',
            context: 'tools',
            onclick: function() {
                // Open window
                editor.windowManager.open({
                    title: 'Example plugin',
                    body: [
                        {type: 'textbox', name: 'title', label: 'Title'}
                    ],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
                        editor.insertContent('Title: ' + e.data.title);
                    }
                });
            }
        });
        */
    });
}


// Permet d'aller d'une page à une autre avec animation
// Lance le chargement asynchrone de la page si besoin
function goToPage(page)
{
	// 1) On regarde si on doit/peut changer de page
	needToLoadPage = false;
	if (page == 'p' && currentPage > 0 )
	{
		// Page précédente
		currentPage -= 1;
		needToLoadPage = true;
	}
	else if (page == 'n' && currentPage < totalPage - 1)
	{
		// Page suivante
		currentPage += 1;
		needToLoadPage = true;
	}
	else if (page >= 1 && page <= totalPage)
	{
		currentPage = page - 1;
		needToLoadPage = true;
	}
	
	if (!needToLoadPage) return;
	
	// 2) On lance le chargement de la page
	console.log('On charge la page '+currentPage);
    go('forum/read/'+currentTopicId+'/'+currentPage);
    
}


function updatePage(form)
{
    if (form == null) return;
    
    goToPage(form.value);
}