<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller 
{
	private $userData = false;



	private function returnError($errCode, $data)
	{
		$this->load->view('api/error', array("status" => $errCode, "data" => json_encode($data)));
	}

	private function returnResult($data)
	{
		$this->load->view('api/result', array("data" => json_encode($data)));
	}


	/**
	 * 
	 */
	public function index()
	{
	    $data = array("version" => "1.0");
	    $this->returnResult($data);
	}




	public function login()
	{
		// Special init for login without auto verification of user session
		//$this->layout->setTheme('login');
		//$this->layout->init('login', true);


		$this->load->library('encrypt');

		$username =  $this->input->post('username');
		$pwd =  $this->input->post('mdp');

		$userData = initUserFromLogin($this, $username, $pwd);
		$data = array(
			"username" => $username,
			"mdp" => $pwd);

    	if ($userData)
    	{
    		// On est connecté, on retourne à l'accueil du site
    		$this->returnResult($userData);
    	}
    	else
    	{
    		// Infos saisis incorrectes, on relance la procédure de login
    		$this->returnError(402, $data);
    	}
	}



	public function attempt()
	{
		// Special init for login without auto verification of user session
		$this->layout->setTheme('login');
		$this->layout->init('login', true);

		// Load library for form validation
		$this->load->library('form_validation');
		$this->load->library('encrypt');

		$data = array();
		$data['session_id'] = $this->session->userdata('session_id');
		$data['attempt'] = $this->session->userdata('attemptNumber');

		// On précise les règles de validation du formulaire
		$this->form_validation->set_rules('username', '"Nom d\'utilisateur"', 'trim|required|max_length[25]|encode_php_tags|xss_clean');
    	$this->form_validation->set_rules('mdp',    '"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');
 
 		// On vérifie que le formulaire est correctement rempli et on vérifie si on peut log l'utilisateur ou pas
	 	if($this->form_validation->run())
	    {
	    	$userData = initUserFromLogin($this, $this->input->post('username'), $this->input->post('mdp'));

	    	if ($userData)
	    	{
	    		// On est connecté, on retourne à l'accueil du site
	    		redirect('welcome', 'refresh');
	    	}
	    	else
	    	{
	    		// Infos saisis incorrectes, on relance la procédure de login
	    		redirect('user/login', 'refresh');
	    	}
	    }
	    else
	    {
	        //  Le formulaire est invalide ou vide
			$res = $this->layout->setTheme('login');
	        $this->layout->view('user/login', $data);
	    }
	}




	public function logout()
	{
		//  Détruit la session
	    $this->session->sess_destroy();
	 
	    //  Redirige vers la page d'accueil
	    redirect('welcome', 'refresh');
	}



	public function add()
	{
		// Todo : check admin auth
		print_r($this->session->userdata('user'));

		$this->layout->setTheme('login');
		$this->layout->view('user/new', $data);
	}





	public function profilpwd()
	{
		// Init layout
		$user = $this->layout->init("profil");
		$this->layout->addCss("user");
		$this->layout->addPlugin('jquery-ui');
		$data = array();

		// Load library for form validation
		$this->load->library('form_validation');
		$this->load->library('encrypt');

		// Get user data to preset form fields
		


		// On précise les règles de validation des formulaire
    	$this->form_validation->set_rules('oldpwd',    	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');
    	$this->form_validation->set_rules('newpwd',    	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');
    	$this->form_validation->set_rules('newpwd2',	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');


		// On vérifie que le formulaire est correctement rempli
	 	if($this->form_validation->run())
	    {
	    	$userData = initUserFromLogin($this, $this->input->post('username'), $this->input->post('mdp'));

	    	if ($userData)
	    	{
	    		// Infos saisis correctes, on sauvegarde et on affiche la fiche profile modifiée
	    		$this->layout->view('user/changePassword', $data);
	    	}
	    	else
	    	{
	    		// Infos saisis incorrectes, on réaffiche le formulaire
	    		$this->layout->view('user/changePassword', $data);
	    	}
	    }
	    else
	    {
			$this->layout->view('user/changePassword', $data);
		}
	}

	public function changePassword()
	{
		// Load library for form validation
		$user = $this->layout->init("profil");
		$this->layout->addCss("user");
		$this->load->library('form_validation');
		$this->load->library('encrypt');

		$data = array();

		// On précise les règles de validation des formulaire
    	$this->form_validation->set_rules('oldpwd',    	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');
    	$this->form_validation->set_rules('newpwd',    	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');
    	$this->form_validation->set_rules('newpwd2',	'"Mot de passe"',       'trim|required|max_length[25]|encode_php_tags|xss_clean');


		// On vérifie que le formulaire est correctement rempli
	 	if($this->form_validation->run())
	    {
			$encodedOldPwd  = $this->encrypt->sha1($this->input->post('oldpwd'));
			$encodedNewPwd  = $this->encrypt->sha1($this->input->post('newpwd'));
			$encodedNewPwd2 = $this->encrypt->sha1($this->input->post('newpwd2'));

	    	// On verifie 
			if ($encodedNewPwd == $encodedNewPwd2 && $user->password == $encodedOldPwd)
	    	{
		        $sql = "UPDATE  absg_users SET  password =  ? WHERE  user_id = ? ;";
		        $userdata = $this->db->query($sql, array($encodedNewPwd, $user->user_id));
		        redirect('welcome', 'refresh');
	    	}
	    	else
	    	{
	    		// Infos saisis incorrectes, on réaffiche le formulaire
	    		$data["error"] = "Erreur lors de la saisi. Impossible de mettre à jour le mot de passe.";
	    		$this->layout->view('user/changePassword', $data);
	    	}
			

	    }
	    else
	    {
			$this->layout->view('user/profil', $data);
		}
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */