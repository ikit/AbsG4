<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		//$this->output->enable_profiler(TRUE);

		// Init layout
		$this->benchmark->mark('Init_start');
		$user = $this->layout->init("welcom");
		$this->layout->addPlugin('lightbox');
		$data = array();
		$this->benchmark->mark('Init_end');




		// Last activities
		$this->benchmark->mark('Last_start');
		$this->updateTodayEvents();
		$this->benchmark->mark('Last_end');


		// Dernières activité du site (cached)
		// -> pour ajouter manuellement une nouvelle activité, passé par zaffa.php/newActivity
		$data['lastActivities'] = $this->layout->getCache("lastactivities_welcom_snippet");



		// Immt (cached)
		$data['immt'] = $this->layout->getCache("immt_welcom_snippet");


        // Zaffanerie
		$this->benchmark->mark('Zaffa_start');
        $data['zaffanerie'] = $this->zaffanerie_data();
		$this->benchmark->mark('Zaffa_end');



		// Passa G
		$this->benchmark->mark('PassaG_start');
		$data['presence'] = $user->passag;
		$this->benchmark->mark('PassaG_end');



		// Rank
		$this->benchmark->mark('Rank_start');
		//computeRank($user);
		$this->benchmark->mark('Rank_end');


		// Stats
		$this->benchmark->mark('Stats_start');
        $this->layout->footer('welcom/stats', $this->stats($user));
		$this->benchmark->mark('Stats_end');


		// Render
		$this->benchmark->mark('Render_start');
        $this->layout->addCss('welcom');
		$this->layout->view('welcom/index', $data);
		$this->benchmark->mark('Render_end');
	}



    private function zaffanerie_data()
    {
    	$date = mktime(13,30,0,3,2,2010);

    	//echo $date . " > " . date("H:i d/m/Y", $date);
    	// echo mktime();

        // get the date to get the Zaffanerie of the day
        //$date = 
        $data = array();
        $data['url'] = base_url() . 'assets/img/zaffaneries/00004.png';
        $data['alt'] = '10 ans de bons et loyaux services !';
        return $data;
    }



	private function stats($user)
	{

		// Global stats
		$sql = 'SELECT u.user_id, u.people_id, u.username, p.rootfamilly, p.sex FROM  absg_users u, agenda_people p WHERE u.people_id = p.people_id';
		$data = $this->db->query($sql)->result();
		$stats = array('stats');
		$stats['stats']['userRankUrl'] = base_url() . 'grenier/ranks/' . $user->user_id;
		$stats['stats']['nbrMembres'] = count($data) -1; // on ne compte pas le membre spécial Zaffa.
		$stats['stats']['nbrF'] = 0;
		$stats['stats']['nbrM'] = 0;
		$stats['stats']['gueudelot'] = 0;
		$stats['stats']['guibert'] = 0;
		$stats['stats']['guyomard'] = 0;
		$stats['stats']['létot'] = 0;
   
		foreach ($data as $userData) 
		{
   			++$stats['stats']['nbr'.$userData->sex];
      
			if (isset($userData->rootfamilly) && $userData->rootfamilly != "")
				++$stats['stats'][$userData->rootfamilly];
        
		}
   

		// Frequentation
		$sql = "SELECT * FROM  absg_current_data";
		$data = $this->db->query($sql)->result();
		foreach ($data as $row) 
		{
			switch ($row->key)
			{
				case 'stat_max_user_online':
					$stats['stats']['maxOnline'] = $row->value;
					break;
				case 'stat_max_user_date':
					$stats['stats']['maxOnlineDate'] = $this->layout->displayed_date($row->value);
					break;
				case 'stat_max_visitor_by_day':
					$stats['stats']['maxVisitor'] = $row->value;
					break;
				case 'stat_max_visitor_date':
					$stats['stats']['maxVisitorDate'] = $this->layout->displayed_date($row->value, 'date');
					break;
				default:
					break;
			}
		}

		// Rang
		$stats['stats']['rank'] = getUserRankStats($user);


		return $stats;
	}




	/* Méthode appelé une fois par jour : se charge de regarder si il y a des notifications automatiques à indiquer aujourd'hui
	 * Par exemple des anniversaires, ou un changement de phase au AGPA, etc.
	 */
	private function updateTodayEvents()
	{
		// 1) On regarde à quand remonte la dernière vérification des auto notif
		$sql = "SELECT * FROM  `absg_current_data` WHERE  `key` = 'log_last_autocheck'";
        $last_time = $this->db->query($sql)->result()[0];

        if (date("Ymd", $last_time->value) == date ("Ymd",time()))
        {
        	// Déjà fait aujourd'hui
        	return;
        }

        // 2) Les anniversaires
		$sql = 'SELECT * FROM agenda_people WHERE DATE_FORMAT(birthday, "%m%d")='.date ("md",time());
        $birthday = $this->db->query($sql)->result();
        $msg = "Joyeux anniversaire &agrave; ";
        $deltaDate=0;

        foreach ($birthday as $bd) 
        {
        	$name = ($bd->surname !== null) ? $bd->surname : $bd->firstname;
        	$date = mktime(0,0,$deltaDate, date ("m",time()), date ("d",time()), date ("Y",time()));
        	$deltaDate++;

          if ($deltaDate > 0) $msg .= ", ";
          $msg .= $name;
          
        	
        }
        if ($deltaDate > 0)
        {
          $msg .= " !";
          // Comme on gère d'une façon particulière la date du log, on ne passe pas par la méthode user_helper.php > addLog(...)
          $this->db->query("INSERT INTO absg_logs (`user_id`, `date`, `type`, `module`, `message`) VALUES (1, {$date}, 'message', 'birthday', ?);", $msg);
        }
        // 3) Zaffanerie
        // Todo

        // 4) Phase des AGPA
        // Todo

        // On met à jour log_last_autocheck pour ne plus refaire le boulot aoujourd'hui
        $this->db->query("UPDATE  absg_current_data SET value=".time()." WHERE `key` = 'log_last_autocheck'");

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */