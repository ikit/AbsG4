

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





/**
 * vote
 * cette fonction effectue tout ce qu'il est necessaire de faire pour prendre en compte un vote.
 * 
 * @param note,       la note que l'utilisateur lui a attrivuee (0-1-2) (0 = sélection pour meilleur titre)
 * @param id_photo,   l'identifiant de la photo a modifier
 * @param id_item,	  l'identifiant de l'élément html contenant la photo (feedback visual sur la page)
 */
function votePhoto(note, id_photo, id_item)
{
	// Test si on peut voter ou pas
	votePool = totalStars - starsUsed;
	if (note == 0)
	{
		// cas particulier : sélection pour meilleur titre
		item = $("a#vt_"+id_item);
		isAdding  = !item.hasClass('selected');

		// 1) On sauvegarde en bd le vote
		saveVote(id_photo, note);

		// 2) Maj décompte
		updateFeathersCounter((isAdding)? 1 : -1);

		// 3) Feedback visuel
		if (isAdding)
		{
			item.addClass('selected');
		}
		else
		{
			item.removeClass('selected');
		}
	}
	else
	{
		// 1) Déterminer cas si ajout ou suppression de vote (on regarde si la class 'selected' est présente ou non)
		v1 = $("a#v1_"+id_item);
		v2 = $("a#v2_"+id_item);
		item = (note == 1) ? v1 : v2;
		isAdding  = !item.hasClass('selected');

		//cas particulier si 2 étoiles et qu'on clique sur la première
		deltaNote = note;
		if (note == 1 && v2.hasClass('selected'))
		{
			isAdding = true;
			deltaNote = -1;
		}

		if (isAdding)
		{
			// Ajout : on test si on a assez d'étoile en stock pour les ajouter
			if (votePool >= note)				
			{
				// 1) On sauvegarde en bd le vote
				saveVote(id_photo, note);

				// 2) Maj décompte
				updateStarsCounter(deltaNote);

				// 3) Feedback visuel
				if (note >= 1) $("a#v1_"+id_item).addClass('selected');
				if (note >= 2) $("a#v2_"+id_item).addClass('selected') ; else $("a#v2_"+id_item).removeClass('selected');
			}
			else
			{
				// Impossible d'ajouter, message d'erreur
				$("#MessageDialog").html("<p>Vous ne pouvez pas voter, vous n'avez plus assez d'étoiles. Vous pouvez en libérer en baissant la note des autres photos pour lesquelles vous avez voté.</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("bad");
				$("#MessageDialog").dialog("open");
			}
		}
		else
		{
			// 1) On sauvegarde en bd le vote
			saveVote(id_photo, note);

			// 2) Maj décompte
			updateStarsCounter(-note);

			// 3) Feedback visuel
			$("a#v1_"+id_item).removeClass('selected');
			$("a#v2_"+id_item).removeClass('selected');
		}
	} 
}


function updateStarsCounter(delta)
{
	starsUsed += delta;
	starUsedCounter.text(starsUsed);
	if (starsUsed >= totalStars / 2)
	{
		starUsedCounter.addClass('good');
		starUsedCounter.removeClass('bad');
	}
	else
	{
		starUsedCounter.addClass('bad');
		starUsedCounter.removeClass('good');
	}
}
function updateFeathersCounter(delta)
{
	featherUsed += delta;
	featherCounter.text(featherUsed);
	if (featherUsed >= 5 && featherUsed <= 10)
	{
		featherCounter.addClass('good');
		featherCounter.removeClass('bad');
	}
	else
	{
		featherCounter.addClass('bad');
		featherCounter.removeClass('good');
	}
}




function saveVote(photo_id, vote)
{
	$.post(baseURL + "agpa/votePhoto", {photoId : photo_id, score : vote})
		.done(function(msg_serveur)
		{
			if (msg_serveur != 0)
			{
				// on affiche l'erreur du serveur
				$("#MessageDialog").html("<p>Une erreur est survenue. Dans le doute, prévenez un administrateur par mail.</p><p>"+msg_serveur+"</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("bad");
				$("#MessageDialog").dialog("open");
			}
		});
}







