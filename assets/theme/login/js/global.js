
var positionElementInPage;
var mainMenu ;


// Méthod qui effectue les branchement nécessaire au bon fonctionnement de l'écran Login
function absgInit(moduleName)
{

	// 1) Selectionne le module concerné dans le menu principale
	$('#mainMenu li.mm-' + moduleName).addClass("select");
	
	
	// 2) init des variables pour gérer le menu principal flottant
	positionElementInPage = $('#toolbar').offset().top;
	mainMenu = $('#toolbar');
	// Sur le scroll on va mettre à jour le style du mainmenu 
	$(window).scroll(floatableMainMenu);
}



// Fonction qui fixe ou non le main menu en fonction du scroll
function floatableMainMenu() 
{
	if ($(window).scrollTop() >= positionElementInPage) 
	{
		// fixed
		mainMenu.addClass("fixed");
	} else 
	{
		// relative
		mainMenu.removeClass("fixed");
	}
}

function go(link)
{
	var url  = baseURL + link;
	window.location = url;
}












