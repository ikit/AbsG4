<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gling extends CI_Controller 
{
	private $user = false;
	
	
	
	
	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		//$this->output->enable_profiler(TRUE);


		$this->browse();

		
	}

	
	public function browse($y1=0, $m1=0, $y2=0, $m2=0)
	{
		// Init layout
		$user = $this->layout->init("gling");
		$this->layout->addPlugin('lightbox');
		$data = array();
		$data['months'] = array();
		
		
		$sql = 'SELECT * FROM gling_operations';
		if ($y1 == 0) $y1 = date("Y");
		if ($m1 == 0) $m1 = date("m");
		
		
		$sql .= " WHERE date >= '$y1-$m1-01'";
		
		if ($y2 >= $y1)
		{
			if ($m2 <= 0 || $m2 > 12) $m2 = 12;
			$sql .= " AND date <= '$y2-$m2-" . cal_days_in_month(CAL_GREGORIAN, $m2, $y2) . "'";
		}
		
		
		
		$sql .= " ORDER BY Year(date) ASC, Month(date) ASC, budget_id ASC, date ASC";
		print_r($sql);

		$result = $this->db->query($sql)->result();
		
		
		foreach ($result as $operation) 
		{
			$dateKey = date('Y-m', strtotime($operation->date));
			
			// Prepare data
			if (!isset($data['months'][$dateKey]))
			{
				$data['months'][$dateKey] = array(
					'operations' 	=> array(),
					'budgets' 		=> array(),
					'totalIN'		=> 0,
					'totalOUT'		=> 0,
					'title'			=> date('Y m', strtotime($operation->date)),
					'remuneration'  => 0
				);
			}
			
			if (!isset($data['months'][$dateKey]['budgets'][$operation->budget_id]))
			{
				$data['months'][$dateKey]['budgets'][$operation->budget_id] = array('max' => 0, 'total' => 0, 'label' => 'pouet');
			}
			
			
			// Save data
			$data['months'][$dateKey]['operations'][] = array(
				'amount' => $operation->amount,
				'budget' => $operation->budget_id,
				'date'   => $operation->date
			);
			$data['months'][$dateKey]['budgets'][$operation->budget_id]['total'] += $operation->amount;
			if ($operation->amount < 0)
			{
				$data['months'][$dateKey]['totalOUT'] += -$operation->amount;
			}
			else
			{
				$data['months'][$dateKey]['totalIN'] += $operation->amount;
			}
			
		}

		print_r($data);
		// 
		//$data['immt'] = $this->layout->getCache("immt_gling_snippet");



		// Stats
        //$this->layout->footer('gling/stats', $this->stats($user));


		// Render
        $this->layout->addCss('gling');
		//$this->layout->view('gling/index', $data);
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
			if ($userData->rootfamilly !== null)
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

        $deltaDate=0;

        foreach ($birthday as $bd) 
        {
        	$name = ($bd->surname !== null) ? $bd->surname : $bd->firstname;
        	$date = mktime(0,0,$deltaDate, date ("m",time()), date ("d",time()), date ("Y",time()));
        	$deltaDate++;

        	// Comme on gère d'une façon particulière la date du log, on ne passe pas par la méthode user_helper.php > addLog(...)
        	$this->db->query("INSERT INTO absg_logs (`user_id`, `date`, `type`, `module`, `message`) VALUES (1, {$date}, 'message', 'birthday', {$msg});");
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