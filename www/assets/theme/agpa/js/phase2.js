

var ThePhotoId;
var ThePhotoSlot;

// Au chargement 
$(document).ready(function()
{
	// Construction du popup NewImage
	$("#ReportPhoto").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 350,
		width: 600,
		resizable: false,
	});
	$("#ReportPhoto").dialog("close");


	// Construction du popoup WaitDialog (page d'attente)
	$("#MessageDialog").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 200, // 200 - 20 (la hauteur de ui-titlebar)
		width: 300,
		resizable: false
	});
	$("#MessageDialog").dialog("close");



});





// Affichage de la boite de dialogue pour notifier un probleme sur une photo
function showReportPhoto(id_photo, photoSlot)
{
	// modifier paramètres du formulaire en fonction du slot
	ThePhotoSlot = photoSlot;
	ThePhotoId = id_photo;

	// afficher vignette photo et titre dans popup
	$("#ReportPhoto img").attr("src",$("#"+photoSlot+" div.photo img").attr("src"));
	// afficher popup
	$("#ReportPhoto").dialog("open");
}


// Edition du titrePhoto
function reportPhoto(action)
{
	if (action == 'no')
	{
		$("#ReportPhoto").dialog("close");
		return;
	}

	// Récupérer le nouveau titre 
	$("#report").val(jQuery.trim(jQuery("#report").val()));
	var report = $("#report").val();

	// On exécute la requete ajax
	$.post(baseURL + "agpa/reportPhoto", {photoId : ThePhotoId, report : report})
		.done(function(msg_serveur)
		{
			if (msg_serveur == 0)
			{
				$("#ReportPhoto").dialog("close");
				$("#MessageDialog").html("<p>Votre question a été noté. Un administrateur va vérifier ça le plus vite possible. <br/>Merci</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("good");
				$("#MessageDialog").dialog("open");
			}
			else
			{
				$("#ReportPhoto").dialog("close");
				$("#MessageDialog").html("<p>Une erreur est survenue. Dans le doute, prévenez un administrateur par mail.</p><p>"+msg_serveur+"</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("bad");
				$("#MessageDialog").dialog("open");

			}
		});
}








