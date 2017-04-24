<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Immt extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index($page=0)
	{
		// Init layout
		$this->browse($page);
	}



	/**
	 * Parcourir les images du moment
	 */
	public function browse($page=0)
	{
		// Init layout
		$user = $this->layout->init("immt");
        $this->layout->addCss("immt");
        $this->layout->addJs("immt");
		$this->layout->addPlugin('lightbox');
		$data = array();

		// 1) On récupère le nombre total de citation
		$nbrPerPage = 30;
		$sql = 'SELECT Count(*) as "t" FROM immt';

		$data['newFormAction'] = base_url() . 'immt/newImmt';
		$data['totalImmt'] = $result = $this->db->query($sql)->result()[0]->t;
		$data['totalPage'] = ceil($data['totalImmt'] / $nbrPerPage);
		$data['currentPage'] = $page;


		// 2) On met a jour les infos d'activité pour supprimer les notifications
		updateUserActivity($user->user_id, 'immt', $data['totalImmt']);
		$user->notifications['immt'] = 0;




		// 3) On construit la requête sql avec les paramètres
		$sql = 'SELECT i.* ,u.username FROM  immt i INNER JOIN  absg_users u ON  i.user_id = u.user_id';
		$sql .= ' ORDER BY i.year DESC, i.day DESC LIMIT ' . ($page * $nbrPerPage) .', ' . $nbrPerPage;


		// 4) On récupère les immt et on les affiches
		$immtList = array();
		$result = $this->db->query($sql)->result();
		foreach ($result as $immt)
        {
        	$date = $this->getDateFromDay($immt->day, $immt->year);
        	$immt->dateLabel = $immt->username . " le " . $this->layout->displayed_date($date, 'date');
            $immtList[] = $immt;

        }
        $data['immt'] = $immtList;





        $this->layout->footer('immt/stats', $this->stats($user));
		$this->layout->view('immt/browse', $data);
	}



	/**
	 * Enregistre la nouvelle immt via POST
	 */
	public function newImmt()
	{
		// On récupère quelques infos de base
        $user = $this->layout->init("immt");
		$this->load->helper('image_helper');

		// Definitions des constantes importantes
		$PATH_PHOTOS = __DIR__ . '/../../assets/img/immt/';
		$PATH_MINIS  = $PATH_PHOTOS .'mini/';
		$URL_PHOTOS = base_url() . 'assets/img/immt/';
		$URL_MINIS = $URL_PHOTOS . 'mini/';
		$HMAX_FS = 950;
		$LMAX_FS = 1200;
		$HMAX_V  = 150;



        // On part du principe que tout c'est bien passe, et si ya un probleme, on changera a ce moment le msg d'erreur
		$msg = "";
		$error = false;
		// Verification de l'upload
		if ($_FILES['newImage']['error'] === 0)
		{
			$tmpImageFullname = $_FILES['newImage']['tmp_name'];
		}
		elseif ($_FILES['newImage']['error'])
		{
			$msg = '<span class="error"><b>Erreur : </b>';
			switch($_FILES['newImage']['error'])
			{
				case 1: // UPLOAD_ERR_INI_SIZE
				$msg .= 'le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !';
				break;
				case 2: // UPLOAD_ERR_FORM_SIZE
				$msg .= 'le fichier dépasse la limite autorisée dans le formulaire HTML !';
				break;
				case 3: // UPLOAD_ERR_PARTIAL
				$msg .= 'l&lsquo;envoi du fichier a été interrompu pendant le transfert !';
				break;
				case 4: // UPLOAD_ERR_NO_FILE
				$msg .= 'le fichier que vous avez envoyé a une taille nulle !';
				break;
			}
			$msg .= '</span>'.print_r($_FILES);
			$error = true;
		}
		else // sinon c'est qu'il n'y a pas eu d'upload
		{
			$msg = '<span class="error"><b>Erreur : </b>vous n&lsquo;avez envoy&eacute; aucun document...</span>';
			$error = true;
		}
		
		
		// Controle du type de l'image
		if (!$error)
		{
			$objImgTmp = ImageFactory::factory($tmpImageFullname);
			if ($objImgTmp === false) { 
				unlink($tmpImageFullname); 
				$msg = '<span class="error"><b>Action : </b>l&lsquo;image n&lsquo;est pas au format jpg.</span>'; 
				$error = true;
			}
		}
		
		// Creer le nom de l'image
		$filenameWE = date("Y") . "_" . str_pad(date("z"), 3, '0', STR_PAD_LEFT);
		$filename = $filenameWE . $objImgTmp->makeExtension();

		// FS :
		// on redimensionne (si besoins)
		if (!$error)
		{
			if ( $objImgTmp->hauteur() > $HMAX_FS || $objImgTmp->largeur() > $LMAX_FS)
			{
				// savoir en fonction de quoi on redimensionne
				// NB : test oblige car HMAX != LMAX
				$ratio = $HMAX_FS / $objImgTmp->hauteur();
				if ( $objImgTmp->largeur() * $ratio > $LMAX_FS)
				{
					// modifier ration (prendre en fonction de la largeur
					$ratio = $LMAX_FS / $objImgTmp->largeur();
				}
				
				// on redimmensionne en fonction du ratio trouve
				$objImgTmp->redimmensionner($ratio, $tmpImageFullname);
				unset($objImgTmp);
				$objImgTmp = ImageFactory::factory($tmpImageFullname);
			}
			// On copie la FS 
			$imageFullname = $PATH_PHOTOS . $filename;
			copy ($tmpImageFullname, $imageFullname);
			
			// Vignette :
			// on redimmensionne (si besoins)
			if ( $objImgTmp->hauteur() > $HMAX_V || $objImgTmp->largeur() > $HMAX_V)
			{
				$ratio = $HMAX_V / max( $objImgTmp->hauteur(), $objImgTmp->largeur()) ;
				$objImgTmp->redimmensionner($ratio, $tmpImageFullname);
			}
			// On copie la Vignette
			$imageFullname = $PATH_MINIS. $filename;
			copy ($tmpImageFullname, $imageFullname);
		}
		
		// et on supprime l'objet temporaire cree
		unset($objImgTmp);

		// Tout c'est bin passe au niveau fichier -> enregistrement mysql
		$title   = htmlentities($this->input->post('newTitle'), ENT_QUOTES);
		
		
		// Enregistrement en base
		$sql = "INSERT INTO immt ( `year`, `day`, `user_id`,  `title`) VALUES ( ?, ?, ?, ?);";
		$this->db->query($sql, array(date("Y"), date("z"), $user->user_id, $title));

		// On crée le cache ce snippet sera chargé par tout les utilisateur automatiquement
		$immt_url = base_url() . 'assets/img/immt/'.$filenameWE.'.jpg';
		
		$immt_snippet_cache = "<a href=\"{$immt_url}\" title=\"" . htmlspecialchars($title) .  "\" data-lightbox=\"lightbox[immt]\">";
		$immt_snippet_cache .= "<img src=\"{$immt_url}\" alt=\"image du moment\" width=\"450px\" />";
		$immt_snippet_cache .= "<p class=\"immtTitle\">{$title}</p></a>";

		$this->layout->createCache("immt_welcom_snippet", $immt_snippet_cache);


	    // On notifie sur la page d'accueil
	    logMessage($user->user_id, mktime(), 'message', 'immt', 'Ajoute une nouvelle "image du moment"', 'immt');

        // On redirige vers l'accueil des immt
        redirect('immt', 'refresh');
	}



	/**
	 * Calcul les statistiques générales pour les immt
	 */
	private function stats($user)
	{
		$this->layout->addPlugin('jqPlot');

		// 1) Global stats
		$sql = 'SELECT COUNT( * ) AS i, COUNT( DISTINCT (`user_id`) ) AS p FROM  `immt`';
		$data = $this->db->query($sql)->result()[0];
		$stats = array('stats');
		$stats['stats']['userRankUrl'] = base_url() . 'grenier/ranks/' . $user->user_id;
		$stats['stats']['maxImmt'] = $data->i;
		$stats['stats']['maxCommiters'] = $data->p;
		$stats['stats']['average'] = round( ((date("Y") - 2006) * 365 + 362 + date("z")) / $data->i) ; // (année 2005 débute le 3 janvier) + x années pleine + année en cour

		// 2) Best authors
		$sql = 'SELECT COUNT( * ) as i, u.username  FROM  immt i NATURAL JOIN absg_users u GROUP BY user_id ORDER BY i DESC';
		$data = $this->db->query($sql)->result();
		$other = array('name' => 'Les '. (count($data) - 3).' autres', 'value' => 0);
		for ($i=3; $i < count($data); $i++) 
		{ 
			$other['value'] += $data[$i]->i;
		}
		$stats['stats']['commiters'] = array
		(
			0 => array('name' => $data[0]->username, 'value' => $data[0]->i),
			1 => array('name' => $data[1]->username, 'value' => $data[1]->i),
			2 => array('name' => $data[2]->username, 'value' => $data[2]->i),
			3 => $other,
		);


		// 2.2) Graph design
		$js = "var graphData = [['{$stats['stats']['commiters'][0]['name']}',{$stats['stats']['commiters'][0]['value']}],";
		$js.= "['{$stats['stats']['commiters'][1]['name']}',{$stats['stats']['commiters'][1]['value']}],";
		$js.= "['{$stats['stats']['commiters'][2]['name']}',{$stats['stats']['commiters'][2]['value']}],";
		$js.= "['{$stats['stats']['commiters'][3]['name']}',{$stats['stats']['commiters'][3]['value']}]];";
		$stats['stats']['jqPlotData'] = $js;


		// 3) Rangs

		// 3.1) NbrImmt et NoteG
		$nbrImmt = $this->db->query("SELECT count(*) as 'immt' FROM immt WHERE user_id={$user->user_id}")->result()[0]->immt;
        $noteG = $this->db->query("SELECT rank FROM absg_users WHERE user_id={$user->user_id}")->result()[0]->rank;
        $noteG = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);
        $noteG = $noteG[2];
        

        // 3.2) Palliers
		$stepsLevels = getRanksSteps();
		$currentLevel = findRankStep('immt', $stepsLevels, array('immt' => $nbrImmt));
		$minImmt = ($currentLevel == count($stepsLevels['immt'])-1) ? 0 : $stepsLevels['immt'][$currentLevel];
		$maxImmt = $stepsLevels['immt'][min($currentLevel+1,count($stepsLevels['immt'])-1)];
		$nextLevel = findRankStep('immt', $stepsLevels, array('immt' => $maxImmt));


		// 4) Formatage des données a afficher
		$stats['stats']['rank'] = getUserRankStats($user);
		$stats['stats']['rank']['noteg'] = $noteG;
		$stats['stats']['rank']['boundMin'] = $minImmt ;
        $stats['stats']['rank']['boundMax'] = $maxImmt;
        $stats['stats']['rank']['nbrImmt'] = $nbrImmt;
        $stats['stats']['rank']['progression'] = 'width:' . round( (min($nbrImmt, $maxImmt) - $minImmt) / ($maxImmt - $minImmt )*100,0).'px;';
        $stats['stats']['rank']['progressionValue'] =  round((min($nbrImmt, $maxImmt) - $minImmt) / ($maxImmt - $minImmt )*100,0);
        $stats['stats']['rank']['nextReward'] = ($currentLevel < count($stepsLevels['immt'])-1 ) ? '+' . ($nextLevel - $currentLevel) . ' G' : '-';
        $stats['stats']['rank']['nextStep'] = $maxImmt;

		return $stats;
	}
	

	/**
	 * Retourne la date à partir de l'année et du jour de l'année
	 */
	private function getDateFromDay($dayOfYear, $year) 
	{
		$date = DateTime::createFromFormat('z Y', strval($dayOfYear) . ' ' . strval($year));
		return date_timestamp_get($date);
	}
}

/* End of file immt.php */
/* Location: ./application/controllers/immt.php */