<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grenier extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		// Init layout
		$user = $this->layout->init("grenier");


		$data = array();
		$data['user'] = $user;

		$this->layout->addCss("grenier");
		$this->layout->view('grenier/index', $data);
	}


	/**
	 * Pour parcourir les mises à jours du site
	 */
	public function updates($updateResume = '')
	{
		// Init layout
		$user = $this->layout->init("grenier");
		$this->layout->addCss("grenier");


		$data = array();
		$data['user'] = $user;


		if($updateResume != '')
		{
			$this->layout->view('grenier/updatesLog/'.$updateResume, $data);
		}
		else
		{
			$directory = __DIR__ . '/../views/grenier/updatesLog';
			$files = array();
			if($dir = opendir($directory))
			{
				while(false !== ($filename = readdir($dir)))
				{
					if($filename != '.' && $filename != '..' && $filename != 'index.html')
					{
						$number = pathinfo($directory . '/' . $filename, PATHINFO_FILENAME);
						array_unshift($files, array(
							"url" => base_url() . "grenier/updates/" . $number,
							"title" => "mise à jour du " . substr($number,6,2) . '/' . substr($number,4,2) . '/' . substr($number,0,4)
							)
						);
					}
				}
			}
			$data["files"] = $files;
			

			$this->layout->view('grenier/updatesIndex', $data);
		}
	}




	/**
	 * Permet de visualiser les différents rangs en fonction de la noteG, 
	 * Permet aussi de connaître sa position et son évolution
	 */
	public function ranks($userId = -1)
	{
		// Init layout
		$user = $this->layout->init("grenier");
		$this->layout->addCss("grenier");
		$this->layout->addPlugin('lightbox');
		$data = array();
		
		// On récupère tout les rangs
		$sql = "SELECT r.*, u.user_id, u.username, u.noteg FROM `absg_ranks` r LEFT JOIN absg_users u ON u.rank = r.code ORDER BY r.code ASC, u.noteg DESC";
		$ranks = array();
		$rank_code = "";
		foreach ($this->db->query($sql)->result() as $rank) 
		{
			if ($rank->code != $rank_code)
			{
				$ranks[$rank->code] = array(
					"title" => $rank->title,
					"mini" => $this->layout->asset_rank_mini($rank->code),
					"maxi" => $this->layout->asset_rank_maxi($rank->code),
					"gnote" => $rank->g_note,
					"active" => $rank->active,
					"users" => array()
				);
				$rank_code = $rank->code;
			}
			if ($rank->user_id != null)
			{
				$currentLevels = ($rank->noteg!='') ? explode(';', $rank->noteg) : array(false, 0, 0, 0, 0);
				$rankNote = 0;
				for ($i=1; $i < count($currentLevels); $i++) 
	            { 
	                $rankNote += $currentLevels[$i];
	            }
				$ranks[$rank->code]["users"][] = array(
					"id" => $rank->user_id,
					"avatar" => $this->layout->asset_avatar_url($rank->user_id),
					"name" => $rank->username,
					"gnote" => $rankNote
				);
			}
		}

		$data["ranks"] = $ranks;

		

		$this->layout->view('grenier/ranks', $data);
	}


}


/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */