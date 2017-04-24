<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// définition d'une variable globale pour garder le contexte lors des différents appels de fonction
global $AGPA_CTX;


class Agpa extends CI_Controller 
{
    private $user = false;
    public $ctx = array();

    /**
     * Page d'accueil des AGPA. -> concerne l'édition en cours par défaut
     *  - diaporama avec images aléatoires qui défiles
     *  - résumé en chiffre des AGPA
     *  - résumé de l'édition en cours en fonction de la phase
     */
    public function index()
    {
	    $this->current();
    }


    /**
	* Edition courrante des AGPA
	* Affichage de la phase en fonction de la date
	*/
    public function current($category=0, $arg1=0, $arg2=0, $arg3=0)
    {
		// Init layout
		$this->layout->setTheme('agpa');
		$user = $this->layout->init("agpa");

		// Init AGPA
		$this->load->helper('agpa');
		$this->ctx = $this->init($user, 'current');
		
	   
	    // EXCEPTION : TEMPS EN RAB.
	    /*
	    if ($user->user_id != 2)
	    {
	        $this->ctx['current_phase_year'] = 2015;
	        $this->ctx['current_phase'] = 1;
	    }
	    */
    
	    if ($user->user_id == 0)
	    {
	        $this->ctx['current_phase_year'] = 2015;
	    		//$this->ctx['current_phase'] = 1;
	        $this->ctx['current_phase'] = 4;
	    }

		// Résumé de l'édition des AGPA + init tableau de photo
		if ($this->ctx['current_phase'] < 5)
		{
			initPhotosData($this->ctx, $user, $this->ctx['current_phase_year']);
		}
		else
		{
			initPhotosAwardsData($this->ctx, $user, $this->ctx['current_phase_year']);
		}
		
		// On garde un pointer global sur le context
		//global $AGPA_CTX;
		//$AGPA_CTX = &$this->ctx;

		/*
		// Photo aleatoire
		$sql = 'SELECT p.*, u.username FROM agpa_photos p, absg_users u WHERE year < ' . $this->ctx['current_year'] . ' AND u.user_id = p.user_id';
		$query = $this->db->query($sql);
		$this->ctx['rdm_photo'] = $query->result()[rand(0, $query->num_rows())];
		*/


		/*
			'PHOTO_URL_FULLSCR'  => AGPA_PATH_PHOTOS.$photo['year'].'/mini/'.$photo['filename'],
			'PHOTO_URL_THUMB' => AGPA_PATH_PHOTOS.$photo['year'].'/mini/vignette_'.$photo['filename'],
			'PHOTO_TITLE'        => $photo['title'],
			'PHOTO_AUTHOR'       => $photo['username'],
			'PHOTO_YEAR'        => $photo['year'],
			'REMAINING_TIME'      => $timeLeft
		*/

		
		switch ($this->ctx['current_phase']) 
		{
			case 1: // Enregistrement Photos
				$this->layout->addCss("phase1");
				$this->layout->addJs("phase1");
				$this->layout->addPlugin('jquery-ui');
				$this->layout->addPlugin('lightbox');
				$this->ctx = actualPhase1($this->ctx, $user);
				$this->layout->view('agpa/phase1', $this->ctx);
				break;
			
			case 2: // Vérifications Photos
				if ($category == 0)
				{
					$this->ctx['show_category'] = false;
				}
				else
				{
					$this->ctx['show_category'] = $category;
					sufflePhotos($this->ctx['photos']);
				}

				actualPhase2Resume($this->ctx, $user);
				$this->layout->addCss("phase2");
				$this->layout->addJs("phase2");
				$this->layout->addPlugin('jquery-ui');
				$this->layout->addPlugin('lightbox');
				$this->layout->view('agpa/phase2', $this->ctx);
				break;

			case 3: // Votes
				$this->ctx['show_category'] = $category;
				actualPhase3Resume($this->ctx, $user);
				if ($category <= 0  && $category != -3)
				{
					$this->ctx['show_category'] = false;
				}
				$this->layout->addCss("phase3");
				$this->layout->addJs("phase3");
				$this->layout->addPlugin('jquery-ui');
				$this->layout->addPlugin('lightbox');
				$this->layout->view('agpa/phase3', $this->ctx);
				break;

			case 4: // Délibérations
				$this->ctx['show_category'] = $category;
				$this->ctx['computesScores_nextStep'] = $arg1+1;
				
				// si super-Admin : peut procéder au dépouillement
				if ($user->auth == '*' && $category === 'computesScores')
				{
					// L'administrateur suit le déroulement des différentes étapes de calculs pour l'attribution des awards
					$this->load->helper('agpa_algorithms');
					ActualPhase4DeliberationsEngine($this->ctx, $user, $arg1);

					$this->layout->addCss("phase4");
					$this->layout->addJs("phase4");
					$this->layout->addPlugin('jquery-ui');
					$this->layout->addPlugin('lightbox');
					$this->layout->view('agpa/phase4-admin', $this->ctx);
				}
				else
				{
					actualPhase3Resume($this->ctx, $user);
					if ($category <= 0  && $category != -3)
					{
					$this->ctx['show_category'] = false;
					}


					$this->layout->addCss("phase4");
					$this->layout->addJs("phase4");
					$this->layout->addPlugin('jquery-ui');
					$this->layout->addPlugin('lightbox');
					$this->layout->view('agpa/phase4', $this->ctx);
				}

				break;

			case 5: // Résultats
				// si super-Admin : peut procéder au dépouillement
				if ($user->auth == '*' && $category === 'ceremonyOnline')
				{
					
					$this->ctx['photoData'] = ceremonyOnline($this->ctx, $arg1, $arg2, $arg3);
					$this->ctx['ceremonyYear'] = $arg1;
					$this->ctx['ceremonyCat'] = $arg2;
					$this->ctx['ceremonyStep'] = $arg3;
					
					
					$this->layout->addCss("phase5");
					$this->layout->addJs("phase5");
					$this->layout->addPlugin('jquery-ui');
					$this->layout->addPlugin('lightbox');
					$this->layout->view('agpa/ceremonyOnline', $this->ctx);
				}
				else
				{
					$this->ctx['show_category'] = $category;
					actualPhase5Resume($this->ctx, $user);
					if ($category == 0)
					{
						$this->ctx['show_category'] = false;
					}

					
					$this->layout->addCss("phase5");
					$this->layout->addJs("phase5");
					$this->layout->addPlugin('jquery-ui');
					$this->layout->addPlugin('lightbox');
					$this->layout->view('agpa/phase5', $this->ctx);
				}
				break;
			
			default:
				break;
		}
    }



    /**
     * Lire le réglement des AGPA
     */
    public function rules()
    {
  		// Init layout
  		$this->layout->setTheme('agpa');
  		$user = $this->layout->init("rules");
  
  		// Init AGPA
  		$this->load->helper('agpa');
  		$this->ctx = $this->init($user, 'rules');
  
  
  		showRules($this->ctx, $user, $this->ctx['current_phase_year']);
  		
  		
  		
  		$this->layout->view('agpa/rules', $this->ctx);
    }


    /**
     * Parcourir les archives des AGPA
     */
    public function archives($filter_1="", $filter_2="")
    {
  		// Init layout
  		$this->layout->setTheme('agpa');
  		$this->layout->addCss("archives");
  		$this->layout->addJs("archives");
  		$this->layout->addPlugin('lightbox');
  		$user = $this->layout->init("archives");
  		
  		// Init AGPA
  		$this->load->helper('agpa');
  		$this->ctx = $this->init($user, 'archives');
  		
  		// On parse les filtres
  		if ($filter_1 == "")
  		{
  			// Pas de filtre : menu des archives
  			$this->ctx['archiveMenu'] = displayArchivesSummary($this->ctx);
  			$this->layout->view('agpa/archivesMenu', $this->ctx);
  		}
  		else
  		{
  			$filters = array(
  				'f1_type'  => $filter_1[0],
  				'f1_value' => substr($filter_1, 1),
  				'f2_type'  => ($filter_2 == "") ? "" : $filter_2[0],
  				'f2_value' => ($filter_2 == "") ? "" : substr($filter_2, 1)
  				);
        
          
  			$archiveSql = buildArchiveSQLQuery($this->ctx, $filters);
        	$displayView = buildArchiveView($this->ctx, $filters);

        echo "\nSALUT\n";
        print_r($archiveSql);
        echo "\nAu revoir\n";
        
  			$this->layout->view('agpa/archivesReader', $this->ctx);
  		}
    }


    /**
     * Consulter les stats des AGPA
     */
    public function stats()
    {
		  $this->layout->setTheme('agpa');
  		$user = $this->layout->init("stats");
  
  		// Init AGPA
  		$this->load->helper('agpa');
  		$this->ctx = $this->init($user, 'rules');
     
  
  
  		$this->layout->view('agpa/stats', $this->ctx);
    }

    /**
	* Observer son palmares et celui des autres
	*/
    public function slides($year=0)
    {
		// Init layout
		$this->layout->setTheme('agpa');
		$this->layout->addCss("slides");
		$this->layout->addJs("slides");
		$this->layout->addPlugin('lightbox');
		$user = $this->layout->init("currrent");
		
		// Init AGPA
		// $this->load->helper('agpa');
		// $this->load->helper('agpa_algorithms');
		$this->ctx = $this->init($user, 'current');
		
		
		
		if ($user->user_id == 2)
	    {
	        // $this->ctx['current_phase_year'] = 2015;
	        // $this->ctx['current_phase'] = 4;
	        
	        $this->layout->view('agpa/slides', $this->ctx);
	    }
	    else
	    {
	    	$this->layout->view('agpa/stats', $this->ctx);
	    }
	    
	    
				
		
  		
    }

    /**
	* Observer son palmares et celui des autres
	*/
    public function palmares($filter_user=0, $filter_year=0)
    {
		// Init layout
		$this->layout->setTheme('agpa');
		$this->layout->addCss("palmares");
		$this->layout->addJs("palmares");
		$this->layout->addPlugin('lightbox');
		$user = $this->layout->init("palmares");
		
		// Init AGPA
		$this->load->helper('agpa');
		$this->load->helper('agpa_algorithms');
		$this->ctx = $this->init($user, 'palmares');
		
		
		// On récupère les données a afficher
		$data = getPalmaresData($this->ctx, ($filter_user === 0) ? $user->user_id : $filter_user, $filter_year);
		
		// On construit le menu filtre
		$menu = buildPalmaresMenu($this->ctx, 'palmares', ($filter_user === 0) ? $user->user_id : $filter_user, $filter_year);
		
		
		
		$this->ctx['filterYear'] = $data['filterYear'];
		$this->ctx['maxYear'] = $data['maxYear'];
		$this->ctx['resume'] = $data['resume'];
		$this->ctx['resumeTotal'] = $data['resumeTotal'];
		$this->ctx['palmaresUserData'] = $data['palmaresUserData'];
		$this->ctx['filterMenus'] = $menu;
		$this->layout->view('agpa/palmares', $this->ctx);
    }



    public function switchPalmares()
    {
		$feature = $this->input->post('feature', 'palmares');
		$userFilter = $this->input->post('userFilter', 0);
		$yearFilter = $this->input->post('yearFilter', 0);
		
		if ($feature = 'palmares')
		{
			$this->palmares($userFilter, $yearFilter);
		}
		else
		{
			$this->palmares($userFilter, $yearFilter);
		}
    }

    
	
	
	
	





	
	
	
	
	
	
	
	



	/**
	 * Récupérer le fichier de données pour la cérémonie des AGPA.
	 * Seul un administrateur peur le récupérer
	 */
	public function agpaCeremonyFile($year=-1)
	{
		// Init layout
		$this->layout->setTheme('agpa');
		$user = $this->layout->init("agpa");

		// Init AGPA
		$this->load->helper('agpa');
		$this->load->helper('download');
		$this->ctx = $this->init($user, 'current');

		if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}


		// On vérifie l'année
		if ($year < 2006 || $year > date("Y"))
		{
			$year = date("Y");
		}
		// Récupération des données sur les photos
		$sql = "SELECT p.photo_id, u.username, p.category_id, p.year, p.filename, p.title, p.g_score, p.number ";
		$sql.= "FROM agpa_photos p, absg_users u ";
		$sql.= "WHERE p.year = $year AND p.user_id = u.user_id ORDER BY p.number ASC";
		$result = $this->db->query($sql)->result();
		
		$data =  '<?xml version="1.0" encoding="utf-8" ?>';
		$data .= '<agpa><photos>';
		foreach ($result as $row)
		{
			$title = html_entity_decode($row->title,ENT_QUOTES);
			$title = str_replace("\"", "&quot;",$title);
			
			$data .=  "\n\t<photo id=\"{$row->photo_id}\" file=\"{$row->filename}\" author=\"{$row->username}\" title=\"{$title}\" category=\"{$row->category_id}\" score=\"{$row->g_score}\"/>";
		}
		$data .=  '</photos>';
		
		// Récupération des données sur le palmarès
		$sql = "SELECT a.category_id, a.award, a.photo_id, u.username FROM agpa_awards a, absg_users u WHERE `year` =$year AND a.author_id = u.user_id";
		$result = $this->db->query($sql)->result();
		$data .=  '<awards>';
		foreach ($result as $row)
		{
			$data .=  "\n\t<award category=\"{$row->category_id}\" author=\"{$row->username}\" award=\"{$row->award}\" photo=\"{$row->photo_id}\" />";
		}

		$data .=  '</awards>';
		$data .= '</agpa>';
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("AGPA$year.xml", $data);
		
		exit;
	}



	/**
	 * Exporte au format CSV les données des photos (pour les fichiers excels de Florent)
	 */
	public function photosCSVExport($year=-1)
	{
		// Init layout
		$this->layout->setTheme('agpa');
		$user = $this->layout->init("agpa");

		// Init AGPA
		$this->load->helper('agpa');
		$this->load->helper('download');
		$this->ctx = $this->init($user, 'current');

		if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}


		// On vérifie l'année
		if ($year < 2006 || $year > date("Y"))
		{
			$year = date("Y");
		}


		// ------------------------------------------------------------------------------
		// Récupération des données sur les awards

		$awards = array();
		$sql = "SELECT * FROM agpa_awards WHERE year = $year";
		$result = $this->db->query($sql)->result();
		foreach ($result as $row)
		{
			if (!isset($awards[$row->photo_id]))
			{
				$awards[$row->photo_id] = array(0,0,0);
			}

			if ($row->category_id == -2)
			{
				// Meilleure photo toute catégorie
				$awards[$row->photo_id][0] = $this->awardToInt($row->award);
			}
			else if ($row->category_id > 0)
			{
				// Meilleure photo dans sa catégorie
				$awards[$row->photo_id][1] = $this->awardToInt($row->award);
			}
			else if ($row->category_id == -3)
			{
				// Meilleure titre
				$awards[$row->photo_id][2] = $this->awardToInt($row->award);
			}
		}

        

		// ------------------------------------------------------------------------------
		// Récupération des données sur les photos

		$sql = "SELECT * FROM agpa_photos WHERE year = $year";
		$result = $this->db->query($sql)->result();

	
		$data = "";
		$edition = $year - 2005;
		
		// On affiche les infos
		foreach ($result as $row)
		{
			$title = html_entity_decode($row->title,ENT_QUOTES);
			if (!isset($awards[$row->photo_id]))
			{
				$awards[$row->photo_id] = array(0,0,0);
			}
			
			// On double les guillemets double " (norme du format CSV pour traiter les " dans une chaine de caractère elle même délimité par des ".
			$title = str_replace("\"", "\"\"",$title);
			
			$data .= "{$row->photo_id},{$edition},\"{$row->filename}\",{$row->category_id},{$row->user_id},\"{$title}\",{$row->score},{$row->votes},{$row->votes_title},{$row->g_score},";
			$data .= $awards[$row->photo_id][0] . "," . $awards[$row->photo_id][1] . "," . $awards[$row->photo_id][2] . ",\n";
		}
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("AGPA$year-photos.csv", $data);
		
		exit;
	}

	
	private function awardToInt($award)
	{
		switch ($award) 
		{
			case 'diamant':
				return 5;
				break;
			case 'or':
				return 4;
				break;
			case 'argent':
				return 3;
				break;
			case 'bronze':
				return 2;
				break;
			case 'lice':
				return 1;
				break;
		}

		return -1;
	}
	
	

	/**
	 * Exporte au format CSV les données des votes (pour les fichiers excels de Florent)
	 */
	public function votesCSVExport($year=-1)
	{
		// Init layout
		$this->layout->setTheme('agpa');
		$user = $this->layout->init("agpa");

		// Init AGPA
		$this->load->helper('agpa');
		$this->load->helper('download');
		$this->ctx = $this->init($user, 'current');

		if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}


		// On vérifie l'année
		if ($year < 2006 || $year > date("Y"))
		{
			$year = date("Y");
		}


		// ------------------------------------------------------------------------------
		// Récupération des données sur les votes

		$sql = "SELECT * FROM agpa_votes WHERE year = $year";
		$result = $this->db->query($sql)->result();

	
        $data = "";
		$edition = $year - 2005;
		
		// On affiche les infos
		foreach ($result as $row)
        {
            $data .= "{$edition},{$row->user_id},{$row->photo_id},{$row->category_id},{$row->score},\n";
        }
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("AGPA$year-votes.csv", $data);
		
		exit;
	}



	/**
	 * Exporte au format CSV les données des votes (pour les fichiers excels de Florent)
	 */
	public function usersCSVExport()
	{
		// Init layout
		$this->layout->setTheme('agpa');
		$user = $this->layout->init("agpa");

		// Init AGPA
		$this->load->helper('agpa');
		$this->load->helper('download');
		$this->ctx = $this->init($user, 'current');

		if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}


		// ------------------------------------------------------------------------------
		// Récupération des données sur les votes

		$sql = "SELECT  `user_id` ,  `username` FROM  `absg_users` ";
		$result = $this->db->query($sql)->result();

	
		$data = "";
		
		// On affiche les infos
		foreach ($result as $row)
		{
			$username = html_entity_decode($row->username,ENT_QUOTES);
			$data .= "{$row->user_id},\"{$username}\",\n";
		}
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("AbsG-Users.csv", $data);
		
		exit;
	}
	
	
	
	
	
    /**
     * Clean les originaux en supprimant ceux qui ne sont pas référencés en base
     * Puis crée un zip .
     * Lance le téléchargement du zip. La suppression du zip est a faire manuellement, ansi que le retéléchargement si nécessaire
     */
    public function getAndClearOriginals($year=0)
    {
	
	// Check year
	if ($year < 2006 || $year > date("Y"))
	{
	    $year = date("Y");
	}
 
	// On définit quelques variables importante
	$AGPA_PATH_PHOTOS = __DIR__ . '/../../assets/img/agpa/' . $year .'/';
    
	// On réucupère la liste des fichiers
	$filesList = array_diff( scandir($AGPA_PATH_PHOTOS), Array( ".", "..", "mini", "index.html, toDelete.txt" ) );
	$filesToZip = array();

	
	
	// On les supprimes de la liste des fichiers tout ceux qui sont référencés en base
	$result = $this->db->query("Select filename from agpa_photos where year=$year")->result();
	
	
	foreach($result as $fileInDb)
	{
	    //echo "Search for {$fileInDb->filename} => idx=";
	    $idx = array_search($fileInDb->filename, $filesList);
	    //echo "$idx\n";
	    if (isset($filesList[$idx]))
	    {
		$filesToZip[] = $filesList[$idx];
		unset($filesList[$idx]);
	    }
	}
	
	// Création d'un fichier toDelete.txt avec la liste des fichiers à supprimer.
	// Pour plus de sécurité, la suppression des fichiers sur le server doit être faite manuellement.
	$myfile = fopen($AGPA_PATH_PHOTOS . "toDelete.txt", "w") or die("Unable to create the toDelete.txt file !");
	$txt = "Les fichiers ci-dessous ont été ignorés lors de la création du zip car non référencés en base de donnée.\n" . implode("\n", $filesList);
	fwrite($myfile, $txt);
	fclose($myfile);
	
	
	// Si plus d'originaux à télécharger, on prévient l'utilisateur
	if (count($filesToZip) == 0)
	{
	    echo "Les originaux ne sont plus en ligne. Voir avec Olivier pour les récupérer.";
	    exit;
	}
	
	// Si l'archive existe déjà, on supprime 
	$archiveFileName = "originals.zip";
	$archiveFileFullName = $AGPA_PATH_PHOTOS . $archiveFileName;
	if (file_exists($archiveFileFullName))
	{
	    unlink($archiveFileFullName);
	}
	
	
	// On crée l'archive zip des originaux
	$zip = new ZipArchive();
	
	// Create the file and throw the error if unsuccessful
	if ($zip->open($archiveFileFullName, ZIPARCHIVE::CREATE )!==TRUE) 
	{
	    exit("cannot open <$archiveFileName>\n");
	}
	
	// On ajoute les fichiers à l'archive zip
	foreach($filesToZip as $files)
	{
	    $zip->addFile($AGPA_PATH_PHOTOS.$files,$files);
	}
	$zip->close();
	
	// On force le téléchargement du fichier zip par le navigateur
	header("Content-type: application/zip"); 
	header("Content-Disposition: attachment; filename=$archiveFileName");
	header("Content-length: " . filesize($archiveFileFullName));
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	readfile("$archiveFileFullName");
    }
	
/*
	
-- 
SELECT p.*, 
FROM  agpa_photos p
      LEFT OUTER JOIN agpa_prizelist a
           ON p.photo_id = a.photo
WHERE  p.year=2013
	

-- Décompte des votes
SELECT YEAR, category_id, photo_id, SUM( score ) AS score
FROM agpa_votes
WHERE YEAR =2013
AND category_id <> -3
GROUP BY photo_id
ORDER BY category_id ASC , score DESC 


SELECT YEAR, category_id, photo_id, COUNT( category_id ) AS score_titre
FROM agpa_votes
WHERE YEAR =2013
AND category_id = -3
GROUP BY photo_id
ORDER BY category_id ASC , score_titre DESC 



SELECT t1.year, t1.category_id, t1.photo_id, SUM( t1.score ) AS score, count(t2.category_id) AS score_titre
FROM agpa_votes t1, agpa_votes t2
Where t2.year=2013 and t2.category_id = -3
and t1.year=2013 AND t1.category_id <> -3
and t1.photo_id = t2.photo_id
GROUP BY t1.photo_id
Order by t1.category_id asc, t1.score desc


*/




















	/**
	 * Initialise le contexte pour les AGPA.
	 */
	private function init($user, $section)
	{
		$ctx = array();

		// Variables importantes
		$ctx['current_year'] = date('Y');
		$ctx['current_phase_year'] = $ctx['current_year'];
		$ctx['section'] = $section;
		$ctx['user'] = $user;
		$ctx['is_admin'] = $user->auth == '*';
		$ctx['max_feather'] = 10;

		// ----------------------------------------------------------------------------
		// les limites des phases (N° phase => (jour, mois))
		$sql = "SELECT `value` FROM `absg_current_data` WHERE `key`='agpa_phase_boundaries'";
		$dataBoudaries = $this->db->query($sql)->result()[0]->value;
		
		//$dataBoudaries = "1/1-15/12-17/12-21/12-25/12-30/12";
		$dataBoudaries = explode('-', $dataBoudaries);
		
		$phase_boundaries = array();
		for ($i =0; $i < count($dataBoudaries); $i++)
		{
			$phase_boundaries[$i] = explode('/', $dataBoudaries[$i]);
		}
		
		$current_time = time();
		$ctx['current_phase'] = 0;
		$ctx['phases_boundaries'] = $phase_boundaries;

		// ----------------------------------------------------------------------------
		// recuperer la phase a laquelle on est.
		do
		{
			++$ctx['current_phase'];
			$limitMkt  = mktime(0,0,0, $phase_boundaries[$ctx['current_phase']][1], $phase_boundaries[$ctx['current_phase']][0], $ctx['current_year']);
		}
		while ($limitMkt - $current_time < 0 && $ctx['current_phase'] < 5);

		// Cas spécial, si phase 1 mais avant juin, alors phase 5 de l'année précédante
		if ($ctx['current_phase'] == 1 && $current_time < mktime(0,0,0, 6, 1, $ctx['current_year'])) 
		{
			$ctx['current_phase'] = 5; 
			$ctx['current_phase_year'] = $ctx['current_year'] - 1;
		}
		

		// TODO : système de dérogation des phase (pour réengistrer photo en phase 2, ou voter en phase 4 pour arranger les retardataires)
		// if ($ctx['is_admin'])
		// {
		// 	$ctx['current_phase'] = 3; 
		// 	$ctx['current_year'] = 2013;
		// 	$ctx['current_phase_year'] = 2013;
		// }

		// ----------------------------------------------------------------------------
		// Gestion de la frise chronologique
		if ($section == 'current')
		setupTimeLine($ctx['current_phase'], $phase_boundaries, $ctx);


		// ----------------------------------------------------------------------------
		// Récupérer liste des membres
		$ctx['members'] = array();
		$sql = 'SELECT u.user_id, u.username, p.rootfamilly FROM absg_users u INNER JOIN agenda_people p ON u.people_id=p.people_id';
		$result = $this->db->query($sql)->result();
		foreach ($result as $member)
		{
			$ctx['members'][$member->user_id] = $member;
		}

		// ----------------------------------------------------------------------------
		// Récupérer liste des catégories
		$ctx['cat_number'] = 0;
		$ctx['categories'] = array();

		$sql = 'SELECT c.* , v.title as "vtitle", v.description AS "vdescription" FROM agpa_categories c LEFT JOIN agpa_catvariants v ON c.category_id = v.category_id AND v.year ='.$ctx['current_phase_year'].' ORDER BY c.order ASC';
		$result = $this->db->query($sql)->result();
		foreach ($result as $cat)
		{
			$ctx['categories'][$cat->category_id] = $cat;
			if ($cat->category_id > 0) $ctx['cat_number']++;
		}

		// ----------------------------------------------------------------------------
		// Photos aleatoires qui défileront dans la banniere
		$ctx['slideshow'] = array();
		$sql = 'SELECT year, filename FROM agpa_photos WHERE year < ' . $ctx['current_year'];
		$query = $this->db->query($sql);
		$result =  $query->result();
		for ($i=0; $i < 40; $i++) 
		{ 
			$ctx['slideshow'][] = $result[rand(0, $query->num_rows() - 1)];
		}
		

		// ----------------------------------------------------------------------------
		// Quelques stats
		$sql = 'SELECT COUNT(DISTINCT photo_id) as "photos", COUNT( DISTINCT year) as "editions", COUNT( DISTINCT user_id) as "authors" FROM agpa_photos WHERE 1';
		$data = $this->db->query($sql)->result()[0];
		$ctx['nbr_photos'] = $data->photos;
		$ctx['nbr_editions'] = $data->editions;
		$ctx['nbr_authors'] = $data->authors;


		return $ctx;
	}







	/**
	 * Enregistrement d'une nouvelle photo (phase 1 et 2).
	 * Les paramètres doivent être fournis via un form multi-data de type post.
	 */
	public function newPhoto()
	{
		// check user session
		$CI = get_instance();
		$user = checkUserSession($CI);


		// Load helper
		//$this->load->helper('agpa');
		$this->load->helper('image_helper');


		// Definitions des constantes importantes
		$AGPA_ROOT_PATH =  __DIR__ . '/../../assets/img/agpa/';
		$AGPA_PATH_PHOTOS = $AGPA_ROOT_PATH . date("Y").'/';
		$AGPA_PATH_MINIS  = $AGPA_ROOT_PATH . date("Y").'/mini/';
		$AGPA_URL_PHOTOS = base_url() . 'assets/img/agpa/'. date("Y").'/';
		$AGPA_URL_MINIS = base_url() . 'assets/img/agpa/'. date("Y").'/mini/';
		$HMAX_FS = 950;
		$LMAX_FS = 1200;
		$HMAX_V  = 200;

		// On vérifie si les répertoires visés existent, si non : on les crées
		if ( !file_exists( $AGPA_PATH_PHOTOS ))
		{
			mkdir($AGPA_PATH_PHOTOS);
		}
		if ( !file_exists( $AGPA_PATH_MINIS ))
		{
			mkdir($AGPA_PATH_MINIS);
		}

		//ob_start();
		
		// On part du principe que tout c'est bien passe, et si ya un probleme, on changera a ce moment le msg d'erreur
		$msg = "";
		$error = false;
		// Verification de l'upload
		if ($_FILES['newPhoto']['error'] === 0)
		{
			$tmpImageFullname = $_FILES['newPhoto']['tmp_name'];
		}
		elseif ($_FILES['newPhoto']['error'])
		{
			$msg = '<span class="error"><b>Erreur : </b>';
			switch($_FILES['newPhoto']['error'])
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
		
		
		// Creer le nom de l'image
		$imageTitle = time();
		
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
		
		// On garde l'originale tel quel
		if (!$error)
		{
			$filename    = $imageTitle . $objImgTmp->makeExtension();
			//$resolution = $objImgTmp->largeur() . ' x '. $objImgTmp->hauteur();
			//$weight      = round( filesize($tmpImageFullname) / 1024, 1);
			if ( $weight > 950.9 )
			{
				$weight = round( $weight / 1024, 1);
				$weight = $weight . ' Mo';
			}
			else
			{
				$weight = $weight . ' Ko';
			}
			
			$imageFullname = $AGPA_PATH_PHOTOS . $filename;
			if (!copy ($tmpImageFullname, $imageFullname))
			{
				$msg = '<span class="error"><b>Erreur : </b>droits insuffisants pour copier de  &lsquo;' . $tmpImageFullname . '&lsquo;<br/>vers &lsquo;' . $imageFullname . '&lsquo;.</span>'; 
				$error = true;
			}
		}
		
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
			$imageFullname = $AGPA_PATH_MINIS . $filename;
			copy ($tmpImageFullname, $imageFullname);
			
			// Vignette :
			// on redimmensionne (si besoins)
			if ( $objImgTmp->hauteur() > $HMAX_V || $objImgTmp->largeur() > $HMAX_V)
			{
				$ratio = $HMAX_V / max( $objImgTmp->hauteur(), $objImgTmp->largeur()) ;
				$objImgTmp->redimmensionner($ratio, $tmpImageFullname);
			}
			// On copie la Vignette
			$imageFullname = $AGPA_PATH_MINIS.'vignette_' . $filename;
			copy ($tmpImageFullname, $imageFullname);
		}
		/*	move_uploaded_file($imageTitle, $imageFullname)
				or $errors[3] = 'Pb de d&eacute;placement de l\'image';
			// car avec free la gestion des droits n'est pas aussi simple...
		*/
		
		// et on supprime l'objet temporaire cree
		unset($objImgTmp);

		// Tout c'est bin passe au niveau fichier -> enregistrement mysql
		$id_user = $user->user_id;
		$catId  = $_POST['newCategory'];
		$year   = date("Y");
		$title   = htmlentities($this->input->post('newTitle'), ENT_QUOTES);
		
		
		// Enregistrement en base
		$sql = "INSERT INTO agpa_photos ( `photo_id` , `user_id` , `category_id` , `year` , `filename` , `title`)
			VALUES ( NULL , '$id_user', '$catId', '$year', '$filename', '$title' );";
		$result = $CI->db->query($sql);

		if ($title == "") $msg .= '<span class="info"><b>Attention, </b> vous devez mettre un titre &agrave; votre photo.</span>'; 
		// Envoyer comme msg a l'hiddenframe un script a executer
		$sql = "SELECT MAX(photo_id) AS 'maxid' FROM agpa_photos";
		$result = $CI->db->query($sql)->result()[0];
		
		$urlFS = $AGPA_URL_MINIS . $filename;
		$urlV  = $AGPA_URL_MINIS.'vignette_' . $filename;
		$urlO  = $AGPA_URL_PHOTOS . $filename;
		//ob_end_clean();
		
		// On balance la reponse du serveur !
		echo '<html><head><script type="text/javascript">window.parent.';
		if ($error)
			echo "postPhotoERREUR('$msg');";
		else
			echo "postPhotoOK('$msg', {$result->maxid}, '$title', '$urlFS', '$urlV', '$urlO');";
		echo '</script></head></html>';
	}

	/**
	 * Mise à jour du titre d'une photo (phase 1 et 2)
	 * Les paramètres doivent être fournis via un form multi-data de type post.
	 */
	public function updateTitle()
	{
		// check user session
		$CI = get_instance();
		$user = checkUserSession($CI);

		// Get post data
		$photoId = $this->input->post('photoId');
		$newTitle = $this->input->post('newTitle');

		if ($photoId !== false && $newTitle !== false)
		{

			$title    = htmlentities($newTitle, ENT_QUOTES);
			$sql = "UPDATE agpa_photos SET `title` = '$title' WHERE `photo_id` =$photoId LIMIT 1 ;";

			$CI->db->query($sql);
			echo 0; // réponse que tout s'est bien passé
			return;
		}
		// erreur
		return;
	}


	/**
	 * Supression d'une photo (phase 1 et 2)
	 * Les paramètres doivent être fournis via un form multi-data de type post.
	 */
	public function deletePhoto()
	{
		// check user session
		$CI = get_instance();
		$user = checkUserSession($CI);

		// Get post data
		$photoId = $this->input->post('photoId');

		// Definitions des constantes importantes
		$AGPA_ROOT_PATH =  __DIR__ . '/../../assets/img/agpa/';
		$AGPA_PATH_PHOTOS = $AGPA_ROOT_PATH . date("Y").'/';
		$AGPA_PATH_MINIS  = $AGPA_ROOT_PATH . date("Y").'/mini/';

		// on verifie que l'auteur de l'action est bien le meme que l'auteur de la photo
		$sql = "SELECT * FROM agpa_photos WHERE photo_id=$photoId";
		$photo = $CI->db->query($sql)->result()[0];

		if ( $photo->user_id != $user->user_id)
		{
			echo "<span class=\"action_erreur\"><b>Action : </b>vous n'avez pas la permission de supprimer la photo";
		}
		else
		{
			$msg = 0;
			$error = false;
			// on va detruire les fichiers
			$urlO  = $AGPA_PATH_PHOTOS . $photo->filename;
			$urlFS = $AGPA_PATH_MINIS . $photo->filename;
			$urlV  = $AGPA_PATH_MINIS.'vignette_' . $photo->filename;
			try
			{
				unlink ($urlO);
				unlink ($urlFS);
				unlink ($urlV);
			}
			catch(String $msg){}
			
			// On detruit l'enregistrement
			if ( $msg == 0 )
			{
				$sql = "DELETE FROM agpa_photos WHERE photo_id=$photoId LIMIT 1";
				$CI->db->query($sql);
			}
			echo $photo->category_id;

		}
	}

	/**
	 *Associe / rapporte une question, un problème à une photo
	 * Les paramètres doivent être fournis via un form multi-data de type post.
	 */
	public function reportPhoto()
	{
		// check user session
		$CI = get_instance();
		$user = checkUserSession($CI);

		// Get post data
		$photoId = $this->input->post('photoId');
		$report = $this->input->post('report');

		if ($photoId !== false && $report !== false)
		{
			// On récupère la valeur actuel du champs ERROR de la photo pour faire la concaténation
			$sql = "SELECT error FROM agpa_photos WHERE `photo_id` =$photoId LIMIT 1 ;";
			$error = $CI->db->query($sql)->result()[0]->error;


			if ($error === null) $error = "";

			$report    = $error . $user->user_id . ": " .htmlentities($report, ENT_QUOTES) . "\n";
			$sql = "UPDATE agpa_photos SET `error` = '$report' WHERE `photo_id` =$photoId LIMIT 1 ;";

			$CI->db->query($sql);
			echo 0; // réponse que tout s'est bien passé
			return;
		}
		// erreur
		return;
	}


	/**
	 * Supression d'une photo (phase 1 et 2)
	 * Les paramètres doivent être fournis via un form multi-data de type post.
	 */
	public function votePhoto()
	{
		// check user session
		$CI = get_instance();
		$user = checkUserSession($CI);

		// Get post data
		$photoId = $this->input->post('photoId');
		$score   = $this->input->post('score');
		$year    = date("Y");

		// 1.1) Récupérer les données de la photo
		$datas = array();
		$sql = "SELECT * FROM agpa_photos WHERE photo_id={$photoId} ;";
		$photo = $CI->db->query($sql)->result()[0];

		// 1.2) Récupérer les données du vote associé (on récupère le vote "étoile" ou "plume" en fonction)
		$sql = "SELECT * FROM agpa_votes WHERE photo_id={$photoId} AND user_id={$user->user_id} ";
		if ($score == 0) $sql .= "AND score = 0"; else $sql .= "AND score <> 0";
		$vote = $CI->db->query($sql)->result();
		$vote = (isset($vote[0])) ? $vote[0] : null;

		// Précaution : si vote pour meilleur titre on change l'id de la catégorie de la photo pour -3
		if ($score == 0) $photo->category_id = -3;


		// 2- Vérifier qu'il s'agit bien de photos de même catégorie, bonne année, bon user, etc
		if ($photo->user_id == $user->user_id) 
		{
			echo "Erreur : Impossible de voter pour ses propres photos";
			return;
		}
		if ($photo->year != $year) 
		{
			echo "Erreur : Il n'est pas possible de voter pour des photos de l'année $year.";
			return;
		}

		// 3- Construction de la requete en fonction de l'action à mener
		$sql = "";
		if ($vote === null)
		{
			// 3.1) pas d'ancien vote -> on ajoute le vote tout simplement
			$sql  = 'INSERT INTO agpa_votes (`year` ,`category_id` ,`user_id` ,`photo_id` ,`score`)';
			$sql .= "VALUES ('$year', '{$photo->category_id}', '{$user->user_id}', '$photoId', '$score');";
			
		}
		else
		{
			// 3.2) un ancien vote: 2 cas a distinguer (étoile vs plume)
			if ($score == 0)
			{
				// 3.2.1) vote plume -> on supprimer l'ancien vote
				$sql = "DELETE FROM agpa_votes WHERE year={$year} AND category_id={$photo->category_id} AND user_id={$user->user_id} AND photo_id={$photoId} LIMIT 1;";
			}
			else
			{
				// 3.2.2) Vote étoile : 2 cas à distinguer (si même vote, on supprime, sinon on met à jour)
				if ($score == $vote->score)
				{
					// 3.2.2.1) On supprime
					$sql = "DELETE FROM agpa_votes WHERE year={$year} AND category_id={$photo->category_id} AND user_id={$user->user_id} AND photo_id={$photoId} LIMIT 1;";
				}
				else
				{
					// 3.2.2.2) On met à jour
					$sql = "UPDATE agpa_votes SET score={$score} WHERE year={$year} AND category_id={$photo->category_id} AND user_id={$user->user_id} AND photo_id={$photoId} LIMIT 1 ;";
				}
			}
		}

		// 4- Execution de la requête SQL
		$CI->db->query($sql);

		/*
		// requete imbriquee en 0.0788 sec.
		SELECT t2.id_photo, t2.id_categorie FROM `agpa_votes` t2 WHERE 
		t2.id_categorie = (SELECT t1.id_categorie FROM `abs3g_agpa_votes` t1 WHERE t1.id_photo=71 AND t1.id_user=2 AND t1.annee=2007)
		
		// tables jumelles en  0.0004 sec
		$sql = "SELECT t2.id_photo, t1.id_categorie FROM agpa_votes t1, agpa_votes t2 WHERE t1.id_photo={$photoId} AND t1.id_user={$user->id} AND t1.annee={$year} AND t2.id_categorie=t1.id_categorie";
		*/

		echo 0;
	}
}



