

function citationsInit(cp, tp)
{
	currentPage = cp;
	totalPage = tp;
	loadingPagesStatus = new Array(tp);
	loadingPagesStatus[0] = true;
	
	$("#citation1").hover(
		function () {$("#citation1EditPanel").addClass("visible");},
		function () {$("#citation1EditPanel").removeClass("visible");}
	);
	
	$("#create-citation").click(function () 
	{
		$("#newCitationForm").show();
		
	});
	
	updatePageControls(cp);
}

// Permet d'aller d'une page à une autre avec animation
// Lance le chargement asynchrone de la page si besoin
function goToPage(page, filter)
{
	// 1) On regarde si on doit/peut changer de page
	needToLoadPage = false;
	if (page == -1 && currentPage > 0 )
	{
		// Page précédente
		currentPage -= 1;
		needToLoadPage = true;
	}
	else if (page == 1000 && currentPage < totalPage)
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
	alert('change de page : ' + filter + ',' + currentPage);
	
	// 2) On lance le chargement asynch de la page si besoin
	console.log('On charge la page '+currentPage);
	go('citation/browse/'+filter+'/'+currentPage);
	
	
	// 3) On met à jour la vue
	//updatePageControls(currentPage);
	
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

