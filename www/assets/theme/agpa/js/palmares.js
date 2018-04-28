

var couleur  = ['#000', '#FFD103', '#FF7800', '#900'];
var loading  = '<img src="./styles/Abs3G/diapo/images/loading7.gif" alt="chargement..." width="94px" height="17px" style="background: transparent; border: none"/>';
var txt_note = 'Votre note&nbsp;:&nbsp;<br/>';

var voteResumePanelPosition;
var voteResumePanel;
var starUsedCounter;
var featherCounter;

var starsUsed = 0;
var totalStars = 0;
var featherUsed = 0;





// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function agpaPhase3Init(su, ts, fu)
{
	starsUsed = su;
	totalStars = ts;
	featherUsed = fu;
	starUsedCounter = $('#starUsedCounter');
	featherCounter = $('#featherCounter');
	
	// Construction du popoup WaitDialog (page d'attente)
	$("#MessageDialog").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 200, // 200 - 20 (la hauteur de ui-titlebar)
		width: 300,
		resizable: false
	});
	$("#MessageDialog").dialog("close");

	// Init des variables pour gérer le menu principal flottant
	voteResumePanelPosition = $('#voteResumePanel').offset().top - 40; // -40 = la taille de la toolbar
	voteResumePanel = $('#voteResumePanel');

	// Sur le scroll on va mettre à jour le style du mainmenu 
	$(window).scroll(floatableVoteResumePanel);

}



// Fonction qui fixe ou non le main menu en fonction du scroll
function floatableVoteResumePanel() 
{
	if ($(window).scrollTop() >=  voteResumePanelPosition) 
	{
		// fixed
		voteResumePanel.addClass("fixed");
	} 
	else 
	{
		// relative
		voteResumePanel.removeClass("fixed");
	}
}











