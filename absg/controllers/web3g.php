<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web3g extends CI_Controller 
{


	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		$this->resume();
	}


	/**
	 * Liste les site web de la famille de l'actualité la plus à la moins récente
	 */
	public function resume()
	{
		// Check session
		$user = $this->layout->init("web3g");
		// Init layout
        $this->layout->addCss("web3g");
        $this->layout->addJs("web3g");
		$data = array();


		// 1) On met a jour les infos d'activité pour supprimer les notifications (qui n'apparaitront plus qu'au prochain reload de la page)
		updateUserActivity($user->user_id, 'web3g', time());

		// 2) On récupère les infos sur les sites 
		$data['sites'] = array();
		$sql = 'SELECT s . * , u.username FROM web3g s, absg_users u WHERE s.last_update_by = u.user_id ORDER BY  s.last_update DESC';

		$result = $this->db->query($sql)->result();
		foreach ($result as $site)
        {
        	$site->last_update = $this->layout->displayed_date($site->last_update);
        	$data['sites'][] = $site;
        }

        $data['notification_url'] = base_url() . 'web3g/notification/';

		

		$this->layout->view('web3g/index', $data);
	}

	/**
	 * Prend en compte la notification envoyé et affiche la page résumé
	 */
	public function notification($siteId)
	{
		// Check session
		$user = checkUserSession(get_instance());

        // Initialisation du timezone par défaut :
        date_default_timezone_set ( 'Europe/Paris' );

		// 1) On sauvegarde la notification
		$sql = 'UPDATE  web3g SET  last_update = ' . time() . ', last_update_note =  "' . $this->input->post('note') . '", last_update_by =  "' . $user->user_id . '" WHERE  web_id=' . $siteId;
		$this->db->query($sql);

		// 2) On met à jour les stats de l'utilisateur (rangs)
		// TODO

		// 3) On redirige vers la page résumé
		redirect('web3g', 'refresh');
	}

	/**
	 * On ajoute un click au compteur du site
	 */
	public function clickOn($siteId)
	{
		// 1) On récupère le nombre de click actuel du sote
		$sql = 'SELECT * FROM web3g WHERE web_id = ' . $siteId;
		$click = $this->db->query($sql)->result()[0]->clicks;

		// 2) On sauvegarde la notification
		$click++;
		$sql = 'UPDATE  web3g SET  clicks = ' . $click . ' WHERE  web_id=' . $siteId;
		$this->db->query($sql);

		// 3) Ok c'est bon, on retourne un résultat pour la requete asynchrone
		echo $click;
	}
}

/* End of file web3g.php */
/* Location: ./application/controllers/web3g.php */