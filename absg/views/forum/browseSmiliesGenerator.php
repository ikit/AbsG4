<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');








// Creation de l'arborecence
function ScanRepertoire($repertoire)
{
	$arbo = array();
	$rep = opendir($repertoire);
	$ss_rep = false;
	while($fichier = readdir($rep)) 
	{
		if (substr($fichier, 0, 2) == "x-") continue;
		if( is_dir($repertoire."/".$fichier) AND $fichier != "." AND $fichier != "..") 
		{
			$arbo["$fichier"] = ScanRepertoire("$repertoire/$fichier", $arbo);
			$ss_rep = true;
		}
		else if (eregi(".gif",$fichier) OR eregi(".png",$fichier)) 
		{
			//$arbo[] = $fichier;
		}
		// sinon on ignore
	}
	closedir($rep);
	if ($ss_rep) asort($arbo);
	else         sort($arbo);
	return $arbo;
}

// L'arborescence etant toujours la meme, et pour pas que le serveur travail inutilement, elle a ete ecrite en dur
// dans un fichier js. ce fichier js peut être généré automatiquement grace à ce script php. 

// $arbor = ScanRepertoire(PATH_SMILIES);
// asort($arbor);
// print_r($arbor);
// die;
// // STOP

$rubriques = array(
    0 => "Actions",
    1 => "Emotions-Etats",
    2 => "Personnages",
    3 => "Divers",
    4 => "Specials"
);

$arborescence = array(
    0 => array(
            "Transport", "Saluer", "Rechercher", "Musique", "Dormir", "Jouer", "Telephoner", "Moqueur", "Sport", "Pardonner", "Feter", "Danser", "Acquiescer", "Manger", "Rigoler", "Lire", "Refuser", "Chanter"
            ),
    1 => array(
            "Rougir","Peur","Triste","Malade","Deception","Tendresse","Colere","Mefiance","Content","Innocent","Etonnement","Amour"
            ),
    2 => array(
            "Medievals","Debiles","Membres","Mode","Celebres","Bebes","Noel","Metiers-Passions","Films","Ethnies","South_Park","BD-Manga","Personnages","Costumes","Robots"
            ),
    3 => array(),
    4 => array(
            "Animaux","Symboles","Ours","Cochons","Alphabet"
            )
);



// Pour regénérer le fichier js gérant les menus des smilies, il faut décommenter le block ci-dessous
// (et pensez à le recommenter une fois le travail terminé)
/*
// construction des menu select :
$js_define_rub = "";
$js_rub_switch = "";
$js_define_sections = "";
$id_menu = 0;



foreach ($arborescence as $menu => $ssmenu)
{
    $rub_size = sizeof($ssmenu) + 1;
    $js_define_rub .= "var liste$id_menu = new Array($rub_size); // $menu\n";

    $js_rub_switch .= "        case $id_menu: // $menu\n";
    $js_rub_switch .= "            for (i = 0 ; i < liste$id_menu.length ; i++) {\n";
    $js_rub_switch .= "            form1.listSections.options[i] = liste{$id_menu}[i];\n";
    $js_rub_switch .= "            }\n";
    $js_rub_switch .= "        break;\n";

    // Pour chaque rubrique, la liste des sections comporte au moins 1 élément
    $js_define_sections .= "// Sections de la rubrique $menu\n";
    $js_define_sections .= "  liste{$id_menu}[0] = new Option();\n";
    $js_define_sections .= "  liste{$id_menu}[0].text = \"----------\";\n";
    $js_define_sections .= "  liste{$id_menu}[0].value = \"0\";\n";

    if ( !empty ($ssmenu))
    {
        $i_cat = 1;
        foreach($ssmenu as $cat)
        {
            $js_define_sections .= "  liste{$id_menu}[$i_cat] = new Option();\n";
            $js_define_sections .= "  liste{$id_menu}[$i_cat].text = \"$cat\";\n";
            $js_define_sections .= "  liste{$id_menu}[$i_cat].value = \"$i_cat\";\n";
            ++$i_cat;
        }
        $js_define_sections .= "\n";
    }

    ++$id_menu;
}


echo <<<EOF
//
// Ce fichier est généré automatiquement par browsSmiliesGenerator.php
// Il faut éditer ce fichier pour qu'il fasse ce travail.
//


// Les Rubriques détectées lors de la générations du fichier js
$js_define_rub

$js_define_sections

// Méthode pour switcher entre les différents menus
function switchRub(form1)
{
    var choice=form1.listRubriques.selectedIndex;

    // On supprime d'abord les anciennes options du second menu
    for (i = 0 ; i < form1.listSections.options.length ; i++)
    {
        form1.listSections.options[i] = null;
    }

    // On met ensuite à jours les options du second menu en accord avec l'option
    // choisie dans le premier menu
    switch(choice)
    {
$js_rub_switch
    }
}


EOF;

die;
*/