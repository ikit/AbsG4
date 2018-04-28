var editorOpen = false;
var immtControlsPosition;
var immtControls;


// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function agendaInit()
{
    // Init des variables pour gérer le menu header flottant
    agendaControlsPosition = $('#agendaControlsBar').offset().top - 40; // -40 = la taille de la toolbar
    agendaControls = $('#agendaControlsBar');

    // Sur le scroll on va mettre à jour le style du mainmenu 
    $(window).scroll(floatableImmtControls);

}

// Fonction qui fixe ou non le header en fonction du scroll
function floatableImmtControls() 
{
    if ($(window).scrollTop() >=  agendaControlsPosition) 
    {
        // fixed
        agendaControls.addClass("fixed");
    } 
    else 
    {
        // relative
        agendaControls.removeClass("fixed");
    }
}


// Affiche le formulaire pour enregistrer une nouvelle immt et scroll jusqu'au formulaire si besoin.
function displayNewForm()
{
	$('#newEntry').removeClass("hidden");

	var offset = $('#newEntry').offset().top - 80;
	if ($(window).scrollTop() >=  offset)
	{
		$('html, body').animate({ scrollTop: offset }, 0.5);
	}
}


// Permet d'aller d'une page à une autre avec animation
// Lance le chargement asynchrone de la page si besoin
function updateFilter(selector)
{
	// 1) On recupere la valeur du select qui est selectionnee
	filter = $('#filterSelector').find(":selected").val();
	
	// 2) On lance le chargement de la page
	console.log('On charge la page agenda/browseDirectory/'+filter + selector);
	go('agenda/browseDirectory/'+filter + selector);
	
}


function updatePage(form)
{
	if (form == null) return;
	
	goToPage(form.value);
}


