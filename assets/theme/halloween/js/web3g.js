


// Met en place l'éditeur WYSIWYG pour la rédaction de post
function clickOn(siteId)
{
	// 1) on comptabilise le click sur le site (simple stat)
	$.ajax(baseURL + 'web3g/clickOn/' + siteId);
}

