<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gtheque extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		// Init layout
        $user = $this->layout->init("gtheque");
		$this->layout->init("gtheque");
		$this->layout->addCss("gtheque");


		$data = array();
		$data['user'] = $user;

		$this->layout->view('gtheque/index', $data);
	}




	public function browse($browsing='', $id=-1, $filters='')
    {
    	switch ($browsing) 
    	{
    		case 'owner':
                if ($id < 1)
                {
                    // parcourir les membres qui ont des thèques
                    $this->browseOwners($filters);
                }
                else
                {
                    // parcourir les G-thèques appartenant à $id
                    $this->browseGtheque($id, $filters);
                }
    			break;

			case 'gtheque':
    			// parcourir la G-thèque $id
    			$this->browseGtheque($id, $filters);
    			break;

    		case 'set':
    			// parcourir la collection $id
    			$this->browseSet($id, $filters);
    			break;
    		
    		default:
    			// si type indéféni : 
    			// parcourir les membres qui ont des thèques
				$this->browseOwners($filters);
    			break;
    	}
    }

/*
Requete de base à décorer en fonction des filtres/cas 
=> Retourne l'ensemble des thèques, collection et éléments d'un user

SELECT *
FROM gtheque_registers r
NATURAL JOIN gtheque_elements e
LEFT JOIN gtheque_sets s ON s.set_id = e.set_id
WHERE r.user_id=2


'bd','manga','novel','book','movie','tvshow','videogame','boardgame','miscellaneous','custom','unknow'

*/


    public function browseOwners($filters)
    {
        // Init layout
        $user = $this->layout->init("gtheque");
        $this->layout->init("gtheque");
        $this->layout->addCss("gtheque");


        $data = array();
        $data['user'] = $user;


        // Creation de la requete sql pour récurer les Gthèques des owners
        $sql = "SELECT s.type, r.user_id, u.username, s.set_id, s.title, count(*) as 'elementNumber' FROM gtheque_registers r ";
        $sql.= "NATURAL JOIN gtheque_elements e NATURAL JOIN absg_users u LEFT JOIN gtheque_sets s ON s.set_id = e.set_id GROUP BY s.set_id, r.user_id";

        // TODO : filters
        


        // Récupération des données
        $owners = array();
        $result = $this->db->query($sql)->result();
        foreach ($result as $thq) 
        {
            if (!isset($owners[$thq->user_id]))
            {
                // On crée l'entrée pour le owner <user_id>
                $owners[$thq->user_id] = array(
                    "username" => $thq->username,
                    "theques" => array()
                );
            }

            if (!isset($owners[$thq->user_id]["theques"][$thq->type]))
            {
				print_r($thq);
                // On crée l'entrée pour le owner <user_id>
                $owners[$thq->user_id]["theques"][$thq->type] = array(
                    "title" => $thq->type,
                    "totalSet" => 0,
                    "totalElmt" => 0,
                    "url" => base_url() . '/browseGtheque/' . '',
                );
            }

            // On ajoute les infos de la thèque pour l'utilisateur concerné
            $owners[$thq->user_id]["theques"][$thq->type]["totalSet"] ++;
            $owners[$thq->user_id]["theques"][$thq->type]["totalElmt"] += $thq->elementNumber;
        }



        $data['owners'] = $owners;
        $data['path'] = array();

        
        $this->layout->view('gtheque/browseOwners', $data);
    }



    public function browseOwner($id, $filters)
    {
        // Init layout
        $user = $this->layout->init("gtheque");
        $this->layout->init("gtheque");
        $this->layout->addCss("gtheque");


        $data = array();
        $data['user'] = $user;

        // controle $id
        if ($id < 1)
        {
            // 
        }


        // Creation de la requete sql pour récurer les Gthèques du owner
        $sql = "SELECT s.type, count(distinct(s.set_id)) as 'setNumber', count(*) as 'elementNumber' FROM gtheque_registers r ";
        $sql.= 'NATURAL JOIN gtheque_elements e LEFT JOIN gtheque_sets s ON s.set_id = e.set_id WHERE r.user_id=' . $id . ' Group by s.type';

        // TODO : filters
        echo $sql;


        // Récupération des données
        $theques = array();
        $result = $this->db->query($sql)->result();


        $data['theques'] = $result;



        $this->layout->view('gtheque/browseOwner', $data);
    }




    public function browseGtheque($id, $filters)
    {
        // controle $id


        // Creation de la requete sql pour récurer les sets de la Gthèque
        $sql = 'SELECT DISTINCT (e.set_id) FROM  gtheque_registers r, gtheque_elements e WHERE r.user_id=' . $id . ' AND r.element_id = e.element_id';

        // Récupération des données
        $result = $this->db->query($sql)->result();
        foreach ($result as $gtheque)
        {
            $lastActivities[] = array(
                'data' => $topic,
                'count' => $user->notifications['forum']['posts'][$topic->topic_id],
                'date' => date("j M", $topic->last_post_time),
                'time' => date("H\hi", $topic->last_post_time),
                'url' => base_url() . 'forum/read/' . $topic->topic_id . '/last',
            );
        }
    }












    public function gnereux()
    {

    }



    public function gnereuxBrowse()
    {
        // Init layout
        $user = $this->layout->init("gtheque");
        $this->layout->init("gtheque");
        $this->layout->addCss("gtheque");

        $data = array();
        $data['path'] = array(
            array( 'url' => '', 'title' => 'Gthèque'),
            array( 'url' => '', 'title' => 'Gnéreux')
            );


        $data['user'] = $user;

        // Retrieve user list
        $sql = 'SELECT g.*, u.username, p.rootfamilly FROM gnereux g ';
        $sql.= 'INNER JOIN absg_users u ON g.owner_id = u.user_id ';
        $sql.= 'INNER JOIN agenda_people p ON u.people_id = p.people_id ';
        $sql.= 'WHERE g.owner_id = ' . $user->user_id;
        $sql.= ' ORDER BY u.username, g.title ';
        $result = $this->db->query($sql)->result();
        $data['wishes'] = $result;

        // Retrieve others list
        $sql = "SELECT g.*, u.username, p.rootfamilly FROM gnereux g ";
        $sql.= 'INNER JOIN absg_users u ON g.owner_id = u.user_id ';
        $sql.= 'INNER JOIN agenda_people p ON u.people_id = p.people_id ';
        $sql.= 'WHERE g.owner_id <> ' . $user->user_id;
        $sql.= ' ORDER BY u.username, g.title';
        $result = $this->db->query($sql)->result();
        $data['others'] = $result;



        $this->layout->view('gtheque/gnereuxBrowse', $data);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */