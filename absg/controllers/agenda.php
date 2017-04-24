<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agenda extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		$this->browseDirectory("G");
	}




	public function browseDirectory($selector="NomG")
	{
		// Init layout
		$user = $this->layout->init("agenda");
		$this->layout->addCss('agenda');
    $this->layout->addJs("agenda");
		$this->layout->addPlugin('lightbox');

		// Init variables
    $selector = urldecode($selector);
		$data = array();
		$data['user'] = $user;
		$selectors = array();
		$selectorsId="ABCDEFGHIJKLMNOPQRSTUVWXYZ?";
		$currentSelector = substr($selector, strlen($selector)-1);
		$currentFilter = substr($selector, 0, strlen($selector)-1);


		// Check filters
		$filters = array(
			"Nom" => array("lastname", "Nom"),
			"Prenom" => array("firstname", "Prenom"));
			//"Ville" => array("city", "Ville"));
    $currentFilterFr =   $currentFilter;
		if (!array_key_exists($currentFilter, $filters))
		{
			$currentFilter = "lastname";
		}
		else
		{
			$currentFilter = $filters[$currentFilter][0];
		}


		// Check selectors
		if (!strstr($selectorsId,$currentSelector))
		{
			$currentSelector = "G";
		}
		for ($i=0; $i<strlen($selectorsId); $i++)
		{
			$selectors[$selectorsId[$i]] = array
			(
				'selected' => ($selectorsId[$i]==$selector),
				'enable' => false,
        'count' => 0
			);
		}
   
    $sql = "SELECT substring($currentFilter,1,1) as 'letter', count(*) as 'total' FROM agenda_people group by substring($currentFilter,1,1)";
    $entries = $this->db->query($sql)->result();
    foreach ($entries as $id => $entry) 
    {
      $selectors[$entry->letter]['count'] = $entry->total;
      $selectors[$entry->letter]['enable'] = true;
    }

		// On recupere les resultats en fonction des filtres et du selector
		$entries = array();
		$sql = "SELECT p.*,  u.username, u.user_id, u.rank, u.noteg, r.title FROM `agenda_people` p LEFT JOIN `absg_users` u ON u.people_id = p.people_id LEFT JOIN `absg_ranks` r ON r.code = u.rank WHERE $currentFilter like '$currentSelector%' ORDER BY lastname, firstname, firstname2, rootfamilly";
        $entries = $this->db->query($sql)->result();

        // Compute age
        $tz  = new DateTimeZone('Europe/Brussels');
        foreach ($entries as $id => $entry) 
        {
          // check photo
          $entry->noPhoto = false;
          $path = 'assets/img/agenda/' . str_pad($entry->people_id, 3, '0', STR_PAD_LEFT) . '.jpg';
          if (file_exists($path))
          {
            $entry->photo = base_url() . $path;
          }
          else
          {
            $entry->noPhoto = true;
            $entry->photo = base_url() . 'assets/img/agenda/00' . $entry->sex . '.png';
          }
          
          // Check date naissance / dece
        	if ($entry->birthday != null)
        	{
	        	$birthDate = new DateTime(date("Y-m-d",$entry->birthday));
	        	$limitDate = ($entry->deathday != null) ?  $entry->deathday : time();
            $limitDate = new DateTime(date("Y-m-d", $limitDate));
	        	$entry->age = $limitDate->diff($birthDate)->y;
            if ($entry->age == 0) 
            {
              $entry->age = $limitDate->diff($birthDate)->m . " mois";
            }
            else
            {
              if ($entry->age > 1)
                $entry->age .= " ans";
              else
                $entry->age .= " an";
            }
	        	$entry->birthday = $this->layout->displayed_date($entry->birthday, 'shortdate');
	        	if ($entry->deathday != null)
	        	{
	        		$entry->deathday = $this->layout->displayed_date($entry->deathday, 'shortdate');
	        	}
	        }
                 
          // Check absg user
          if ($entry->username != null)
          {
            $entry->avatar = $this->layout->asset_avatar_url($entry->user_id);
            $entry->noteg = ($entry->noteg!='') ? explode(';', $entry->noteg) : array(false, 0);
            $entry->notegTotal = 0;
            for ($i=1; $i< count($entry->noteg); $i++)
            {
              $entry->notegTotal += $entry->noteg[$i];
            }
          }
	    }
        
		

		// Set view
    $data['newFormAction'] = base_url() . 'agenda/saveEntry';
		$data['currentFilter'] = $currentFilter;
    $data['currentFilterFr'] = $currentFilterFr;
		$data['filters'] = $filters;
		$data['currentSelector'] = $currentSelector;
		$data['selectors'] = $selectors;
		$data['entries'] = $entries;
		$this->layout->view('agenda/browseAgenda', $data);
	}




  public function editEntry($people_id)
  {
    // On récupère quelques infos de base
    $user = $this->layout->init("agenda");
		$this->load->helper('image_helper');
    $this->layout->addCss('agenda');

		// Init variables
		$data = array();
		$data['user'] = $user;
   
    // On check l'id fourni
    if (!is_numeric($people_id))
    {
      $this->browseDirectory("G");
      return;
    }
    
    
    // On recupere les resultats en fonction des filtres et du selector
		$entries = array();
		$sql = "SELECT p.*,  u.username, u.user_id, u.rank, u.noteg, r.title FROM `agenda_people` p LEFT JOIN `absg_users` u ON u.people_id = p.people_id LEFT JOIN `absg_ranks` r ON r.code = u.rank WHERE p.people_id = $people_id";
    $entry = $this->db->query($sql)->result()[0];
    
    // On check la photo
    $entry->noPhoto = false;
    $path = 'assets/img/agenda/' . str_pad($entry->people_id, 3, '0', STR_PAD_LEFT) . '.jpg';
    if (file_exists($path))
    {
      $entry->photo = base_url() . $path;
    }
    else
    {
      $entry->noPhoto = true;
      $entry->photo = base_url() . 'assets/img/agenda/00' . $entry->sex . '.png';
    }
          
    // Set view
    $data['editFormAction'] = base_url() . 'agenda/saveEntry';
		$data['entry'] = $entry;
		$this->layout->view('agenda/editEntry', $data);
  }


/**
	 * Enregistre la nouvelle entrée dans l'agenda via POST
	 */
	public function saveEntry()
	{
		// On récupère quelques infos de base
    $user = $this->layout->init("agenda");
		$this->load->helper('image_helper');

		// Definitions des constantes importantes
		$PATH_PHOTOS = __DIR__ . '/../../assets/img/agenda/';
		$URL_PHOTOS = base_url() . 'assets/img/immt/';
		$HMAX_FS = 950;
		$LMAX_FS = 1200;

    // Check people_id
    $newData = new stdClass();
    $newData->people_id = $this->input->post('newPeopleId');


    // On part du principe que tout c'est bien passe, et si ya un probleme, on changera a ce moment le msg d'erreur
		$msg = "";
		$error = false;
   
		// Verification de l'upload
    $needToSavePhoto = true;
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
      $needToSavePhoto = false;
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
		
		

		// FS :
		// on redimensionne (si besoins)
		if (!$error && $needToSavePhoto)
		{
      // Creer le nom de l'image
  		$filenameWE = str_pad($newData->people_id, 3, '0', STR_PAD_LEFT);
  		$filename = $filenameWE . $objImgTmp->makeExtension();
   
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
      
			// On copie la FS et si un fichier existe deja, on le supprime
			$imageFullname = $PATH_PHOTOS . $filename;
      
      if (file_exists($imageFullname)) 
      { 
        unlink ($imageFullname); 
      }
			copy ($tmpImageFullname, $imageFullname);
			
			
		}
		
		// et on supprime l'objet temporaire cree
		unset($objImgTmp);

  
		// On continue avec le reste du formulaire
		$newData->lastname = htmlspecialchars ($this->input->post('newLastname'), ENT_QUOTES);
    $newData->firstname = htmlspecialchars ($this->input->post('newFirstname'), ENT_QUOTES);
    $newData->firstname2 = htmlspecialchars ($this->input->post('newFirstname2'), ENT_QUOTES);
    $newData->surname = htmlspecialchars ($this->input->post('newSurname'), ENT_QUOTES);
    $newData->rootfamilly = htmlspecialchars ($this->input->post('newRootFamilly'), ENT_QUOTES);
    
    $newData->sex = htmlspecialchars ($this->input->post('newSex'), ENT_QUOTES);
    $newData->birthday = explode('/', htmlspecialchars ($this->input->post('newBirthday'), ENT_QUOTES));
    $newData->deathday = explode('/', htmlspecialchars ($this->input->post('newDeathday'), ENT_QUOTES));
   
    $newData->address = htmlspecialchars ($this->input->post('newAddress'), ENT_QUOTES);
    $newData->city = htmlspecialchars ($this->input->post('newCity'), ENT_QUOTES);
    $newData->country = htmlspecialchars ($this->input->post('newCountry'), ENT_QUOTES);
		
    $newData->phone = htmlspecialchars ($this->input->post('newPhone'), ENT_QUOTES);
    $newData->mobilephone = htmlspecialchars ($this->input->post('newMobile'), ENT_QUOTES);
    $newData->email = htmlspecialchars ($this->input->post('newEmail'), ENT_QUOTES);
    $newData->skype = htmlspecialchars ($this->input->post('newSkype'), ENT_QUOTES);
    $newData->website = htmlspecialchars ($this->input->post('newWebSite'), ENT_QUOTES);
    
    //print_r($newData);
    
    if (count($newData->birthday) == 3)
    {
      $newData->birthday = mktime (0,0,0, $newData->birthday[1], $newData->birthday[0], $newData->birthday[2]);
		}
    else
    {
      $newData->birthday = null;
    }
    
    if (count($newData->deathday) == 3)
    {
      $newData->deathday = mktime (0,0,0, $newData->deathday[1], $newData->deathday[0], $newData->deathday[2]);
		}
    else
    {
      $newData->deathday = null;
    }
    
    //print_r($newData);
    
		// Enregistrement en base
    $sql = "INSERT INTO agenda_people(people_id, lastname, firstname, firstname2, surname, rootfamilly, sex, birthday, deathday, address, city, country, phone, mobilephone, email, skype, website) ";
    $sql .= "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE ";
    $sql .= "people_id=?, lastname=?, firstname=?, firstname2=?, surname=?, rootfamilly=?, sex=?, birthday=?, deathday=?, address=?, city=?, country=?, phone=?, mobilephone=?, email=?, skype=?, website=?;";
		
    $sqlData = array(
      $newData->people_id, 
      $newData->lastname, 
      $newData->firstname, 
      $newData->firstname2, 
      $newData->surname, 
      $newData->rootfamilly, 
      $newData->sex, 
      $newData->birthday, 
      $newData->deathday, 
      $newData->address, 
      $newData->city, 
      $newData->country, 
      $newData->phone, 
      $newData->mobilephone, 
      $newData->email, 
      $newData->skype, 
      $newData->website, 
      
      $newData->people_id, 
      $newData->lastname, 
      $newData->firstname, 
      $newData->firstname2, 
      $newData->surname, 
      $newData->rootfamilly, 
      $newData->sex, 
      $newData->birthday, 
      $newData->deathday, 
      $newData->address, 
      $newData->city, 
      $newData->country, 
      $newData->phone, 
      $newData->mobilephone, 
      $newData->email, 
      $newData->skype, 
      $newData->website
    );
    
    // On execute la query
    $this->db->query($sql, $sqlData);

    // On notifie sur la page d'accueil
    $msg = 'Met à jour la fiche de : ';
    $msg .= $newData->lastname . ' ' . $newData->firstname;
    logMessage($user->user_id, mktime(), 'message', 'agenda', $msg, 'agenda/browseDirectory/' . $newData->lastname[0]);

    // On redirige vers l'accueil des immt
    redirect('agenda/browseDirectory/' . $newData->lastname[0], 'refresh');
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */