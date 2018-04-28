

var ThePhotoId;
var ThePhotoSlot;

// Au chargement 
$(document).ready(function()
{
	// Construction du popup NewImage
	$("#NewImage").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 350,
		width: 600,
		resizable: false,
	});
	$("#NewImage").dialog("close");

	// Construction du popup DeleteImage
	$("#DeleteImage").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 350,
		width: 600,
		resizable: false
	});
	$("#DeleteImage").dialog("close");

	// Construction du popup EditTitle
	$("#EditTitle").dialog({ 
		modal: true, 
		overlay: {opacity: 0.7, background: "black"},
		height: 350,
		width: 600,
		resizable: false
	});
	$("#EditTitle").dialog("close");

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



/*  ENREGISTREMENT NOUVELLE PHOTO */
// Affichage de la boite de dialogue pour enregistrer une nouvelle photo
function showNewImage(categorie, photoSlot)
{
	// maj du formulaire en fonction de la catégorie
	$("#newTitle").val("");
	$("#newCategory").val(categorie);
	$("#newPhoto").val("");
	ThePhotoSlot = photoSlot;
	// afficher popup
	$("#NewImage").dialog("open");
}

// quand submit, faire patienter
function submitImage()
{
	$("#NewImage").dialog("close");
	$("#MessageDialog").removeClass();
	$("#MessageDialog").addClass("wait");
	$("#MessageDialog").html("<p>La photo est en cours d'enregistrement.<br/>Merci de patienter quelques secondes.</p>");
	$("#MessageDialog").dialog("open");
}

// quand retour positif
function postPhotoOK(message, idPhoto, title, urlFull, urlThumb, urlOriginal)
{
	// Cacher message d'attente et afficher Confirmation
	$("#MessageDialog").html("<p>Votre photo à bien été enregistrée ! <br/>" + message +"</p>");
	$("#MessageDialog").removeClass();
	$("#MessageDialog").addClass("good");
	$("#MessageDialog").dialog("open");

	// Ajouter nouvelle photo dans slot
	html =  '<div class="photo">';
	html += '<a href="'+urlFull+'" rel="lightbox[agpa]" title="'+title+'">';
	html += '<img src="'+urlThumb+'" /></a>';
	html += '</div>';
	html += '<p class="title">'+title+'</p>';
	html += '<div class="pannel"><a class="deletePhoto" onclick="javascript:showDeleteImage(\''+idPhoto+'\',\''+ThePhotoSlot+'\');" title="Supprimer la photo ?">Supprimer</a>';
	html += '<a class="editPhotoTitle" onclick="javascript:showEditTitle(\''+idPhoto+'\',\''+ThePhotoSlot+'\');" title="Modifier le titre de la photo ?">Modifier le titre</a>';
	html += '<br class="clear"/>';
	html += '</div>';

	// On ajoute la photo
	$("#"+ThePhotoSlot).html(html);

	// on reinitialise le lightbox
	$('a[@rel*=lightbox]').lightBox();

	// On cache le message indiquant qu'il n'y a aucune photo d'enregistrée
	$('#infogen').attr('style', 'display:none;');
}

// Quand retour négatif
function postPhotoERREUR(msg)
{
	$("#MessageDialog").html(msg);
	$("#MessageDialog").removeClass();
	$("#MessageDialog").addClass("bad");
	$("#MessageDialog").dialog("open");
}




// Affichage de la boite de dialogue pour modifier le titre d'une photo
function showEditTitle(id_photo, photoSlot)
{
	// modifier paramètres du formulaire en fonction du slot
	ThePhotoSlot = photoSlot;
	ThePhotoId = id_photo;

	// afficher vignette photo et titre dans popup
	$("#EditTitle img").attr("src",$("#"+photoSlot+" div.photo img").attr("src"));
	$("#editTitleInput").val($("#"+photoSlot+" div.pannel div.titre").html());
	// afficher popup
	$("#EditTitle").dialog("open");
}


// Edition du titrePhoto
function editPhotoTitle(action)
{
	if (action == 'no')
	{
		$("#EditTitle").dialog("close");
		return;
	}

	// Récupérer le nouveau titre 
	$("#editTitleInput").val(jQuery.trim(jQuery("#editTitleInput").val()));
	var title = $("#editTitleInput").val();

	// On exécute la requete ajax
	$.post("agpa/updateTitle", {photoId : ThePhotoId, newTitle : title})
		.done(function(msg_serveur) 
		{
			if (msg_serveur == 0)
			{
				$("#"+ThePhotoSlot+" p.title").text(title);
				$("#EditTitle").dialog("close");
				$("#MessageDialog").html("<p>Titre de la photo modifié</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("good");
				$("#MessageDialog").dialog("open");
			}
			else
			{
				$("#EditTitle").dialog("close");
				$("#MessageDialog").html("<p>Une erreur est survenue. Faites F5 pour voir si votre modification a été prise en compte. Sinon, prévenez un administrateur.</p>");
				$("#MessageDialog").removeClass();
				$("#MessageDialog").addClass("bad");
				$("#MessageDialog").dialog("open");
			}
		});
}





// Affichage de la boite de dialogue pour supprimer une photo
function showDeleteImage(id_photo, photoSlot)
{
	// modifier paramètres du formulaire en fonction du slot
	ThePhotoSlot = photoSlot;
	ThePhotoId = id_photo;

	// afficher vignette photo dans popup
	$("#DeleteImage img").attr("src",$("#"+photoSlot+" div.photo img").attr("src"));
	// afficher popup
	$("#DeleteImage").dialog("open");
}

function deletePhoto(action)
{
	if (action == 'no')
	{
		$("#DeleteImage").dialog("close");
		return;
	}

	$("#DeleteImage").dialog("close");
	$("#MessageDialog").html("<p>Suppression en cours...<br/>Merci de patienter quelques secondes.</p>");
	$("#MessageDialog").removeClass();
	$("#MessageDialog").addClass("wait");
	$("#MessageDialog").dialog("open");

	$.post("agpa/deletePhoto", {photoId : ThePhotoId})
		.done(function(id_categorie) 
		{
			// supprimer la photo
			html  = '<div class="photo">';
			html += '<a class="emptySlot" onclick="javascript:showNewImage(\''+id_categorie+'\', \''+ThePhotoSlot+'\');" title="Cliquez ici pour insérer une photo">&nbsp;</a>';
			html += '</div>';

			$("#"+ThePhotoSlot).html(html);

			// Afficher message
			$("#MessageDialog").html("<p>Votre photo a bien été supprimée.</p>");
			$("#MessageDialog").removeClass();
			$("#MessageDialog").addClass("good");
			$("#MessageDialog").dialog("open");

			// on reinitialise le lightbox
			//$('a[@rel*=lightbox]').lightBox();
		});
}






