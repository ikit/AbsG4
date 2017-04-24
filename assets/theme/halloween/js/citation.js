var editorOpen = false;
var citationControlsPosition;
var citationControls;


// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function citationInit()
{
    // Init des variables pour gérer le menu header flottant
    citationControlsPosition = $('#citationControlsBar').offset().top - 40; // -40 = la taille de la toolbar
    citationControls = $('#citationControlsBar');

    // Sur le scroll on va mettre à jour le style du mainmenu 
    $(window).scroll(floatableCitationControls);

}

// Fonction qui fixe ou non le header en fonction du scroll
function floatableCitationControls() 
{
    if ($(window).scrollTop() >=  citationControlsPosition) 
    {
        // fixed
        citationControls.addClass("fixed");
    } 
    else 
    {
        // relative
        citationControls.removeClass("fixed");
    }
}


// Affiche le formulaire pour enregistrer une nouvelle citation et scroll jusqu'au formulaire si besoin.
function displayNewForm()
{
	$('#newCitation').removeClass("hidden");

	var offset = $('#newCitation').offset().top - 80;
	if ($(window).scrollTop() >=  offset)
	{
		$('html, body').animate({ scrollTop: offset }, 0.5);
	}
}

// Permet d'aller d'une page à une autre avec animation
// Lance le chargement asynchrone de la page si besoin
function goToPage(page, filter)
{
	// 1) On regarde si on doit/peut changer de page
	needToLoadPage = false;
	if (page == 'p' && currentPage > 0 )
	{
		// Page précédente
		currentPage -= 1;
		needToLoadPage = true;
	}
	else if (page == 'n' && currentPage < totalPage)
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
	go('citation/browse/'+filter+'/'+currentPage);
	
}



function updateFilter(form)
{
	if (form == null) return;
	
	var filter = form.options[form.selectedIndex].value;
	
	go('citation/browse/'+filter);
}


function updatePage(form, filter)
{
	if (form == null) return;
	
	goToPage(form.value, filter);
}


function updatePageControls(page)
{
	// 1) Besoin d'activer ou non les boutons "précédent"
	if (page <= 0)
	{
		$( ".paginationArea .prev" ).addClass("disable");
	}
	else
	{
		$( ".paginationArea .prev" ).removeClass("disable");
	}
	
	// 2) Besoin d'activer ou non les boutons "suivant"
	if (page >= totalPage)
	{
		$( ".paginationArea .next" ).addClass("disable");
	}
	else
	{
		$( ".paginationArea .next" ).removeClass("disable");
	}
	
	// 3) Maj valeur 
	internalUpdate = true;
	
	$( ".paginationArea input" ).val(page+1);
	
	internalUpdate = false;
}

