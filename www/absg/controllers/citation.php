<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Citation extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index($author="undefined")
	{
		// Init layout
		$this->browse($author);
	}



	/**
	 * Parcourir les citations
	 */
	public function browse($author="undefined", $page=0)
	{
		// On active la mise en cache pour cette page à 1 jour : 24h = 1440 minutes
		//$this->output->cache(1440);

		// Init layout
		$user = $this->layout->init("citation");
		$this->layout->addCss("citation");
		$this->layout->addJs("citation");
		$data = array();

		
		// 1) On récupère le nombre total de citation
		$nbrPerPage = 30;
		$sql = 'SELECT Count(*) as "t" FROM citations';
		if ($author != "undefined")
		{
			$sql .= ' WHERE author_id=' . $author;
		}
		$data['newFormAction'] = base_url() . 'citation/newCitation';
		$data['totalCitations'] = $this->db->query($sql)->result()[0]->t;
		$data['totalPage'] = ceil($data['totalCitations'] / $nbrPerPage);
		$data['currentPage'] = $page;

		// 2) On met a jour les infos d'activité pour supprimer les notifications
		updateUserActivity($user->user_id, 'citation', $data['totalCitations']);
		$user->notifications['citations'] = 0;
	

		// 3) On construit la requête sql avec les paramètres
		$sql = 'SELECT c.citation_id, c.citation , p.firstname , p.surname FROM  citations c,  agenda_people p WHERE  c.author_id = p.people_id';

		if ($author != "undefined")
		{
			$sql .= ' AND c.author_id=' . $author;
		}
		$sql .= ' ORDER BY c.citation_id DESC LIMIT ' . ($page * $nbrPerPage) .', ' . $nbrPerPage;


		// 4) On récupère les citations et on les affiches
		$citationsList = array();
		$result = $this->db->query($sql)->result();
		foreach ($result as $citation)
        {
            $citationsList[] = $citation;
        }
        $data['citations'] = $citationsList;


		// 5) On récupère la liste des auteurs de citations pour les filtres
		$authorsList = array();
		$result =$this->db->query('SELECT DISTINCT (c.author_id), p.firstname , p.surname FROM  citations c,  agenda_people p WHERE c.author_id = p.people_id ')->result();
		foreach($result as $auth)
		{
			$authorsList[] = array($auth->author_id, (!empty($auth->surname)) ? $auth->surname : $auth->firstname);
			
		}
		function sortAlpha($a, $b) { return strcmp($a[1], $b[1]); }
		usort($authorsList, 'sortAlpha');
		$data['authors'] = $authorsList;
		$data['currentAuthor'] = $author;

		$cit = $data['citations'][0];
		$filterAuthor = fromUsername( (!empty($cit->surname)) ? $cit->surname : $cit->firstname);

		$data['fromAuthor'] = ($author!="undefined") ? $filterAuthor : '';


		$this->layout->footer('citation/stats', $this->stats($user));
		$this->layout->view('citation/browse', $data);

		
		
		
		// 5) On prépare les données qu'on envoie au template pour qu'il les utilises
	}



	/**
	 * Enregistre la nouvelle citation via POST
	 */
	public function newCitation()
	{
		// On récupère quelques infos de base
        $user = $this->layout->init("immt");
        $authorId = null;
        $citation = $this->input->post('newCit');
        $author = $this->input->post('newAuthor');

        // On vérifie que l'auteur et la citation ne sont pas vide...
        if ($citation != "" && $author != "")
        {

        	// On cherche à récupérer l'auteur de la citation
	        $result = $this->db->query("SELECT `people_id`, `firstname`, `surname` FROM `agenda_people` WHERE `surname` LIKE ? or `firstname` LIKE ?", array($author, $author))->result();

	        if (count($result) > 0)
	        {
	        	$authorId = $result[0]->people_id;
	        }
	        else
	        {
	        	// Si il n'existe pas, il faut créer une entré dans la base people
	        	$this->db->query("INSERT INTO  agenda_people (`surname` , `sex`) VALUES (?,  'M');", array($author));
	        	$authorId = $this->db->insert_id();
	        }

	        // On sauvegarde la citation
	        $citation = str_replace('((', '<span class="note">(', $citation);
	        $citation = str_replace('))', ')</span>', $citation);

	        $this->db->query("INSERT INTO  citations (`poster_id` , `citation`, `author_id`) VALUES (?,  ?, ?);", array($user->user_id, $citation, $authorId));


		    // On notifie sur la page d'accueil
		    logMessage($user->user_id, mktime(), 'message', 'citation', 'Ajoute une nouvelle citation', 'citation');

		}

        // On redirige vers l'accueil des citations
        redirect('citation', 'refresh');
	}



	/**
	 * Calcul les statistiques générales pour les citations
	 */
	private function stats($user)
	{
		// On active la mise en cache pour cette page à 1 jour : 24h = 1440 minutes
		//$this->output->cache(1440);

		$this->layout->addPlugin('jqPlot');

		// 1) Global stats
		$sql = 'SELECT COUNT( * ) AS t, COUNT( DISTINCT (`poster_id`) ) AS p, COUNT( DISTINCT (`author_id`) ) AS a FROM  `citations`';
		$data = $this->db->query($sql)->result()[0];
		$stats = array('stats');
		$stats['stats']['userRankUrl'] = base_url() . 'grenier/ranks/' . $user->user_id;
		$stats['stats']['maxCitations'] = $data->t;
		$stats['stats']['maxAuthor'] = $data->a;
		$stats['stats']['maxCommiters'] = $data->p;

		// 2) Best authors
		$sql = 'SELECT COUNT( * ) as c, p.firstname, p.surname  FROM  citations c INNER JOIN agenda_people p ON p.people_id = c.author_id  GROUP BY c.author_id ORDER BY c DESC';
		$data = $this->db->query($sql)->result();
		$other = array('name' => 'Les '. (count($data) - 3).' autres', 'value' => 0);
		for ($i=3; $i < count($data); $i++) 
		{ 
			$other['value'] += $data[$i]->c;
		}
		$stats['stats']['authors'] = array
		(
			0 => array('name' => ($data[0]->surname != null) ? $data[0]->surname : $data[0]->firstname, 'value' => $data[0]->c),
			1 => array('name' => ($data[1]->surname != null) ? $data[1]->surname : $data[1]->firstname, 'value' => $data[1]->c),
			2 => array('name' => ($data[2]->surname != null) ? $data[2]->surname : $data[2]->firstname, 'value' => $data[2]->c),
			3 => $other,
		);


		// 2.2) Graph design
		$js = "var graphData = [['{$stats['stats']['authors'][0]['name']}',{$stats['stats']['authors'][0]['value']}],";
		$js.= "['{$stats['stats']['authors'][1]['name']}',{$stats['stats']['authors'][1]['value']}],";
		$js.= "['{$stats['stats']['authors'][2]['name']}',{$stats['stats']['authors'][2]['value']}],";
		$js.= "['{$stats['stats']['authors'][3]['name']}',{$stats['stats']['authors'][3]['value']}]];";
		$stats['stats']['jqPlotData'] = $js;


		// 3) Rangs

		// 3.1) NbrCitation et NoteG
		$nbrCitation = $this->db->query("SELECT count(*) as 'citations' FROM citations WHERE poster_id={$user->user_id}")->result()[0]->citations;
        $noteG = $this->db->query("SELECT rank FROM absg_users WHERE user_id={$user->user_id}")->result()[0]->rank;
        $noteG = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);
        $noteG = $noteG[1];
        

        // 3.2) Palliers
		$stepsLevels = getRanksSteps();
		$currentLevel = findRankStep('citation', $stepsLevels, array('citation' => $nbrCitation));
		$minCitations = ($currentLevel == count($stepsLevels['citation'])-1) ? 0 : $stepsLevels['citation'][$currentLevel];
		$maxCitations = $stepsLevels['citation'][min($currentLevel+1,count($stepsLevels['citation'])-1)];
		$nextLevel = findRankStep('citation', $stepsLevels, array('citation' => $maxCitations));


		// 4) Formatage des données a afficher
		$stats['stats']['rank'] = getUserRankStats($user);
		$stats['stats']['rank']['noteg'] = $noteG;
		$stats['stats']['rank']['boundMin'] = $minCitations ;
        $stats['stats']['rank']['boundMax'] = $maxCitations;
        $stats['stats']['rank']['nbrCitation'] = $nbrCitation;
        $stats['stats']['rank']['progression'] = 'width:' . round( (min($nbrCitation, $maxCitations) - $minCitations) / ($maxCitations - $minCitations )*100,0).'px;';
        $stats['stats']['rank']['progressionValue'] =  round((min($nbrCitation, $maxCitations) - $minCitations) / ($maxCitations - $minCitations )*100,0);
        $stats['stats']['rank']['nextReward'] = ($currentLevel < count($stepsLevels['citation'])-1 ) ? '+' . ($nextLevel - $currentLevel) . ' G' : '-';
        $stats['stats']['rank']['nextStep'] = $maxCitations;

		return $stats;
	}

}

/* End of file citation.php */
/* Location: ./application/controllers/citation.php */