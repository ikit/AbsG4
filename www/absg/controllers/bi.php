<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Bi extends CI_Controller 
{
    private $user = false;
    public $ctx = array();

    /**
     */
    public function index($data="")
    {
  		die("pouet");
  		
    }


  public function users()
  {
		$this->load->helper('download');
    $this->layout->setTheme('absg');
		$user = $this->layout->init("absg");
    
    if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}
   
		$result = $this->db->query("
      SELECT u.user_id, u.username, p.firstname, p.lastname, p.surname, p.birthday, p.deathday, p.city, p.country, p.email, p.sex, p.rootfamilly 
      FROM absg_users u
      INNER JOIN agenda_people p ON u.people_id = p.people_id
      WHERE u.user_id >= 2 and u.user_id != 11
      ORDER BY u.user_id")->result();
      
      
		$data = 'user_id,username,firstname,lastname,surname,birthday,deathday,city,country,email,sex,rootfamilly';
		foreach ($result as $row)
		{
			$data .= "\r\n\"{$row->user_id}\",\"{$row->username}\",\"{$row->firstname}\",\"{$row->lastname}\",\"{$row->surname}\",\"{$row->birthday}\",\"{$row->deathday}\",\"{$row->city}\",\"{$row->country}\",\"{$row->email}\",\"{$row->sex}\",\"{$row->rootfamilly}";
		}
		
		
		force_download("users.csv", $data);
		
		exit;
  }


  public function photos()
  {
		$this->load->helper('download');
    $this->layout->setTheme('absg');
		$user = $this->layout->init("absg");
    
    if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}
  		
		$awards = array();
		$sql = "SELECT * FROM agpa_awards";
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

		$sql = "SELECT * FROM agpa_photos";
		$result = $this->db->query($sql)->result();

	
		$data = 'photo_id,edition,filename,category_id,user_id,title,score,votes,votes_title,g_score,award_best,award_cat,award_titre,error';
		foreach ($result as $row)
		{
			$title = html_entity_decode($row->title,ENT_QUOTES);
			if (!isset($awards[$row->photo_id]))
			{
				$awards[$row->photo_id] = array(0,0,0);
			}
			
			// On double les guillemets double " (norme du format CSV pour traiter les " dans une chaine de caractère elle même délimité par des ".
			$title = str_replace("\"", "\"\"",$title);
			$edition = $row->year - 2005;
			$data .= "\r\n{$row->photo_id},{$edition},\"{$row->filename}\",{$row->category_id},{$row->user_id},\"{$title}\",{$row->score},{$row->votes},{$row->votes_title},{$row->g_score},";
			$data .= $awards[$row->photo_id][0] . "," . $awards[$row->photo_id][1] . "," . $awards[$row->photo_id][2]  . "," . $row->error;
		}
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("photos.csv", $data);
		
		exit;
  }











	

	/**
	 * Exporte au format CSV les données des votes (pour les fichiers excels de Florent)
	 */
	public function votes()
	{
		$this->load->helper('download');
    $this->layout->setTheme('absg');
		$user = $this->layout->init("absg");
    
    if ($user->auth != '*')
		{
			die("Cette fonction n'est pas pour vous :)");
		}


		// ------------------------------------------------------------------------------
		// Récupération des données sur les votes

		$sql = "SELECT * FROM agpa_votes";
		$result = $this->db->query($sql)->result();

	
    $data = 'edition,user_id,photo_id,category_id,score';
		foreach ($result as $row)
    {
      $edition = $row->year - 2005;
      $data .= "\r\n{$edition},{$row->user_id},{$row->photo_id},{$row->category_id},{$row->score}";
    }
		
		// On change la réponse du serveur pour indiquer qu'on télécharge un fichier et non une page web standart
		force_download("votes.csv", $data);
		
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
	

}



