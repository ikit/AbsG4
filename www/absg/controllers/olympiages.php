<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Olympiages extends CI_Controller 
{
    private $user = false;

    /**
    * Index Page for this controller.
    */
    public function index()
    {
        // Init layout
        //$this->layout->setTheme('olympiages');
        $user = $this->layout->init("olympiages");
        
        //$this->load->helper('olympiages');

        $data = array();
        $data['user'] = $user;



        // Prendre 10 question au hasard .
        $data['qJson'] = json_encode($this->getQuestionSimple()); // $this->questionToJson($this->getQuestionSimple());

        /*
        [{"id":"1063","question":"Quelle com\u00e9die musicale se passe dans le quartier porto-ricain de New York ?","theme_id":"2","ratio":"0", "answer":"Grease"},{"id":"1378","question":"Qui a invent\u00e9 les lunettes \u00e0 double foyer en 1780 ?","theme_id":"5","ratio":"0", "answer":"copernique"},{"id":"1164","question":"Dans quelle ville trouve-t-on l'h\u00f4tel Raffles ?","theme_id":"1","ratio":"0", "answer":"Paris"},{"id":"1428","question":"Combien y avait-il de deniers dans un sou ?","theme_id":"3","ratio":"0", "answer":"13"},{"id":"868","question":"Quel est le nom famillier du y\u00e9ti ?","theme_id":"5","ratio":"0", "answer":"bigfoot"},{"id":"1088","question":"De quelle ann\u00e9e de son r\u00e9gne Louis XVIII data-t-il la Charte qu'il octroya lors de la Restauration en 1814 ?","theme_id":"3","ratio":"0", "answer":"1814"},{"id":"1649","question":"Qui est Genevi\u00e8ve dans <i>Les parapluies de Cherbourg<\/i> ?","theme_id":"2","ratio":"0", "answer":"la victime"},{"id":"1013","question":"Comment s'appelle le m\u00e9lange de pastis et de sirop de grenadine ?","theme_id":"6","ratio":"0", "answer":"peroquet"},{"id":"728","question":"Quel pays a \u00e9t\u00e9 victime d'une invasion sovi\u00e9tique en 1956 ?","theme_id":"3","ratio":"0", "answer":"pologne"},{"id":"519","question":"Quelle est la couleur d'un objet cramoisi ?","theme_id":"5","ratio":"0", "answer":"rouge"}]
        */
        $data['qJson'] = "[{\"id\":\"1063\",\"question\":\"Quelle com\\u00e9die musicale se passe dans le quartier porto-ricain de New York ?\",\"theme_id\":\"2\",\"ratio\":\"0\", \"answer\":\"Grease\"}]";

        //$this->processAnswer($data['qJson']);


        $this->layout->addCss("olympiages");
        //$this->layout->view('olympiages/index', $data);
        $this->layout->view('olympiages/p1_preparation', $data);
    }


    /**
    * Edition courrante des OlympiaGes
    * Affichage de la phase si une edition est en cours, sinon page d'accueil standar
    */
    public function current($category=0, $arg1=0, $arg2=0, $arg3=0)
    {
        // Init layout
        $this->layout->setTheme('agpa');
        $user = $this->layout->init("agpa");

        // Init OlympiaGes
        // $this->load->helper('olympiages');
        $this->ctx = $this->init($user, 'current');
    }



    public function newTPQuestions()
    {
        // Init layout
        //$this->layout->setTheme('olympiages');
        $user = $this->layout->init("olympiages");

        //$this->load->helper('olympiages');

        $data = array();
        $data['user'] = $user;
    }

























    /**
     * Initialise le contexte pour les OlympiaGes.
     */
    private function init($user, $section)
    {
        $ctx = array();

        // Variables importantes
        $ctx['current_year'] = date('Y');
        $ctx['section'] = $section;
        $ctx['user'] = $user;
        $ctx['is_admin'] = $user->auth == '*';
        $ctx['max_feather'] = 10;

        // ----------------------------------------------------------------------------
        // les limites des phases (N° phase => (jour, mois))
        $sql = "SELECT * FROM `jog_editions` WHERE `closed`=0";
        $current = $this->db->query($sql)->result();
        
        echo "salut"; print_r($current);if (sizeof($current) = 0)
        {
            // Pas d'edition en cours 
        }
        else
        {
            // Edition en cours
            $ctx['edition'] = array(
                'id' => $current[0]->id,
                'name' => $current[0]->name,
                'start_date' => $current[0]->start_date,
                'end_date' => $current[0]->end_date,
                'master' => $current[0]->master,
                'jdata' => $current[0]->jdata
            );

            // On recupere les deleguations et les "athletes"
            $sql = "SELECT * FROM `jog_delegations` WHERE `edition_id`={$current[0]->id}";
            $result = $this->db->query($sql)->result();
            $ctx['deleguations'] = array();
            foreach ($result as $deleguation)
            {
                $ctx['deleguations'][$deleguation->id] = $deleguation;
            }
        }

        print_r($current);

        return;
/*
        //$dataBoudaries = "1/1-15/12-17/12-21/12-25/12-30/12";
        $dataBoudaries = explode('-', $dataBoudaries);
        
        $phase_boundaries = array();
        for ($i =0; $i < count($dataBoudaries); $i++)
        {
            $phase_boundaries[$i] = explode('/', $dataBoudaries[$i]);
        }
        
        $current_time = time();
        $ctx['current_phase'] = 0;
        $ctx['phases_boundaries'] = $phase_boundaries;

        // ----------------------------------------------------------------------------
        // recuperer la phase a laquelle on est.
        do
        {
            ++$ctx['current_phase'];
            $limitMkt  = mktime(0,0,0, $phase_boundaries[$ctx['current_phase']][1], $phase_boundaries[$ctx['current_phase']][0], $ctx['current_year']);
        }
        while ($limitMkt - $current_time < 0 && $ctx['current_phase'] < 5);

        // Cas spécial, si phase 1 mais avant juin, alors phase 5 de l'année précédante
        if ($ctx['current_phase'] == 1 && $current_time < mktime(0,0,0, 6, 1, $ctx['current_year'])) 
        {
            $ctx['current_phase'] = 5; 
            $ctx['current_phase_year'] = $ctx['current_year'] - 1;
        }


        // TODO : système de dérogation des phase (pour réengistrer photo en phase 2, ou voter en phase 4 pour arranger les retardataires)
        // if ($ctx['is_admin'])
        // {
        //  $ctx['current_phase'] = 3; 
        //  $ctx['current_year'] = 2013;
        //  $ctx['current_phase_year'] = 2013;
        // }

        // ----------------------------------------------------------------------------
        // Gestion de la frise chronologique
        if ($section == 'current')
        setupTimeLine($ctx['current_phase'], $phase_boundaries, $ctx);


        // ----------------------------------------------------------------------------
        // Récupérer liste des membres
        $ctx['members'] = array();
        $sql = 'SELECT u.user_id, u.username, p.rootfamilly FROM absg_users u INNER JOIN agenda_people p ON u.people_id=p.people_id';
        $result = $this->db->query($sql)->result();
        foreach ($result as $member)
        {
            $ctx['members'][$member->user_id] = $member;
        }

        // ----------------------------------------------------------------------------
        // Récupérer liste des catégories
        $ctx['cat_number'] = 0;
        $ctx['categories'] = array();

        $sql = 'SELECT c . * , v.title as "vtitle", v.description AS  "vdescription" FROM agpa_categories c, agpa_catvariants v ';
        $sql.= 'WHERE c.has_variants = v.category_id OR c.category_id = v.category_id AND v.year ='.$ctx['current_phase_year'].' ORDER BY c.order ASC';
        $result = $this->db->query($sql)->result();
        foreach ($result as $cat)
        {
            $ctx['categories'][$cat->category_id] = $cat;
            if ($cat->category_id > 0) $ctx['cat_number']++;
        }

        // ----------------------------------------------------------------------------
        // Photos aleatoires qui défileront dans la banniere
        $ctx['slideshow'] = array();
        $sql = 'SELECT year, filename FROM agpa_photos WHERE year < ' . $ctx['current_year'];
        $query = $this->db->query($sql);
        $result =  $query->result();
        for ($i=0; $i < 40; $i++) 
        { 
            $ctx['slideshow'][] = $result[rand(0, $query->num_rows() - 1)];
        }
        

        // ----------------------------------------------------------------------------
        // Quelques stats
        $sql = 'SELECT COUNT(DISTINCT photo_id) as "photos", COUNT( DISTINCT year) as "editions", COUNT( DISTINCT user_id) as "authors" FROM agpa_photos WHERE 1';
        $data = $this->db->query($sql)->result()[0];
        $ctx['nbr_photos'] = $data->photos;
        $ctx['nbr_editions'] = $data->editions;
        $ctx['nbr_authors'] = $data->authors;


        return $ctx;
        */
    }


    private function processAnswers($jsonAnswers)
    {
        print_r($jsonAnswers);
        // 1) On converti le tableau json en objet php
        $data = json_decode($jsonAnswers);
        print_r($data);

        // 2) On récupère les réponses et stats des questions
        $sql = "SELECT id, answer, nb_asked, nb_good_answer, ratio FROM `jog_questions` Where type='simple' AND id IN (";
        foreach($data as $q)
        {
            $sql .= "{$q->id},";
        }
        $sql = substr($sql, 0, strlen($sql)-1) . ")";
        $result = $this->db->query($sql)->result();
        $answers = array();
        foreach($result as $r)
        {
            $answers[$r->id] = $r;
        }
        /*
        // 3) On calcul les bonnes réponses, les points et les stats
        foreach($data as $q)
        {
        // On compare la réponse du joueur et celle en base de donnée
        if ($this->checkAnswer($q->answer, $answers[$q->id]))
        {
        // Bonne réponse
        }
        else
        {
        // Mauvaise réponse
        }

        return $data;
        }
        */


        // 4) On met à jour la db


        // 5) On retourne le résultat

    } 


    /**
    * Vérifie si une réponse est bonne ou pas
    * @playerAnswer = la réponse du joueur à vérifier
    * @goodAnswer = la bonne réponse récupérée dans la base de donnée
    */
    private function checkAnswer($playerAnswer, $goodAnswer)
    {
        $result = false;
        // 1) split answers en token #_#

        // 2) On prend 1 par 1 les tokens de la goodAnswer, et on doit trouver pour chaque au moins une bonne réponse
        //    parmis les tokens du joueur

        // 2.1) #Token Date (#D)

        // 2.2) #Token Word (#W)

        // 2.3) #Token Multi-choix (#M)

        // 2.4) #Token Number (#N)

        // 2.5) #Token Arrondi (#A)

        return $result;
    }




    /**
    * Pioche une question au hasard (plus ou moins controlé)
    * @theme = question d'un theme en particulier (0 = pas de theme en particulier)
    * @avoidAuthor = si precisé, les question enregistrés par l'author seront ignorées
    * @difficulty = (ratio nombre de fois posé / nbr de bonne réponse) : si 1, des questions plutôt facile seront sélectionnée, si 0 au contraire c'est des dur, si -1 ignoré
    */
    private function getQuestionSimple($theme=0, $avoidAuthor=0, $difficulty=-1)
    {   
        $sql = "SELECT id, question, theme_id, ratio FROM `jog_questions` Where type='simple'";
        if ($theme > 0 & $theme <=7)
        {
            $sql .= " AND theme_id <> " . theme;
        }
        if ($avoidAuthor > 0)
        {
            $sql .= " AND author_id <> " . avoidAuthor;
        }

        $sql .= " ORDER BY RAND() Limit 10";
        $result = $this->db->query($sql)->result();

        return $result;
    }

    /**
    * COnverti le tableau de question récupéré via les méthode getQuestionsXXX au format json
    * @questionArray = le tableau des questions
    * @withAnswer = est-ce qu'on ajoute les réponses des questions dans le tableau json ?
    */
    private function questionToJson($questionArray)
    {
        $json = "[";
        foreach($questionArray as $q)
        {
            $json .= "{\"id\":\"{$q->id}\", \"question\":\"".addslashes($q->question)."\"";
            //if ($withAnswer)
            //{
            //  $json .= ", \"answer\":\"".addslashes($q->answer)."\"";
            //}
            $json .= ", \"theme_id\":\"{$q->theme_id}\", \"ratio\":\"{$q->ratio}\"},";
        }

        $json = substr($json, 0, strlen($json)-1) . "]";
        return $json;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */