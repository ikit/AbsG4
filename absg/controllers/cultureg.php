<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cultureg extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		// Init layout
		//$this->browse($page);
		
		
		// Init
		$user = $this->layout->init( "cultureg");
        $this->layout->addCss("cultureg");


		$data = array();

		$this->layout->view('cultureg/index', $data);
	}
	
	
	
	public function browse($filters="all", $page=0)
	{
		// Init layout
		$user = $this->layout->init("cultureg");
        $this->layout->addCss("cultureg");
        $this->layout->addJs("cultureg");
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */