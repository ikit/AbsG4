var editorOpen = false;
var immtControlsPosition;
var immtControls;


// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function immtInit()
{
    // Init des variables pour gérer le menu header flottant
    immtControlsPosition = $('#immtControlsBar').offset().top - 40; // -40 = la taille de la toolbar
    immtControls = $('#immtControlsBar');

    // Sur le scroll on va mettre à jour le style du mainmenu 
    $(window).scroll(floatableImmtControls);

}

// Fonction qui fixe ou non le header en fonction du scroll
function floatableImmtControls() 
{
    if ($(window).scrollTop() >=  immtControlsPosition) 
    {
        // fixed
        immtControls.addClass("fixed");
    } 
    else 
    {
        // relative
        immtControls.removeClass("fixed");
    }
}


// Affiche le formulaire pour enregistrer une nouvelle immt et scroll jusqu'au formulaire si besoin.
function displayNewForm()
{
	$('#newImmt').removeClass("hidden");

	var offset = $('#newImmt').offset().top - 80;
	if ($(window).scrollTop() >=  offset)
	{
		$('html, body').animate({ scrollTop: offset }, 0.5);
	}
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
	go('immt/browse/'+currentPage);
	
}


function updatePage(form)
{
	if (form == null) return;
	
	goToPage(form.value);
}


