<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * setupTimeLine
 * Fait les calcul nécéssaire, et met à jour le $template afin 
 * d'afficher la frise chronologique des AGPA
 *
 * @param $phase [int], le numéro de la phase actuelle.
 * @return [int] le nombre de seconde qu'il reste avant de passer à la phase suivante
 *               renvoie false si la phase indiqué ne correspond pas aux dates.
 *
 * Rappel des phase :
 *   1 : enregistrement des oeuvre        [ jusqu'au 15 décembre ]
 *   2 : vérification des photos          [ du 15 au 17 décembre ]
 *   3 : votes                            [ du 17 au 21 décembre ]
 *   4 : calculs et préparation cérémonie [ du 21 au 24 décembre ]
 *   5 : post cérémonie                   [ du 24 jusqu'au démarrage (manuelle) de la prochaine édition ]
 */
if ( ! function_exists('setupTimeLine'))
{
    function setupTimeLine($phase, $limits_phases, &$ctx)
    {
        // Gestion de la frise chronologique
        $pixels_lenght_phases = array(1=>array(1,238), 2=>array(239, 88), 3=>array(327, 153), 4=>array(481, 135));
        $pixels = 0;

        $time_left = false;

        if ($phase == 5)
        {
            $pixels = $pixels_lenght_phases[4][0]+$pixels_lenght_phases[4][1];
        }
        else
        {
            $endMkt   = mktime(0,0,0, $limits_phases[$phase][1],   $limits_phases[$phase][0], date('Y'));
            $startMkt = mktime(0,0,0, $limits_phases[$phase-1][1], $limits_phases[$phase-1][0], date('Y'));
            $actualMkt = time();

            // le nombre d'heure entre la date de début de la phase et aujourd'hui
            $delta_hr  = max ( ($actualMkt - $startMkt) / 3600, 0) ;
            // le nombre d'heure que dure la phase en entier (du début à la fin)
            $total_hr  = ($endMkt-$startMkt) / 3600;
            
            // le nombre de pixel a afficher de l'image
            $pixels = $pixels_lenght_phases[$phase][0] + min( ( ($pixels_lenght_phases[$phase][1] * $delta_hr) / $total_hr * 100 ) / 100, $pixels_lenght_phases[$phase][1]);
            


            $time_left = $endMkt-$actualMkt;
        }



        // Conversion du temps restant en string
        if ($time_left)
        {
            $jours = (int) ($time_left/86400);
            $time_left -= $jours*86400;
            $heures = (int) ($time_left/3600);
            $time_left -= $heures*3600;
            $minutes = (int) ($time_left/60);

            $time_left  = "$jours jour".(($jours > 1)?'s':'');
            $time_left .= ", $heures heure".(($heures > 1)?'s':'');
            $time_left .= " et $minutes minute".(($minutes > 1)?'s':'');
        }

        $ctx['phase_timeline_progression'] = $pixels;
        $ctx['phase_timeleft'] = $time_left;
    }
}


/**
 * initPhotosData
 * Crée la liste des photos d'une édition, par catégories
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @param $user[array], les informations relatives à l'utilisateur.
 * @param $year, l'année de l'édition
 * @return $ctx [array] le contexte mis à jour avec le tableau récapitulatif des photos de l'edition des AGPA (triées par catégories)
 */
if ( ! function_exists('initPhotosData'))
{
    function initPhotosData(&$ctx, $user, $year)
    {        
        $CI = get_instance();
        $havePhoto = false; // est-ce que l'utilisateur a posté des photos ?
        $photos = array();  // l'ensemble des photos du concours (de l'édition en cours)
        $sql = 'SELECT * FROM agpa_photos WHERE year='.$year.' ORDER BY category_id ASC, number ASC';
        
        // On ajoute des informations sur la participation aux catégories
        foreach($ctx['categories'] as $cat)
        {
            $ctx['categories'][$cat->category_id]->nbr_photo_user = 0;
            $ctx['categories'][$cat->category_id]->nbr_photo = 0;
            $ctx['categories'][$cat->category_id]->authors = array();
            
            // init propre du tableau de photos
            $photos[$cat->category_id] = array();
        }
        
        $result = $CI->db->query($sql)->result();
        foreach ($result as $row)
        {
            // On augmente le nombre de photo inscrite (valide) dans la catégorie concernée
            if ( $row->error == null)
            {
            	$ctx['categories'][$row->category_id]->nbr_photo += 1;
            }

            // On ajoute l'autheur si il ne l'a pas déjà été
            if (!in_array($row->user_id, $ctx['categories'][$row->category_id]->authors)) $ctx['categories'][$row->category_id]->authors[] = $row->user_id;
            
            // Si c'est une photo de l'utilisateur en cours, on incrémente son compteur
            if ($row->user_id == $user->user_id)
            {
                $ctx['categories'][$row->category_id]->nbr_photo_user += 1;
                $havePhoto = true;
            }
            
            // On stocke les infos de la photo
            $photos[$row->category_id][$row->photo_id] = $row;
        }

/*
        // Données générales sur les catégories et sur l'état de la participation aux AGPA
        foreach($ctx['categories'] as $cat)
        {
            if ($cat->category_id > 0)
            {
                $id = $cat->category_id;
                $order = $ctx['categories'][$id]->order;
                
                $ctx['cat'.$order.'_id']                   = $ctx['categories'][$id]->category_id;
                $ctx['cat'.$order.'_name']                 = ($ctx['categories'][$id]->has_variants) ? $ctx['categories'][$id]->title . ' : ' . $ctx['categories'][$id]->vtitle : $ctx['categories'][$id]->title;
                $ctx['cat'.$order.'_color']                = $ctx['categories'][$id]->color;
                $ctx['cat'.$order.'_user_participation']   = $ctx['categories'][$id]->nbr_photo_user;
                $ctx['cat'.$order.'_global_participation'] = $ctx['categories'][$id]->nbr_photo;
            }
        }
*/

        // on met à jour le contexte avec les nouvelles infos
        $ctx['have_photos'] = $havePhoto;
        $ctx['photos'] = $photos;


        return $ctx;
    }
}


/**
 * initPhotosAwardsData
 * Comme la méthode initPhotosData, crée le tableau des photos pour une édition donnée
 * mais les photos seront classées en fonction de leur note et les infos relatives aux awards seront données
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @param $user [array], les informations relatives à l'utilisateur.
 * @param $year, l'année de l'édition
 * @return $ctx [array] le contexte mis à jour avec le tableau récapitulatif des photos de l'edition des AGPA (triées par catégories)
 */
if ( ! function_exists('initPhotosAwardsData'))
{
    function initPhotosAwardsData(&$ctx, $user, $year)
    {        
        $CI = get_instance();
        $havePhoto = false; // est-ce que l'utilisateur a posté des photos ?
        $photos = array();  // l'ensemble des photos du concours (de l'édition en cours)
        $sql = "SELECT P.*, U.username, A.award, A.category_id as 'award_category_id' FROM agpa_photos P INNER JOIN absg_users U ON U.user_id = P.user_id LEFT JOIN agpa_awards A ON A.photo_id = P.photo_id WHERE P.year=$year ORDER BY P.category_id ASC, P.g_score DESC";
        
        
        // On ajoute des informations sur la participation aux catégories
        foreach($ctx['categories'] as $cat)
        {
            $ctx['categories'][$cat->category_id]->nbr_photo = 0;
            $ctx['categories'][$cat->category_id]->authors = array();
            
            // init propre du tableau de photos
            $photos[$cat->category_id] = array();
        }
        
        $result = $CI->db->query($sql)->result();
        foreach ($result as $row)
        {
			// On vérifie que la photo n'est pas déjà enregistré (peux arriver si la photo à plusieurs award (Agpa bronze + meilleur titre par exemple)
			if (!isset($photos[$row->category_id][$row->photo_id]))
			{
				// On augmente le nombre de photo inscrite dans la catégorie concernée
				$ctx['categories'][$row->category_id]->nbr_photo += 1;
				
				// On ajoute l'autheur si il ne l'a pas déjà été
				if (!in_array($row->user_id, $ctx['categories'][$row->category_id]->authors)) $ctx['categories'][$row->category_id]->authors[] = $row->user_id;
				
				// On reformate les infos des awards (tableau car peut en avoir plusieurs)
				$award = array();
				if ($row->award != null)
				{
					$award[convertCatIdToCssId($row->award_category_id)] = $row->award;
				}
				unset($row->award_category_id);
				$row->award = $award;
				
				// On stocke les infos de la photo
				$photos[$row->category_id][$row->photo_id] = $row;
            }
            else
            {
				// on ajoute l'award 
				$photos[$row->category_id][$row->photo_id]->award[convertCatIdToCssId($row->award_category_id)] = $row->award;
            }
        }

        // on met à jour le contexte avec les nouvelles infos
        $ctx['have_photos'] = $havePhoto;
        $ctx['photos'] = $photos;


        return $ctx;
    }
    
    
    function convertCatIdToCssId($cat_id)
    {
		$result = $cat_id;
		
		switch($cat_id)
		{
			case '-3':
				$result = 'x3';
				break;
			case '-2':
				$result = 'x2';
				break;
			case '-1':
				$result = 'x1';
				break;
		}
		
		return $result;
    }
}


/**
 * sufflePhotos
 * attribut un numéro aléatoire aux photos d'une même édition
 * et met à jour en même temps ce tableau avec les numéros générés
 *
 * @param $photos [array],  les photos de l'éditions, triés par catégories.
 * @param $user [array], les informations relatives à l'utilisateur.
 * @param $year, l'année de l'édition
 * @return $ctx [array] le contexte mis à jour avec le tableau récapitulatif des photos de l'edition des AGPA (triées par catégories)
 */
if ( ! function_exists('sufflePhotos'))
{
    function sufflePhotos(&$photos)
    {
        $CI = get_instance();

        $newPhotos = array();
        foreach($photos as $categorie)
            foreach($categorie as &$photo)
            {
                $newPhotos[] = $photo;
            }

        // Mélanger
        shuffle($newPhotos);
        $num = 1;

        // Maj MySQL
        $sql = "";
        foreach($newPhotos as &$photo)
        {
            $sql = "UPDATE agpa_photos SET number = '{$num}' WHERE `photo_id` = {$photo->photo_id};";
            $photo->number = $num;
            $CI->db->query($sql);
            ++$num;
        }
       

    }
}


















/**
 * showRules
 * Affiche le réglement en ligne. L'essentiel du réglement est écrit directement dans le template chargé... 
 * Cette fonction s'occupe essentiellement de calculer les dates afin que le réglement colle avec l'édition courante.
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @param $user [array], les informations relatives à l'utilisateur.
 * @param $year, l'année de l'édition
 * @return $ctx [array] le contexte mis à jour avec les dates de l'année courante
 */
if ( ! function_exists('showRules'))
{
    function showRules(&$ctx, $user, $year)
    {
        
        $days = array('lundi ', 'mardi ', 'mercredi ', 'jeudi ', 'vendredi ', 'samedi ', 'dimanche ');
        $phase1End   = mktime(12,3,3,$ctx['phases_boundaries'][1][1],$ctx['phases_boundaries'][1][0]-1,$year); // moins 1 car (" on inclus" la journée ds le texte du réglement")
        $phase2Start = mktime(12,3,3,$ctx['phases_boundaries'][1][1],$ctx['phases_boundaries'][1][0]  ,$year);
        $phase2End   = mktime(12,3,3,$ctx['phases_boundaries'][2][1],$ctx['phases_boundaries'][2][0]-1,$year);
        $phase3Start = mktime(12,3,3,$ctx['phases_boundaries'][2][1],$ctx['phases_boundaries'][2][0]  ,$year);
        $phase3End   = mktime(12,3,3,$ctx['phases_boundaries'][3][1],$ctx['phases_boundaries'][3][0]  ,$year);
        
        
        // on met à jour le contexte avec les nouvelles infos
        $ctx['prev_year']     = $year -1;
        $ctx['p1_end_date']   = $days[date('N',$phase1End)-1].date('j',$phase1End);
        $ctx['p2_start_date'] = $days[date('N',$phase2Start)-1].date('j',$phase2Start);
        $ctx['p2_end_date']   = $days[date('N',$phase2End)-1].date('j',$phase2End);
        $ctx['p3_start_date'] = $days[date('N',$phase3Start)-1].date('j',$phase3Start);
        $ctx['p3_end_date']   = $days[date('N',$phase3End)-1].date('j',$phase3End);
        
        

        return $ctx;
    }
}







/**
 * actualPhase1
 * Gère la phase 1 de l'édition actuelle : Enregistrement / Edition de ses photos
 * @param $ctx [array], le contexte des agpa
 * @param $user [array], les informations relative à l'utilisateur
 * @return $ctx [array], le contexte mis à jour les informations nécessaire à l'affichage de la phase 1.
 */
if ( ! function_exists('actualPhase1'))
{
    function actualPhase1($ctx, $user)
    {
        // Pour chaques catégories
        foreach($ctx['categories'] as $k => $categorie)
        {
            if ($categorie->category_id < 0) continue;

            $catId = $categorie->category_id;

            // On affiche les photos dans les slots
            $numPhoto = 0;
            foreach($ctx['photos'][$catId] as $photo)
            {
                if ($photo->user_id != $user->user_id) continue;
                ++$numPhoto ;

                $categorie->photos[] =  array(
                    'num'            => $numPhoto,
                    'empty'          => false,
                    'id'             => $photo->photo_id,
                    'url_hd'         => base_url() . 'assets/img/agpa/'.$photo->year.'/'.$photo->filename,
                    'url_fullscreen' => base_url() . 'assets/img/agpa/'.$photo->year.'/mini/'.$photo->filename,
                    'url_thumb'      => base_url() . 'assets/img/agpa/'.$photo->year.'/mini/vignette_'.$photo->filename,
                    'title'          => $photo->title
                );

                $ctx['categories'][$k] = $categorie;
            }
            
            for ($slot = $numPhoto+1; $slot <= 2 ; $slot++)
            {
                $categorie->photos[] =  array(
                    'num'    => $slot,
                    'empty'  => true
                );
                $ctx['categories'][$k] = $categorie;
            }
        }

        return $ctx;
    }
}


/**
 * actualPhase2Resume
 * Résume la phase 2 du concours. Si pas de catégorie d'indiquée, on affiche 
 * le résumé, ainsi que la liste des photos posant problèmes...
 *
 * @param $ctx [array], le contexte des agpa
 * @param $user [array], les informations relative à l'utilisateur
 * @return $ctx [array], le contexte mis à jour les informations nécessaire à l'affichage du résumé de la phase 2.
 */
if ( ! function_exists('actualPhase2Resume'))
{
    function actualPhase2Resume(&$ctx, $user)
    {
        $badPhotos = array();

        // Rechercher les mauvaises photos
        $badPhotos = array();
        foreach($ctx['photos'] as $cat)
            foreach($cat as $photo)
                if ($photo->error != NULL) $badPhotos[] = $photo;

        $ctx['have_photos_error'] = !empty($badPhotos);

        if  (!empty($badPhotos))
        {
            $ctx['photos_error'] = $badPhotos;
        }
    }
}




/**
 * actualPhase3Resume
 * Résume la phase 3 du concours. On resumes les categories de l'annee 
 * actuelle avec d'affiche pour chacune les 3 votes
 *
 * @param $ctx [array], le contexte des agpa
 * @param $user [array], les informations relative à l'utilisateur
 * @return $ctx [array], le contexte mis à jour les informations nécessaire à l'affichage du résumé de la phase 2.
 */
if ( ! function_exists('actualPhase3Resume'))
{
    function actualPhase3Resume(&$ctx, $user)
    {
        $CI = get_instance();
        $ctx['photos'][-3] = array();
        


    // 1- Informations participations par categories
        foreach($ctx['categories'] as $cat)
        {
            if ($cat->category_id > 0)
            {
                $ctx['categories'][$cat->category_id]->star_used = 0;
                $ctx['categories'][$cat->category_id]->star_available = 0;
                $ctx['categories'][$cat->category_id]->star_ok = false;
                
                foreach($ctx['photos'][$cat->category_id] as $p)
                {
		    if ($p->error === null)
		    $ctx['categories'][$cat->category_id]->star_available ++;
                }
                $ctx['categories'][$cat->category_id]->star_available = round($ctx['categories'][$cat->category_id]->star_available / 2, 0);
                
            }
            if ($cat->category_id == -3)
            {
                $ctx['categories'][-3]->feather = 0;
                $ctx['categories'][-3]->feather_ok = false;
            }
        }

    // 2- On recupere les votes du membre ainsi que les photos qui y sont liee
        $sql = "SELECT p.*, v.score as `user_vote` FROM agpa_votes v, agpa_photos p 
            WHERE v.year={$ctx['current_phase_year']}
                AND v.user_id={$user->user_id}
                AND v.photo_id=p.photo_id
            ORDER BY category_id ASC, user_vote DESC ";
        $votes = array();
        $result = $CI->db->query($sql)->result();
        foreach ($result as $photo) 
        {
            // cas général
            if ($photo->user_vote > 0)
            {
                $ctx['categories'][$photo->category_id]->star_used += $photo->user_vote;
                $ctx['photos'][$photo->category_id][$photo->photo_id]->user_vote = $photo->user_vote;
                if ($ctx['categories'][$photo->category_id]->star_used >= $ctx['categories'][$photo->category_id]->star_available / 2) 
                {
                    $ctx['categories'][$photo->category_id]->star_ok = true;
                }
            }
            // cas meilleur titre
            else if ($photo->user_vote == 0)
            {
                ++$ctx['categories'][-3]->feather;
                $ctx['photos'][-3][] = $photo;
                $ctx['photos'][$photo->category_id][$photo->photo_id]->title_selection = true;
                if ($ctx['categories'][-3]->feather >= ($ctx['max_feather']/2) && $ctx['categories'][-3]->feather <= $ctx['max_feather']) 
                {
                    $ctx['categories'][-3]->feather_ok = true;
                }
            }
        }
    }
}


/**
* actualPhase4DeliberationsEngine
* Effectue la série d'action nécessaire (étape par étape) pour calculer les points
* de chaques de photos et leur attribuer les récompenses en départageant les exaequos
* @param $ctx [array], le contexte des agpa
* @param $user [array], les informations relative à l'utilisateur
* @param $checkStep [int], l'étape à laquelle on arrête le processus afin de permettre de le suivre étape par étape
*/
if ( ! function_exists('actualPhase4DeliberationsEngine'))
{
    function actualPhase4DeliberationsEngine(&$ctx, $user, $checkStep)
    {
    // 1- Récupérer les votes et les vérifier
        $votes = checkVotes($ctx, (($checkStep == 1)? true : false));
        
        // Est-ce qu'il faut arrêter là ou continuer ?
        if ($checkStep == 1) return;

    // 2- Comptabiliser les votes correctes et calculer les notes pour chaque photo
        $categories = computeNotes($ctx, $votes, (($checkStep == 2)? true : false));

        // Est-ce qu'il faut arrêter là ou continuer ?
        if ($checkStep == 2) return;

    // 3- Attributions AGPA et création palmares
        // evaluation des notes et classement des photos / photographes
        // attribution des AGPA (or,argent,bronze)
        $evalResult = evalNote($ctx, $categories, (($checkStep == 3)? true : false));
        //printCategoriesArray($evalResult);

        // Est-ce qu'il faut arrêter là ou continuer ?
        if ($checkStep == 3) return;

    // 4- Attribution des AGPA de diamant
        $finalResult = deliverAwards($ctx, $evalResult, (($checkStep == 4)? true : false));
        //printCategoriesArray($finalResult);

        // Est-ce qu'il faut arrêter là ou continuer ?
        if ($checkStep == 4) return;

    // 5- Clore les stats pour l'édition actuelle (maj bdd)
        closeEdition($ctx, $finalResult);
    }
}



/**
* actualPhase5Resume
* Résumé des récompenses obtenu cette année (seulement les AGPA or/diamant)
* @param $ctx [array], le contexte des agpa
* @param $user [array], les informations relative à l'utilisateur
*/
if ( ! function_exists('actualPhase5Resume'))
{
    function actualPhase5Resume(&$ctx, $user)
    {
		$CI = get_instance();
        $ctx['photos'][-3] = array();


    // 1- Informations participations par categories
        /*foreach($ctx['categories'] as $cat)
        {
            if ($cat->category_id > 0)
            {
                $ctx['categories'][$cat->category_id]->star_used = 0;
                $ctx['categories'][$cat->category_id]->star_available = round(count($ctx['photos'][$cat->category_id]) / 2, 0);
            }
            else if ($cat->category_id == -1)
            {
                $ctx['categories'][-3]->feather = 0;
            }
            else if ($cat->category_id == -3)
            {
                $ctx['categories'][-3]->feather = 0;
            }
        }*/
/*
    // 2- On recupere les votes du membre ainsi que les photos qui y sont liées
        $sql = "SELECT p.*, v.score as `user_vote` FROM agpa_votes v, agpa_photos p 
            WHERE v.year={$ctx['current_phase_year']}
                AND v.user_id={$user->user_id}
                AND v.photo_id=p.photo_id
            ORDER BY category_id ASC, user_vote DESC ";
        $votes = array();
        $result = $CI->db->query($sql)->result();
        foreach ($result as $photo) 
        {
            // cas général
            if ($photo->user_vote > 0)
            {
                $ctx['categories'][$photo->category_id]->star_used += $photo->user_vote;
                $ctx['photos'][$photo->category_id][$photo->photo_id]->user_vote = $photo->user_vote;
                if ($ctx['categories'][$photo->category_id]->star_used >= $ctx['categories'][$photo->category_id]->star_available / 2) 
                {
                    $ctx['categories'][$photo->category_id]->star_ok = true;
                }
            }
            // cas meilleur titre
            else if ($photo->user_vote == 0)
            {
                ++$ctx['categories'][-3]->feather;
                $ctx['photos'][-3][] = $photo;
                $ctx['photos'][$photo->category_id][$photo->photo_id]->title_selection = true;
                if ($ctx['categories'][-3]->feather >= 4 && $ctx['categories'][-3]->feather <= 8) 
                {
                    $ctx['categories'][-3]->feather_ok = true;
                }
            }
        }
        */
        
		return;
    
        $CI = get_instance();
        // 1- On récupère les données de l'édition terminée
        $sql = "SELECT * FROM agpa_awards WHERE year = {$ctx['current_phase_year']}  ORDER BY category ASC, award ASC";
        $result = $CI->db->query($sql)->result();
        $infosEdition = array();
        foreach ($result as $row) 
        {
            $infosEdition[$row->category][$row->award] = $row;
        }
        
        // 2- On recupere toutes les données sur les photos de cette année la
        //$AGPA_PHOTOS = initAGPA($ctx['current_phase_year']);
        
        foreach ($infosEdition as $catId => $categoryInformations)
        {
            if ($catId == -1) // AGPA meilleur photographe
            {
                if (isset($categoryInformations['diamant']))
                {
                    analyseHC1( $categoryInformations['diamant'] );
                }
                else
                {
                    analyseHC1( $categoryInformations['or'] );
                }
            }
            else if ($catId == -2) // AGPA de la meilleur photo
            {
                if (isset($categoryInformations['diamant']))
                {
                    analyseHC2( $categoryInformations['diamant'] );
                }
                else
                {
                    analyseHC2( $categoryInformations['or'] );
                }
            }
            else // categorie normal
            {
                
                
                // afficher les 3 meilleurs photos (ordre avec lequel on appel analyseSC est important)
                if (isset($categoryInformations['diamant']))
                {
                    analyseSC( $categoryInformations['diamant'] );
                }
                else
                {
                    analyseSC( $categoryInformations['or'] );
                }
                analyseSC( $categoryInformations['argent'] );
                analyseSC( $categoryInformations['bronze'] );
            }
        }
    }
}





/**
* ceremonyOnline
* La cérémonie de remise des AGPA online
* @param $ctx [array], le contexte des agpa
* @param $category [int], la catégorie en cours
* @param $step [int] l'étape courante : 0=Présentation, 1=nominés, 2=Bronze, 3=argent, 4=or/diamant
*/
if ( ! function_exists('ceremonyOnline'))
{
    function ceremonyOnline(&$ctx, $year, $category, $step)
    {
		$CI = get_instance();
		$data = array();
		
		// On récupère les données
		$sql  = "SELECT a.year, a.award, a.photo_id, a.category_id, p.title, p.filename, p.ranking,  u.user_id, u.username, up.rootfamilly ";
		$sql .= "FROM agpa_awards a ";
		$sql .= "INNER JOIN agpa_categories c ON a.category_id=c.category_id ";
		$sql .= "INNER JOIN absg_users u ON a.author_id=u.user_id ";
		$sql .= "INNER JOIN agenda_people up ON u.people_id=up.people_id ";
		$sql .= "LEFT JOIN agpa_photos p ON a.photo_id = p.photo_id ";
        
        // On détermine la date limite
		$maxYear = date("Y");
		if ($ctx['current_phase'] <= 4) $maxYear--;
	    
        // On affiche le palmarès de quelle année ?    
		if ($year <= 0 || $year > $maxYear)
		{
			$year = $maxYear;
		}
		$sql .= "WHERE a.year=$maxYear AND a.category_id=$category ";
		$sql .= "ORDER BY  p.ranking ASC";
	
		// On récupère les données de l'édition terminée
        $data = $CI->db->query($sql)->result();

		
		return $data;
	}
	
}








/**
 * analyseHC1
 * Effectue l'analyse necessaire pour afficher le resume du Hors Categorie (-1)
 * En profite pour retourner le nombre de participant
 *
 * @param $infos,   [array] les donnees de la categorie HC1 (meilleur photographe) 
 *                  a analyser
 * 
 */
if ( ! function_exists('analyseHC1'))
{
    function analyseHC1( $infos)
    {
        global $template, $AGPA_MEMBERS, $AGPA_CATEGORIES, $AGPA_PHOTOS;

        // Get Avatar of the winner
        $avatar = ''; 
        $avatar_type = $AGPA_MEMBERS[$infos['author']]['user_avatar_type'];
        if ( $avatar_type == 3 )
            $avatar = "{$phpbb_root_path}images/avatars/gallery/".$AGPA_MEMBERS[$infos['author']]['user_avatar'];
        else
            $avatar = "{$phpbb_root_path}images/avatars/upload/".$AGPA_MEMBERS[$infos['author']]['user_avatar'];
        
        // Compter le nombre de photos du photographes et les points récoltés
        $photosScore = 0;
        $photosNumber = 0;
        foreach($AGPA_PHOTOS as $catId => $category)
            foreach($category as $photo)
            {
                if ($photo['user_id'] == $infos['author'] && $catId != -2)
                {
                    $photosScore += $photo['score'];
                    ++$photosNumber;
                }
            }

        $template->assign_vars(array(
            'PHOTOGRAPHER_AVATAR'     => $avatar,
            'PHOTOGRAPHER_AWARD'      => ($infos['award'] == 'diamant') ? 'AGPA de diamant' : 'AGPA d\'or',
            'PHOTOGRAPHER_NAME'       => $AGPA_MEMBERS[$infos['author']]['username'],
            'PHOTOGRAPHER_NBR_PHOTOS' => $photosNumber,
            'PHOTOGRAPHER_SCORE'      => $photosScore)
        );
    }
}


/**
 * analyseHC2
 * Effectue l'analyse necessaire pour afficher le resume du Hors Categorie (-2)
 * En profite pour retourner le nombre de photos postees
 *
 * @param $infos,   [array] les donnees de la categorie HC1 (meilleur photographe) 
 *                  a analyser
 */
if ( ! function_exists('analyseHC2'))
{
    function analyseHC2( $infos)
    {
        global $template, $AGPA_MEMBERS, $AGPA_CATEGORIES, $AGPA_PHOTOS;

        // retrieve Photography
        $photo;
        foreach($AGPA_PHOTOS as $category)
        {
            if (isset($category[$infos['photo']]))
            {
                $photo = $category[$infos['photo']];
            }
        }

        // Set template data
        $template->assign_vars(array(
            'PHOTOGRAPHY_URL_FULLSCR'  => AGPA_PATH_PHOTOS.$infos['year'].'/mini/'.$photo['filename'],
            'PHOTOGRAPHY_URL_THUMB'    => AGPA_PATH_PHOTOS.$infos['year'].'/mini/vignette_'.$photo['filename'],
            'PHOTOGRAPHY_TITLE'        => $photo['title'],
            'PHOTOGRAPHY_AWARD'        => ($infos['award'] == 'diamant') ? 'AGPA de diamant' : 'AGPA d\'or',
            'PHOTOGRAPHY_AUTHOR'       => $AGPA_MEMBERS[$infos['author']]['username'],
            'PHOTOGRAPHY_SCORE'        => $photo['score'])
        );
    }
}


/**
 * analyseSC
 * Effectue l'analyse necessaire pour afficher le resume d'une Categorie simple (1 a 6 par exemple)
 *
 * @param $infos      [array]   les infos sur la photos nominées
 * 
 * @return -
 */
if ( ! function_exists('analyseSC'))
{
    function analyseSC( $infos )
    {
        global $template, $AGPA_MEMBERS, $AGPA_CATEGORIES, $AGPA_PHOTOS;
        
        $photo = $AGPA_PHOTOS[$infos['category']][$infos['photo']];
        $place = array('diamant' => 1, 'or' => 1, 'argent' => 2, 'bronze' => 3);
        // contruction du template !
        $template->assign_block_vars('categories.photos', array(
            'LAST'          => ($infos['award'] == 'bronze') ? true : false,
            'AWARD'         => $infos['award'],
            'URL_THUMB'     => AGPA_PATH_PHOTOS.$infos['year'].'/mini/vignette_'.$photo['filename'],
            'URL_FULLSCR'   => AGPA_PATH_PHOTOS.$infos['year'].'/mini/'.$photo['filename'],
            'URL_ORIGINAL'  => AGPA_PATH_PHOTOS.$infos['year'].'/'.$photo['filename'],
            'RESOLUTION'    => $photo['resolution'],
            'WEIGHT'        => $photo['weight'],
            'TITLE'         => $photo['title'],
            'AUTHOR'        => $AGPA_MEMBERS[$infos['author']]['username'],
            'SCORE'         => $photo['score'].' ('.$photo['votes'].' vote'.(($photo['votes']>1)?'s':'').')',
            'PLACE'         => $place[$infos['award']])
        );
    }
}





















// PALMARES -----------------------------------------------------------------------------------------------------------------------------------------





/**
 * getPalmaresData
 * Construit la requête sql qui permettra de récupérer les infos palmarès a afficher en fonction des filtres
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @param $filter_user      [dynamic]   si int : user_id (0 = current user)
 *					si string : { 'gueudelot', 'guyomard', 'guibert'}
 * @param $filter_year	    [dynamic]   si int : year (0 = all)
 *                                        
 * 
 * @return [array] 
 */
if ( ! function_exists('getPalmaresData'))
{
    function getPalmaresData(&$ctx, $filter_user, $filter_year )
    {
        $CI = get_instance();
		$data = array();
		
		

		
		// On récupère les données
		$sql  = "SELECT a.year, a.award, a.photo_id, a.category_id, p.title, p.filename, u.user_id, u.username, up.rootfamilly ";
		$sql .= "FROM agpa_awards a ";
		$sql .= "INNER JOIN agpa_categories c ON a.category_id=c.category_id ";
		$sql .= "INNER JOIN absg_users u ON a.author_id=u.user_id ";
		$sql .= "INNER JOIN agenda_people up ON u.people_id=up.people_id ";
		$sql .= "LEFT JOIN agpa_photos p ON a.photo_id = p.photo_id ";
		
		// On affiche les palmarès de qui ?
        if ($filter_user == 'gueudelot' || $filter_user == 'guibert' || $filter_user == 'guyomard')
        {
			$sql .= "WHERE up.rootfamilly='$filter_user'";
			$data['palmaresUserData'] = array();
			$data['palmaresUserData']['displayAuthor'] = true;
			$data['palmaresUserData']['username'] = ucfirst($filter_user);
			$data['palmaresUserData']['fromUser'] = 'de la famille ' . ucfirst($filter_user);
			$data['palmaresUserData']['avatar'] = '';
			$data['palmaresUserData']['forYear'] = ($filter_year === 0) ? '' : $filter_year;
        }
        else
        {
			$user_id = ($filter_user > 0) ? $filter_user : $user->user_id;
			$sql .= "WHERE author_id=$user_id ";
        }
        
        
        // On détermine la date limite
		$maxYear = date("Y");
		if ($ctx['current_phase'] <= 4) $maxYear--;
			
			
			// On affiche le palmarès de quelle année ?    
		if ($filter_year > 0 && $filter_year <= $maxYear)
		{
			$sql .= "AND a.year=$filter_year ";
		}
		else
		{
			$sql .= "AND a.year<=$maxYear ";
			$filter_year = 0;
		}
		$sql .= "ORDER BY a.category_id ASC, a.year ASC";
		$result = $CI->db->query($sql)->result();
			
		// On parse les résultats pour construire : le tableau résumé, calculer les scores totaux et partiels, ainsi que le tableau par catégories
		$resume = array();
		foreach ($CI->ctx['categories'] as $cat)
		{
			$resume[$cat->category_id] = array('totalAgpa' => 0, 'totalPoints' => 0);
		}
		
		$totalPoints = 0;
		foreach ($result as $row)
		{
			if (!isset($resume[$row->category_id][$row->award]))
			{
				$resume[$row->category_id][$row->award] = array();
			}
			$resume[$row->category_id][$row->award][] = array('year' => $row->year, 'photo_id' => $row->photo_id, 'filename' => $row->filename, 'title' => $row->title, 'user_id' => $row->user_id, 'username' => $row->username, 'avatar' => $CI->layout->asset_avatar_url($row->user_id));
			
			$resume[$row->category_id]['totalPoints'] += getPalmaresPoint($row->category_id, $row->award);
			$totalPoints += getPalmaresPoint($row->category_id, $row->award);
			if ($row->award != 'lice')
			{
				$resume[$row->category_id]['totalAgpa'] ++; 
			}
			
			if (!isset($data['palmaresUserData']))
			{
				$data['palmaresUserData'] = array();
				$data['palmaresUserData']['displayAuthor'] = false;
				$data['palmaresUserData']['forYear'] = ($filter_year === 0) ? '' : $filter_year;
				$data['palmaresUserData']['username'] = $row->username;
				$data['palmaresUserData']['fromUser'] = fromUsername($row->username);
				$data['palmaresUserData']['avatar'] = $CI->layout->asset_avatar_url($row->user_id);
			}
		}
			
			
		$data['filterYear'] = $filter_year;
		$data['maxYear'] = $maxYear;
		$data['resumeTotal'] = $totalPoints;
		$data['resume'] = $resume;
        
        return $data;
    }
}




/**
 * buildPalmaresMenu
 * Construit la requête sql qui permettra de récupérer les infos palmarès a afficher en fonction des filtres
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @param $feature 	[string]	
 * @param $filter_user  [dynamic]	si int : user_id (0 = current user)
 *			 		si string : { 'gueudelot', 'guyomard', 'guibert'}
 * @param $filter_year	[int]		si int : year (0 = all)
 *                                        
 * 
 * @return [array] 
 */
if ( ! function_exists('buildPalmaresMenu'))
{
    function buildPalmaresMenu(&$ctx, $feature, $filter_user, $filter_year )
    {
        $CI = get_instance();
		$menu = array(
			'features' => array(
				'ranking' => 'Classement',
				'palmares' => 'Palmares'
			),
			'select' => array(
				'features'=> $feature,
				'userFilter' => $filter_user,
				'yearFilter' => $filter_year
			)
		);
	
		// On construit le menu user
		$members = array();
		foreach($ctx['members'] as $member)
		{
			if (!empty($member->rootfamilly))
			{
				$members[$member->rootfamilly][$member->user_id] = $member->username;
			}
		}
		
		asort($members['gueudelot']);
		asort($members['guibert']);
		asort($members['guyomard']);
		$menu['userFilter'] = $members;
		
		
		// On construit les années
		$menu['yearFilter'] = array(0=> 'Global');
		
		// On détermine la date limite
		$maxYear = date("Y");
		if ($ctx['current_phase'] <= 4) $maxYear--;
		
		for($i=$maxYear; $i>= 2006; $i--)
		{
			$menu['yearFilter'][$i] = $i;
		}
		
		return $menu;
    }
}






// ARCHIVES -----------------------------------------------------------------------------------------------------------------------------------------




/**
 * buildArchiveMenu
 * Récupère les infos a afficher pour le menu des archives
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @return $ctx [array] le contexte mis à jour avec les infos relatives aux anciennes éditions des agpas
 */
if ( ! function_exists('buildArchiveMenu'))
{
    function buildArchiveMenu(&$ctx)
    {
        $CI = get_instance();
		$data = array();

        // On détermine la date limite
        $maxYear = date("Y");
        if ($ctx['current_phase'] <= 4) $maxYear --;
        
		print_r($ctx);
        if ($year >= 2006 && $year < $maxYear)
        {
            // On recupere toutes les donnees sur les photos de cette annee la
            $AGPA_PHOTOS = initAGPA($year);

            // Donnees generales du template
            $l_title = 'AGPA - Les archives de l\'&eacute;dition '.$year;

            $navbar[2] = array('Edition '.$year, append_sid("{$phpbb_root_path}agpa.$phpEx", "section=archives&amp;year=$year"));
            $template->assign_vars(array('SOMMAIRE_ARCHIVES' => false));
            
            
            // Analyser les principaux resultat pour l'annee $annee
            $sql = "SELECT * FROM agpa_awards WHERE year = $year  ORDER BY category ASC, award ASC";
            $result = $db->sql_query($sql);
            
            // save informations about the edition
            $infosEdition = array();
            while ($row = $db->sql_fetchrow($result))
            {
                $infosEdition[$row['category']][$row['award']] = $row;
            }
            $db->sql_freeresult($result);
            
            // foreach category, fill data template and complete some extra statistics
            $usersNumber = 0;
            $photosNumber = 0;
            foreach ($infosEdition as $catId => $categoryInformations)
            {
                if ($catId == -1) // AGPA meilleur photographe
                {
                    if (isset($categoryInformations['diamant']))
                    {
                        analyseHC1( $categoryInformations['diamant'] );
                    }
                    else
                    {
                        analyseHC1( $categoryInformations['or'] );
                    }
                }
                else if ($catId == -2) // AGPA de la meilleur photo
                {
                    if (isset($categoryInformations['diamant']))
                    {
                        analyseHC2( $categoryInformations['diamant'] );
                    }
                    else
                    {
                        analyseHC2( $categoryInformations['or'] );
                    }
                }
                else // categorie normal
                {
                    $photosInTheCategory = 0;
                    $photosNumber += sizeof($AGPA_PHOTOS[$catId]);
                    
                    $template->assign_block_vars('categories', array(
                            'ID' => $catId,
                            'TITLE'       => $ctx['categories'][$catId]['title'],
                            'DESCRIPTION' => $ctx['categories'][$catId]['description'],
                            'NBR_PHOTOS'  => $photosNumber,
                            'SPECIAL'     => ($catId < 0) ? $catId : false)
                        );
                    
                    // afficher les 3 meilleurs photos (ordre avec lequel on appel analyseSC est important)
                    if (isset($categoryInformations['diamant']))
                    {
                        analyseSC( $categoryInformations['diamant'] );
                    }
                    else
                    {
                        analyseSC( $categoryInformations['or'] );
                    }
                    analyseSC( $categoryInformations['argent'] );
                    analyseSC( $categoryInformations['bronze'] );
                }

            }
            
            // TODO : récupérer le nombre de participant (requete SQL basique)
            
            $template->assign_vars(array(
                'EDITION_YEAR'     => $year,
                'NBR_PHOTOS'       => $photosNumber,
                'NBR_PHOTOGRAPHER' => $usersNumber)
            );
        }
        else 
        { 
            // si pas d'annee precise, on considere qu'il s'agit de l'accueil des archives.
            displayArchivesSummary(ctx, $page, 10);
        }
    }
}






/**
 * buildArchive
 * Récupère les infos a afficher pour le menu des archives
 *
 * @param $ctx [array], le contexte avec toutes les infos nécessaire.
 * @return $ctx [array] le contexte mis à jour avec les infos relatives aux anciennes éditions des agpas sélectionnée via les filtres
 */
if ( ! function_exists('buildArchive'))
{
    function buildArchive(&$ctx)
    {
        $CI = get_instance();

        // On détermine la date limite
        $maxYear = date("Y");
        if ($ctx['current_phase'] <= 4) $maxYear --;
        
        
        // On init les variables de filtre
        $filters = $ctx['filters'];
        $year = -1;
        $category = -1;
        $photographe = -1;
        $family = -1;
        $award = -1;
        

        // On récupère les data en fonction des filtres
		$sql = "SELECT p.*, a.rootfamilly, w.award FROM agpa_photos p 
		INNER JOIN absg_users u ON p.user_id = u.user_id
		INNER JOIN agenda_people a ON a.people_id = u.people_id
		LEFT JOIN agpa_awards w ON w.photo_id = p.photo_id ";
	
		// La condition Where va être conditioné par le premier filtre
    if (!isset($filters['f1_type']) || !isset ($filters['f1_value']))
    {
      $filters['f1_type'] = 'a';
      $filters['f1_value'] = $maxYear;
    }
   
		switch($filters['f1_type'])
    {
			case 'a':
				$year = $filters['f1_value'];
				$year = ($year >= 2006 && $year <= $maxYear) ? $year : $maxYear;
				$sql .= "WHERE p.year = $year ";
        
				break;
			case 'c':
				$category = $filters['f1_value'];
				$sql .= "WHERE p.category_id = $category ";
				break;
			case 'p':
				$photographe = $filters['f1_value'];
				break;
			case 'f':
				$family = $filters['f1_value'];
				break;
			case 'w':
				$award = $filters['f1_value'];
				break;
        }
    if (isset($filters['f2_type']) && isset ($filters['f2_value']))
    {
  		switch($filters['f2_type'])
  		{
  			case 'c':
  				$category = $filters['f2_value'];
  				$sql .= "ORDER BY p.category_id ASC";
  				break;
  			case 'p':
  				$photographe = $filters['f2_value'];
  				$sql .= "ORDER BY u.username ASC";
  				break;
  			case 'w':
  				$award = $filters['f2_value'];
  				$sql .= "ORDER BY p.g_score DESC";
  				break;
          
  			case 'a':
        default:
          $filters['f2_type'] = 'a';
  				$year = $filters['f2_value'];
  				$sql .= "ORDER BY p.year DESC";
  				break;
  		}
    }
			
		
		
		// On récupère les données
		$result = $CI->db->query($sql)->result();
		
		foreach($result as $row)
    {
			if (!isset($infosEditions[$row->year][-1]->winners))
			{
			  $infosEditions[$row->year][-1]->winners = array();
			}
			
      $infosEditions[$row->year][-1]->winners[] = $row;
    }
		
		// l'organisation des données est conditionné par les deux filtres
		
		
  }
}






/**
 * afficher_sommaire_archives
 * Affiche les résumés des éditions (sommaire des archives)
 *
 * @param $page,       [int] la page actuellement sélectionnée
 * @param $maxPerPage, [int] le nombre d'édition à afficher par page
 * 
 */
if ( ! function_exists('displayArchivesSummary'))
{
    function displayArchivesSummary(&$ctx, $page=0, $maxPerPage=10 )
    {
        $CI = get_instance(); 
		    $data = array();
        
        // On détermine la date limite
        $maxYear = date("Y");
        if ($ctx['current_phase'] <= 4) $maxYear --;
        
        
        // Les années des édition à afficher 
        $lastEdition = max($maxYear - ($page) * $maxPerPage, 2006);
        $oldestEdition = max(2006, $lastEdition - $maxPerPage);

        // récupérer les données
        $sql = "SELECT a.*, p.filename, p.title, p.photo_id FROM agpa_awards a
            LEFT JOIN agpa_photos p ON a.photo_id = p.photo_id
            WHERE a.year >= $oldestEdition AND a.year <= $lastEdition AND (a.award='diamant' OR a.award='or') 
            ORDER BY a.year DESC, a.category_id ASC, a.award ASC";
        $result = $CI->db->query($sql)->result();


        // save informations about the edition
        $infosEditions = array();
        foreach($result as $idx => $row)
        {
            $infosEditions[$row->year][$row->category_id] = $row;
        }

        // On récupère les données spécifiques pour les hors cat -1 (meilleure photographe)
        $sql = "SELECT * FROM agpa_awards 
            WHERE year <= $lastEdition AND year >= $oldestEdition AND award<>'lice' 
            AND category_id = -1 ORDER BY year DESC, category_id ASC";
        $result = $CI->db->query($sql)->result();
        
        
        foreach($result as $row)
        {
			if (!isset($infosEditions[$row->year][-1]->winners))
			{
				$infosEditions[$row->year][-1]->winners = array();
			}
			
            $infosEditions[$row->year][-1]->winners[] = $row;
        }
        
        // On met en forme les données pour l'affichage
        foreach($infosEditions as $year => $edition)
        {
			$data[$year] = array();
			$data[$year]['winners'] = array();
			
			foreach($edition[-1]->winners as $winner)
			{
				
				$data[$year]['winners'][orderAccordingToAward($winner->award)] = array(
					'name' => $ctx['members'][$winner->author_id]->username,
					'award' => $winner->award,
					'avatar' => $CI->layout->asset_avatar_url($winner->author_id)
				);
			}

			$data[$year]['bestPhoto_filename'] = $edition[-2]->filename;
			$data[$year]['bestPhoto_title'] = $edition[-2]->title;
			$data[$year]['diaporama'] = array();
			
			foreach($edition as $catId => $catData)
			{
				if ($catId > 0)
				{
					$data[$year]['diaporama'][] = array(
						'filename' => $catData->filename,
						'photoTitle' => $catData->title,
						'photoAward' => $catData->award,
						'category' => $catId
					);
				}
			}
        }

        return $data;
    }

    
    function orderAccordingToAward($award)
    {
		switch($award)
		{
			case 'diamand':
			case 'or':
				return 0;
				break;
			case 'argent':
				return 1;
				break;
			case 'bronze':
				return 2;
				break;
		}
		return 10;
    }
    
    
}




/**
 * buildArchiveSQLQuery
 * Construit la requete correspondant aux critere de selection et d'affichage voulus.
 * Analyse des filtres :
 * -> argument par paire : nom_filtre + valeur
 * Ordre des filtres tres important : 1 > 2 > 3 > ...
 *
 * @param $filters,       [array] le nom des filtres qu'on souhaite appliquer {year, author, category}
 * @param $values,        [array] la valeurs de ces filtres                   [int]  [int]   [int]
 * @param $display,       [array] references vers les donnees des membres  {year, author, category, score, number, award}
 *                                                                          desc  asc     asc       desc   asc     asc
 * 
 * @return [string1, string2n string3] 
 *                            string1 => la requete sql correctement construite
 *                            string2 => le texte a afficher décrivant les filtres appliquer pour sélectionner les photos 
 */
if ( ! function_exists('buildArchiveSQLQuery'))
{
    function buildArchiveSQLQuery(&$ctx, $filters)
    {
        $CI = get_instance(); 
		$data = array();
            
        // construction de la requete mysql
        $sql = "SELECT p.photo_id, p.year, p.category_id, p.user_id, p.filename, p.title, a.award FROM agpa_photos p LEFT JOIN agpa_awards a ON a.photo_id = p.photo_id ";
        //    WHERE a.year >= $oldestEdition AND a.year <= $lastEdition AND (a.award='diamant' OR a.award='or') 
        //    ORDER BY a.year DESC, a.category_id ASC, a.award ASC
        $result = $CI->db->query($sql)->result();
        
        // Selection des bonnes donnees
        $first = true;
        $titleFilters = '';
        $numberOfFilters = sizeof($filters);
        $displayFilter = array();
        $titleDisplay = '';
        

        // filter 1
        switch($filters['f1_type'])
        {
    			case 'a':
    				$year = $filters['f1_value'];
                    $maxYear = $ctx['current_year'];
    				$year = ($year >= 2006 && $year < $maxYear) ? $year : $maxYear - 1;
    				$sql .= "WHERE p.year = $year ";
            
                    $titleFilters .= 'Photos de l\'année '. $year;
                    $displayFilter[] = 'year';
    				break;
    			case 'c':
    				$category = $filters['f1_value'];
    				$sql .= "WHERE p.category_id = $category ";


                    $titleFilters .= 'Photos de la catégorie '.$ctx['categories'][$category]['title'];
                    $displayFilter[] = 'category';
    				break;
    			case 'p':
    				$photographe = $filters['f1_value'];

                    $titleFilters .= 'Photos du photographe ' . $ctx['members'][$photographe]['username'];
                    $displayFilter[] = 'author';
    				break;
    			case 'f':
    				$family = $filters['f1_value'];
                    $titleFilters .= 'Photos de la famille ' . $family;
                    $displayFilter[] = 'family';
    				break;
    			case 'w':
    				$award = $filters['f1_value'];
                    $titleFilters .= "AGPA d\'" . $award;
                    $displayFilter[] = 'award';
    				break;
        }
        if (isset($filters['f2_type']) && isset ($filters['f2_value']) && trim($filters['f2_type']) != '' && trim($filters['f2_value']) != '')
        {
      		switch($filters['f2_type'])
      		{
      			case 'c':
      				$category = $filters['f2_value'];
      				$sql .= "AND p.category_id = $category ";
                    $displayFilter[] = 'category';
                    $titleFilters .= ' de la catégorie '. $category;
      				break;
      			case 'p':
      				$photographe = $filters['f2_value'];
      				$sql .= "AND p.author = $photographe ";
                    $displayFilter[] = 'author';
                    $titleFilters .= ' de '. $photographe;
      				break;
      			case 'w':
      				$award = $filters['f2_value'];
      				$sql .= "todo ";
                    $displayFilter[] = 'award';
      				break;
              
      			case 'a':
                default:
                    $filters['f2_type'] = 'a';
      				$year = $filters['f2_value'];
      				$sql .= "AND p.year = $year";
                    $displayFilter[] = 'year';
      				break;
      		}
        }
        
        // Trie et affichage de la requete
        $sql_order = '';
        $first = true;
        foreach ( $displayFilter as $df)
        {
            switch($df)
            {
                case 'year':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' year ASC';
                    $first = false;
                break;
                case 'author':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' user_id ASC';
                    $first = false;
                break;
                case 'category':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' category_id ASC';
                    $first = false;
                break;
                case 'score':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' score DESC';
                    $first = false;
                break;
                case 'number':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' number ASC';
                    $first = false;
                break;
                case 'award':
                    if (!$first) $sql_order .= ', ';
                    $sql_order .= ' ranking ASC';
                    $first = false;
                break;
            }
        }
        
        // Quel que soit les filtres precedant, on termine toujours par ordonner en fonction du score puis du numero attribue a la photo
        if (!$first) { $sql_order .= ', '; }
        $sql_order .= 'score ASC, number ASC';
        
        
        $sql .= " ORDER BY $sql_order";
        return array($sql,$titleFilters) ;
    }
}


/**
 * buildArchiveView
 * trouve la vue a utiliser pour afficher les donnees en fonction des filtres choisis
 *
 * @param $filters,       [array] le nom des filtres qu'on souhaite appliquer {year, author, category}
 * @param $values,        [array] la valeurs de ces filtres                   [int]  [int]   [int]
 * @param $display,       [array] references vers les donnees des membres  {year, author, category, score, number, award}
 *                                                                          desc  asc     asc       desc   asc     asc
 * 
 * @return string,          le nom de la vue
 */
if ( ! function_exists('buildArchiveView'))
{
    function buildArchiveView(&$ctx, $filters)
    {
        $CI = get_instance(); 
        $data = array();
            
        $view = '';
        

        if (isset($filters['f2_type']) && trim($filters['f2_type']) != '')
        {
            $view = $filters['f2_type'];
        }
        else
        {
            $view = 'Menu';
        }

        if (isset($filters['f1_type']) && trim($filters['f1_type']) != '')
        {
            $view = $filters['f1_type'];
        }
        else
        {
            $view = 'a';
        }


        return $view ;
    }
}

