<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forum extends CI_Controller 
{
    /**
     * Page index (résumé) du forum
     */
    public function index()
    {
	// Init layout
	$user = $this->layout->init("forum");
	$this->layout->addCss("forum");

	$data = array();


	// 1) Récupération de l'activité récente sur le forum
	//    -> On récupère les notifications de l'utilisateur concernant le forum via la variable $user
        // 1.1) Si pas de notif : 
        $lastActivities = 0;

        // 1.2) Si des notifs : On résume l'activité pour chaque sujet
        if ($user->notifications['forum']['total'] > 0)
        {
            $lastActivities = array();
            // 1.2.1) Construction de la requete
            $sql = 'SELECT t.*, f.name FROM forum_topics t , forum_forums f WHERE t.forum_id = f.forum_id AND (';
            foreach ($user->notifications['forum']['posts'] as $topic_id => $topic_posts_count)
            {
                $sql .= ' t.topic_id = ' . $topic_id . ' OR ';
            }
            $sql = substr($sql, 0, -4) . ')';

            // 1.2.2) Récupération des données
            $result = $this->db->query($sql)->result();
            foreach ($result as $topic)
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
        
        $data['lastActivities'] = $lastActivities;


	// 2) Récupération des forums dispos
	$forums = array(
	    'public' => array(),
	    'private' => array(),
	);

	$result = $this->db->query('SELECT f.*, t.topic_id FROM forum_forums f INNER JOIN forum_posts p ON f.last_post_id=p.post_id INNER JOIN forum_topics t ON p.topic_id=t.topic_id')->result();
	foreach ($result as $forum)
        {
        	if ($forum->archived || !$this->checkAuthForum($forum, $user)) continue;
            if($forum->private)
            {
            	$forums['private'][] = $forum;
            }
            else 
            {
            	$forums['public'][] = $forum;
            }
        }

	$data['forums'] = $forums;

        $this->layout->footer('forum/statsHome', $this->statsHome($user));
	$this->layout->view('forum/resume', $data);
    }


    /**
     * Consulter les sujets d'un forum
     */
    public function browse($forumId)
    {
        // Init layout
        $user = $this->layout->init("forum");
        $this->layout->addCss("forum");
        $this->layout->addJs("forum");
        $this->layout->addPlugin('jqPlot');
        $this->layout->addPlugin('tinymce');
        $this->load->helper('forum');

        $data = array();
        $data["newTopicUrl"] = base_url() . 'forum/newTopic/' . $forumId;

        $forumData = array();
        foreach (explode(',', explode(';', $user->last_activity)[4]) as $pdata)
        {
            $d = explode('-', $pdata);
            if (isset($d[1]))
                $forumData[$d[0]] = $d[1];
        }


        // 1) Récupération des sujets de discution de l'année dans ce forum 
        $topics = array(); $row = 0;
        $sql = "SELECT * FROM  forum_topics WHERE forum_id={$forumId} ORDER BY type DESC, last_post_time DESC LIMIT 0, 30";
        $result = $this->db->query($sql)->result();
        foreach ($result as $topic)
        {
            array_push($topics, array(
                'id' => $topic->topic_id,
                'title' => $topic->title,
                'replies' => $topic->replies,
                'icon' => topicIcon($topic, $forumData),
                'from_username' => $topic->first_poster_name,
                'from_userId' => $topic->first_poster_id,
                'from_date' => $this->layout->displayed_date($topic->first_post_time),
                'last_username' => $topic->last_poster_name,
                'last_date' => $this->layout->displayed_date($topic->last_post_time),
                'last_userId' => $topic->last_poster_id,
                'row' => $row,
            ));

            $row = ++$row % 2;
        }

        $data['topics'] = $topics;

        // 3) Récupération des infos sur le topic
        // $data['topic'] = $this->db->query("SELECT * FROM  forum_topics WHERE  topic_id={$topic_id} ")->result()[0];

        // 4) Récupération des infos sur le forum
        $data['forum'] = $this->db->query("SELECT * FROM  forum_forums WHERE  forum_id={$forumId} ")->result()[0];

        // 5) Construction du path et de la pagination
        $data['path'] = array(
            array('title' => $data['forum']->name,
		'url' => base_url() . 'forum/browse/' . $forumId)
        );

        $this->layout->view('forum/browse', $data);
    }


    /**
     * Création d'un nouveau sujet de discussion dans un forum donné
     */
    public function newTopic($forumId)
    {

        // Init layout
        $user = $this->layout->init("forum");
        $this->layout->addCss("forum");
        $this->layout->addJs("forum");
        $this->layout->addPlugin('tinymce');
        $this->load->helper('forum');

        $data = array();
        $data['forum'] = $this->db->query("SELECT * FROM  forum_forums WHERE  forum_id={$forumId} ")->result()[0];
        $data['formAction'] = base_url() . 'forum/newt/' . $forumId;

        // 1) Construction du path et de la pagination
        $data['path'] = array(
            array('title' => $data['forum']->name,
		'url' => base_url() . 'forum/browse/' . $forumId)
        );

        // 2) Récupérer liste des smilies affiché directement à côté de l'éditeur
        $smilies = array();
        $rep = opendir('assets/img/smilies/');
        while($fichier = readdir($rep)) 
        {
            if (is_dir($rep.$fichier) OR substr($fichier, 0, 2) == "x-") continue;
            if (preg_match("/\.gif/i",$fichier) OR preg_match("/\.png/i",$fichier)) 
            {
                $smilies[] = $fichier;
            }
        }
        closedir($rep);
        sort($smilies);

        $data['smilies'] = $smilies;
        $data['smileyBaseUrl'] = base_url() . 'assets/img/smilies/';


        $this->layout->view('forum/newTopic', $data);
    }



    /**
     * Edition d'un message donnée
     */
    public function edit($postId, $page)
    {

        // Init layout
        $user = $this->layout->init("forum");
        $this->layout->addCss("forum");
        $this->layout->addJs("forum");
        $this->layout->addPlugin('tinymce');
        $this->load->helper('forum');

        
        // 1) On récupère les infos du sujet à éditer
        $data = array();
        $data['post'] = $this->db->query("SELECT p.*, t.title, t.first_post_id, f.name FROM  forum_posts p INNER JOIN forum_topics t ON p.topic_id = t.topic_id INNER JOIN forum_forums f ON p.forum_id = f.forum_id WHERE  post_id={$postId} ")->result()[0];
        $data['formAction'] = base_url() . 'forum/update/' . $postId;
        
        
        // 2) On vérifie que l'utilisateur à bien le droit d'éditer le message
        // Todo

        
        // 3) On remplace le chemin générique des smilies par ce qui va bien
        $data['post']->text = str_replace(array('{SMILIES_PATH}'), array(base_url() . 'assets/img/smilies'), $data['post']->text);

        
        // 4) Construction du path et de la pagination
        $data['path'] = array(
            array('title' => $data['post']->name,
                  'url' => base_url() . 'forum/browse/' . $data['post']->forum_id)
        );

        
        // 5) Récupérer liste des smilies affiché directement à côté de l'éditeur
        $smilies = array();
        $rep = opendir('assets/img/smilies/');
        while($fichier = readdir($rep)) 
        {
            if (is_dir($rep.$fichier) OR substr($fichier, 0, 2) == "x-") continue;
            if (preg_match("/\.gif/i",$fichier) OR preg_match("/\.png/i",$fichier)) 
            {
                $smilies[] = $fichier;
            }
        }
        closedir($rep);
        sort($smilies);

	$data['currentPage'] = $page;
        $data['smilies'] = $smilies;
        $data['smileyBaseUrl'] = base_url() . 'assets/img/smilies/';


        $this->layout->view('forum/editPost', $data);
    }


    /**
     * Enregistrement d'un message pour un sujet donné
     */
    public function newp($topicId)
    {
        // On récupère quelques infos de base
        $user = $this->layout->init("forum");
        $sql = "SELECT * FROM  forum_topics WHERE  topic_id={$topicId}";
        $topicData = $this->db->query($sql)->result()[0];
        $forumId = $topicData->forum_id;
        $postTime = time();
        $totalReplies = $topicData->replies + 1;



        // On récupère le message dans le formulaire (editeur tinyMCE)
        $newPost =  $this->input->post('message');

	// On parse les codes smilies
        $this->parseUsualSmilies($newPost);

        // On récupère les attachments
        $attachments = 0;

        // On enregistre le nouveau message
        // On execute la requete sql avec le binding de codeigniter qui fait le boulot concernant les " ' et autres caractères chiants
        $sql  = "INSERT INTO forum_posts (`post_id`, `topic_id`, `forum_id`, `poster_id`, `time`, `text`, `attachment`)  VALUES (NULL, ?, ?, ?, ?, ?, ?);";
        $this->db->query($sql, array($topicId, $forumId, $user->user_id, $postTime, $newPost, $attachments));
        $newPostId = $this->db->insert_id();

        // On met à jours les "metadata" du sujet et du forum concernés (dernier poster, nombre de réponses, ...)
        $sql = "UPDATE  forum_forums SET `last_post_id`=?, `last_poster_name`=?, `last_poster_id`= ?, `last_post_time`=? WHERE forum_id=?;";
        $this->db->query($sql, array($newPostId, $user->username, $user->user_id, $postTime, $forumId));
        
        $sql = "UPDATE  forum_topics SET  `last_post_id`=?, `last_post_time`=?, `last_poster_id`=?, `last_poster_name`=?, `replies`=? WHERE `topic_id`=?;";
        $this->db->query($sql, array($newPostId, $postTime, $user->user_id, $user->username, $totalReplies, $topicId));

        // On redirige vers le sujet en lecture
        redirect('forum/read/' . $topicId .'/last', 'refresh');
    }

    
    /**
     * Enregistrement d'un sujet pour un forum donné
     */
    public function newt($forumId)
    {
        // On quelques infos de base
        $user = $this->layout->init("forum");
        $sql = "SELECT * FROM  forum_forums WHERE  forum_id={$forumId}";
        $forumData = $this->db->query($sql)->result()[0];
        $postTime = time();


        // On récupère le message dans le formulaire (editeur tinyMCE)
        $newTopicTitle = $this->input->post('title');
        $newTopicPost =  $this->input->post('message');

        // on remplace les url des smileys par une adresse générique : 
        $this->parseUsualSmilies($newTopicPost);

        
        // On récupère les attachments
        $attachments = 0;

        // On enregistre le nouveau message
        // On execute la requete sql avec le binding de codeigniter qui fait le boulot concernant les " ' et autres caractères chiants
        // On créer d'abord le sujet avec un id de premier post à -1 (on va ensuite créer le post en base et updater le topic avec l'id de post ainsi obtenu)
        $sql = "INSERT INTO forum_topics ( `forum_id`, `title`, `first_post_time`, `first_poster_id`, `first_poster_name`,  `last_post_time`, `last_poster_id`, `last_poster_name`) ";
        $sql.= "VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $this->db->query($sql, array($forumId, $newTopicTitle, $postTime, $user->user_id, $user->username, $postTime, $user->user_id, $user->username));
        $newTopicId = $this->db->insert_id();


        // On ajoute en base le message
        $sql  = "INSERT INTO forum_posts (`post_id`, `topic_id`, `forum_id`, `poster_id`, `time`, `text`, `attachment`)  VALUES (NULL, ?, ?, ?, ?, ?, ?);";
        $this->db->query($sql, array($newTopicId, $forumId, $user->user_id, $postTime, $newTopicPost, $attachments));
        $newPostId = $this->db->insert_id();

        // On met à jour le topic
        $sql = "UPDATE  forum_topics SET  `last_post_id`=?, `first_post_id`=? WHERE `topic_id`=?;";
        $this->db->query($sql, array($newPostId, $newPostId, $newTopicId));

        // On met à jours les "metadata" du sujet et du forum concernés (dernier poster, nombre de réponses, ...)
        $sql = "UPDATE  forum_forums SET  `last_poster_name`=?, `last_poster_id`= ?, `last_post_time`=?, `last_post_id`=? WHERE forum_id=?;";
        $this->db->query($sql, array($user->username, $user->user_id, $postTime, $newPostId, $forumId));
        

        // On redirige vers le sujet en lecture
        redirect('forum/read/' . $newTopicId .'/last', 'refresh');
    }


    /**
     * Enregistrement les modifications d'un message
     */
    public function update($postId)
    {
        // On quelques infos de base
        $user = $this->layout->init("forum");
        $postTime = time();


        // On récupère le message dans le formulaire (editeur tinyMCE)
        $newTopicTitle = $this->input->post('title'); // peut être null si le message n'est pas le premier d'un sujet
        $newPost =  $this->input->post('message');
        $page = $this->input->post('page');
        $newTopicId = $this->input->post('topicId');
        
        
	// On parse les codes smilies
        $this->parseUsualSmilies($newPost);

        // On met à jour le topic si besoin
        if ($newTopicTitle != null)
        {
            $sql = "UPDATE  forum_topics SET  `title`=? WHERE `first_post_id`=?;";
            $this->db->query($sql, array($newTopicTitle, $postId));
        }

        // On met à jours le message
        $sql = "UPDATE  forum_posts SET  `text`=? WHERE post_id=?;";
        $this->db->query($sql, array($newPost, $postId));
        
        

        // On redirige vers le sujet en lecture
        redirect('forum/read/' . $newTopicId .'/' . $page . '/' . $postId, 'refresh');
    }



    /**
     * Lecture d'une discution
     */
    public function read($topic_id, $page=0, $postId=0)
    {
        // Init layout
        $user = $this->layout->init("forum");
        $this->layout->addCss("forum");
        $this->layout->addJs("forum");
        $this->layout->addPlugin('jqPlot');
        $this->layout->addPlugin('tinymce');

        $data = array('user' => $user);

        // 1) Pour les rangs on a besoin de récupérer les infos des utilisateurs
        $ranks = array();
        foreach ($this->db->query('SELECT  `user_id` ,  `rank` FROM  `absg_users` ')->result() as $rank)
        {
            $ranks[$rank->user_id] = $rank->rank;
        }

        // 2) Récupération des infos sur le sujet
        $sql = "SELECT t.*, f.name as 'forum_name' FROM  forum_topics t INNER JOIN forum_forums f ON t.forum_id=f.forum_id WHERE  t.topic_id={$topic_id}";
        //echo $sql;
        $topic_data = $this->db->query($sql)->result()[0];

        // 3) Récupération des posts a afficher
        $posts = array();
        $side = true; $lastUser = ''; $row = 0;

        // 3.1) Calcul pagination
        $perPage = 15; $startElement=0; $currentPage = 0; $needScrollToAnchor = false;
        $totalPages = floor(max(1, $topic_data->replies-1) / $perPage);
        
        
        if ($page == 'last')
        {
	  
            $currentPage = $totalPages;
            $startElement= $currentPage * $perPage;
            $needScrollToAnchor = true;
        }
        elseif ($page == 'first')
        {
            $startElement= 0;
        }
        else
        {
            $startElement= $page * $perPage;
            $currentPage = $page;
        }
        
        if ($postId > 0)
        {
            $needScrollToAnchor = true;
        }
        
        $sql = "SELECT p.*, u.username FROM  forum_posts p, absg_users u WHERE  topic_id={$topic_id} AND p.poster_id=u.user_id ORDER BY  time ASC LIMIT {$startElement}, 15";

        $result = $this->db->query($sql)->result();
        $scrollToAnchor = 0;
        foreach ($result as $post)
        {
            // position de l'avatar par rapport au post (on alterne droite-gauche)
            //$side = ($lastUser == $post->poster_id) ? $side : !$side;
            $lastUser = $post->poster_id;

            $posts[] = array(
                'side' => ($side)? 'left' : 'right',
                'user_url' => "",
                'from_username' => fromUsername($post->username),
                'username' => $post->username,
                'avatar' => $this->layout->asset_avatar_url($post->poster_id),
                'date' => $this->layout->displayed_date($post->time),
                'rank_number' => $ranks[$post->poster_id],
                'poster_id' => $post->poster_id,
                'content' => str_replace(array('{SMILIES_PATH}'), array(base_url() . 'assets/img/smilies'), $post->text),
                'row' => $row,
                'id' => $post->post_id
            );
            $scrollToAnchor = $post->post_id;

            $row = ++$row % 2;
        }
        $data['posts'] = $posts;
        $data['needScrollToAnchor'] = $needScrollToAnchor;
        $data['scrollToAnchor'] = ($page == 'last') ? $scrollToAnchor : $postId;


        // 4) Récupération des infos sur le topic
        $data['topic'] = $this->db->query("SELECT * FROM  forum_topics WHERE  topic_id={$topic_id} ")->result()[0];

        // 5) Construction du path et de la pagination
        $data['path'] = array(
            array('title' => $topic_data->forum_name,
                  'url' => base_url() . 'forum/browse/' . $topic_data->forum_id),
            array('title' => $data['topic']->title, 
                  'url' => base_url() . 'forum/read/' . $topic_data->topic_id . '/first')
        );
        $data['pagination'] = array(
            'totalPages' => $totalPages+1, 
            'perPages'   => $perPage, 
            'currentPage' => $currentPage, 
        );
        $data['topicId'] = $topic_id;


        // 6) Récupérer liste des smilies affiché directement à côté de l'éditeur
        $smilies = array();
        $rep = opendir('assets/img/smilies/');
        while($fichier = readdir($rep)) 
        {
            if (is_dir($rep.$fichier) OR substr($fichier, 0, 2) == "x-") continue;
            if (preg_match("/\.gif/i",$fichier) OR preg_match("/\.png/i",$fichier)) 
            {
                $smilies[] = $fichier;
            }
        }
        closedir($rep);
        sort($smilies);

        $data['smilies'] = $smilies;
        $data['smileyBaseUrl'] = base_url() . 'assets/img/smilies/';


        // 7) On met a jour les infos d'activité pour supprimer les notifications (qui n'apparaitront plus qu'au prochain reload de la page)
        //
        // si id du topic parmi les notifications, on l'y enlève et met à jour les notifs de l'utilisateur
        if (isset($user->notifications['forum']["posts"][$topic_id]))
        {
            $user->notifications['forum']["total"] -= $user->notifications['forum']["posts"][$topic_id];
            unset($user->notifications['forum']["posts"][$topic_id]);


            // serialize notifications data
            $forumFinalData = ""; $forumTotal = 0;
            foreach ($user->notifications['forum']["posts"] as $t => $p)
            {
                if (!empty($t))
                {
                    $forumTotal += $p;
                    $forumFinalData .= "$t-$p,";
                }
            }
            $forumFinalData = substr($forumFinalData, 0, -1);

            // update data
            updateUserActivity($user->user_id, 'forum', $forumFinalData);
        }

        $this->layout->view('forum/read', $data);
    }

    
    
    /**
     * Supprime un post
     */
    public function delete($postId)
    {
		
        // 1) Récupération des infos sur le topic
        $postData = $this->db->query("SELECT * FROM forum_posts WHERE  post_id={$postId}")->result()[0];
        
        
	// 2) Récupération des infos sur le sujet
        $topicData = $this->db->query("SELECT * FROM forum_topics WHERE  topic_id={$postData->topic_id}")->result()[0];
        

	// 3) vérifier qu'il a bien le droit
	// Todo
	
	
	// 4) Supprimer le post
	$this->db->query("DELETE FROM forum_posts WHERE post_id={$postId}");
	
	
	// 5-1) Supprimer le topic si nécessaire
	if ($topicData->first_post_id == $postId)
	{
	    $this->db->query("DELETE FROM forum_topics WHERE  topic_id={$postData->topic_id}");
	    
	    // pour être sûr, on supprime aussi tout les posts qui on ce topic id (normalement il n'y en a qu'un)
	    $this->db->query("DELETE FROM forum_posts WHERE  topic_id={$postData->topic_id}");
	}
	// 5-2) Mettre à jour le topic
	else
	{
	    // récupérer dernier post
	    $lastPostData = $this->db->query("SELECT p.*, u.username FROM forum_posts p INNER JOIN absg_users u ON p.poster_id=u.user_id WHERE p.topic_id={$postData->topic_id} ORDER BY p.time DESC LIMIT 1")->result()[0];
	    
	    // maj db
	    $sql = "UPDATE  forum_topics SET  `last_post_id`=?, `last_post_time`=?, `last_poster_id`=?, `last_poster_name`=? WHERE `topic_id`=?;";
	    $this->db->query($sql, array($lastPostData->post_id, $lastPostData->time, $lastPostData->poster_id, $lastPostData->username, $postData->topic_id));
	    
	    $sql = "UPDATE  forum_topics SET `replies`= (SELECT count(*) FROM forum_posts WHERE topic_id=?) WHERE `topic_id`=?;";
	    $this->db->query($sql, array($postData->topic_id, $postData->topic_id));
	}
	
	
	// 6) Mettre à jour le forum
	// Récupérer dernier post du forum
        $lastForumPostData = $this->db->query("SELECT p.*, u.username FROM forum_posts p INNER JOIN absg_users u ON p.poster_id=u.user_id WHERE forum_id={$postData->forum_id} ORDER BY time DESC LIMIT 1")->result()[0];

	// maj db
	$sql = "UPDATE  forum_forums SET  `last_post_id`=?, `last_post_time`=?, `last_poster_id`=?, `last_poster_name`=? WHERE `forum_id`=?;";
	$this->db->query($sql, array($lastForumPostData->post_id, $lastForumPostData->time, $lastForumPostData->poster_id, $lastForumPostData->username, $postData->forum_id));

	
	
	// 7) Maj stats / user rank
	// Todo
	
	
	// 8) On redirige vers le sujet.
	if ($topicData->first_post_id != $postId)
	{
	    redirect('forum/read/' . $postData->topic_id .'/last', 'refresh');
	}
	else
	{
	    redirect('forum', 'refresh');
	}
    }


    /**
     * Popup permettant d'insérer des émoticons depuis l'éditeur de message 
     */
    public function browseSmilies()
    {
        // Init layout
        $this->layout->setTheme('popup');
        $this->layout->init("popup");
        $this->layout->addJs("smilies");

        $data = array();




        // L'arborescence etant toujours la meme, et pour pas que le serveur travail inutilement, elle a ete ecrite en dur
        // dans un fichier js. ce fichier js peut être généré automatiquement grace à browseSmiliesGenerator.php. 
        $rubriques = array(
            0 => "Actions",
            1 => "Emotions-Etats",
            2 => "Personnages",
            3 => "Divers",
            4 => "Specials"
        );
        $arborescence = array(
            0 => array("Transport", "Saluer", "Rechercher", "Musique", "Dormir", "Jouer", "Telephoner", "Moqueur", "Sport", "Pardonner", "Feter", "Danser", "Acquiescer", "Manger", "Rigoler", "Lire", "Refuser", "Chanter"),
            1 => array("Rougir","Peur","Triste","Malade","Deception","Tendresse","Colere","Mefiance","Content","Innocent","Etonnement","Amour"),
            2 => array("Medievals","Debiles","Membres","Mode","Celebres","Bebes","Noel","Metiers-Passions","Films","Ethnies","South_Park","BD-Manga","Personnages","Costumes","Robots"),
            3 => array(),
            4 => array("Animaux","Symboles","Ours","Cochons","Alphabet")
        );



        // Quel categorie est selectionnee ?
        $cat1 = (isset($_POST['listRubriques'])? $_POST['listRubriques'] : -1);
        $cat2 = (isset($_POST['listSections']) ? $_POST['listSections']  : 0);


        // Création des menus de sélections
        $listeRubriques = '';
        $listeSections  = '<option value="0">----------</option>';
        $id_rubrique    = 0;
        foreach ($arborescence as $menu => $ssmenu)
        {
            $check = ($cat1 == $id_rubrique) ? ' selected="selected"' : '';
            $listeRubriques .= "<option value=\"$id_rubrique\"$check>". $rubriques[$menu] . '</option>';

            if (($cat1 == $id_rubrique) or ($id_rubrique == 0 and $cat1 == -1))
            {
                $id_section = 1;
                foreach ($ssmenu as $section)
                {
                    $check2 = ($cat2 == $id_section) ? ' selected="selected"' : '';
                    $listeSections .= "<option value=\"$id_section\"$check2>$section</option>";
                    ++$id_section;
                }
            }

            ++$id_rubrique;
        }



        // Récupérartion des smilies
        function getSmilies($repertoire)
        {
            $smilies = array();
            $rep = opendir($repertoire);
            while($fichier = readdir($rep)) 
            {
                if (is_dir($repertoire."/".$fichier) OR substr($fichier, 0, 2) == "x-") continue;
                if (preg_match("/\.gif/i",$fichier) OR preg_match("/\.png/i",$fichier)) 
                {
                    $smilies[] = $fichier;
                }
            }
            closedir($rep);
            sort($smilies);
            return $smilies;
        }



        // On recupere les smilies 
        $rep = 'assets/img/smilies/';
        if ($cat1 > -1) $rep .= $rubriques[$cat1];
        if (($cat1 > -1) and ($cat2 > 0)) $rep .= '/'.$arborescence[$cat1][$cat2-1];

        $url = base_url() . $rep . '/';
        $rep = FCPATH . $rep;


        // On affiche
        $data['rubrique_selector'] = $listeRubriques;
        $data['section_selector'] = $listeSections;
        $data['smilies'] = getSmilies($rep);
        $data['smileyBaseUrl'] =$url;
        $this->layout->view('forum/browseSmilies', $data);
    }






    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Parse message and replace usual smilies code by image
     * example : "Salut :)" => "Salut <img src="..." title=":)" />"
     */
    private function parseUsualSmilies(&$post)
    {
		$post = preg_replace('#:mdr:#iUs', '<img src="{SMILIES_PATH}/illare-02.gif" alt="mdr" />', $post);
		$post = preg_replace('#:woua:#iUs', '<img src="{SMILIES_PATH}/content-07.gif" alt="très content" />', $post);
		$post = preg_replace('#:lol:#iUs', '<img src="{SMILIES_PATH}/illare-05.gif" alt="lol" />', $post);
		$post = preg_replace('#:D#iUs', '<img src="{SMILIES_PATH}/content-04.gif" alt="content" />', $post);
		$post = preg_replace('#\^\^#iUs', '<img src="{SMILIES_PATH}/content-10.gif" alt="content" />', $post);
		$post = preg_replace('#:\|#iUs', '<img src="{SMILIES_PATH}/normal.gif" alt="déçu" />', $post);
		$post = preg_replace('#:huh:#iUs', '<img src="{SMILIES_PATH}/etonne-02.gif" alt="hein?" />', $post);
		$post = preg_replace('#O_o#iUs', '<img src="{SMILIES_PATH}/etonne-04.gif" alt="quoi?!" />', $post);
		$post = preg_replace('#:gah:#iUs', '<img src="{SMILIES_PATH}/etonne-06.gif" alt="gah" />', $post);
		$post = preg_replace('#:\(#iUs', '<img src="{SMILIES_PATH}/decus-01.gif" alt="déçu" />', $post);
		$post = preg_replace('#:\'\(#iUs', '<img src="{SMILIES_PATH}/triste-02.gif" alt="triste" />', $post);
		$post = preg_replace('#\-_\-#iUs', '<img src="{SMILIES_PATH}/decus-02.gif" alt="déçu" />', $post);
		$post = preg_replace('#\&lt;_\&lt;#iUs', '<img src="{SMILIES_PATH}/mefiant-03.gif" alt="méfiant" />', $post);
		$post = preg_replace('#:gnaa:#iUs', '<img src="{SMILIES_PATH}/langue-01.gif" alt="super content !" />', $post);
		$post = preg_replace('#\&gt;\(#iUs', '<img src="{SMILIES_PATH}/colere-05.gif" alt="pas content" />', $post);
		$post = preg_replace('#:@#iUs', '<img src="{SMILIES_PATH}/colere-07.gif" alt="en colère" />', $post);
		$post = preg_replace('#\&gt;_\&lt;#iUs', '<img src="{SMILIES_PATH}/pinch.gif" alt="consterné" />', $post);
		$post = preg_replace('#B\-\)#iUs', '<img src="{SMILIES_PATH}/divers-lunette.gif" alt="intello" />', $post);
		$post = preg_replace('#:siffle:#iUs', '<img src="{SMILIES_PATH}/innocent-03.gif" alt="coupable" />', $post);
		$post = preg_replace('#:non:#iUs', '<img src="{SMILIES_PATH}/non-01.gif" alt="non" />', $post);
		$post = preg_replace('#:oui:#iUs', '<img src="{SMILIES_PATH}/oui-01.gif" alt="oui" />', $post);
		$post = preg_replace('#:rolleyes:#iUs', '<img src="{SMILIES_PATH}/rolleyes1.gif" alt="rolleyes" />', $post);
		$post = preg_replace('#:\$#iUs', '<img src="{SMILIES_PATH}/rougir-02.gif" alt="timide" />', $post);
		$post = preg_replace('#:zzz:#iUs', '<img src="{SMILIES_PATH}/zzz-05.gif" alt="dodo" />', $post);
		$post = preg_replace('#:love:#iUs', '<img src="{SMILIES_PATH}/amour-02.gif" alt="amoureux" />', $post);
		$post = preg_replace('#;\)#iUs', '<img src="{SMILIES_PATH}/content-03.gif" alt="content" />', $post);
		$post = preg_replace('#:\)\)\)#iUs', '<img src="{SMILIES_PATH}/content-09.gif" alt="moqueur" />', $post);
		$post = preg_replace('#8\-\)#iUs', '<img src="{SMILIES_PATH}/divers-cool-1.gif" alt="cool" />', $post);
		$post = preg_replace('#:ange:#iUs', '<img src="{SMILIES_PATH}/innocent-02.gif" alt="ange" />', $post);
		$post = preg_replace('#:P#iUs', '<img src="{SMILIES_PATH}/langue-02.gif" alt="moqueur" />', $post);
		$post = preg_replace('#:x#iUs', '<img src="{SMILIES_PATH}/malade-01.gif" alt="malade" />', $post);
		$post = preg_replace('#:cool:#iUs', '<img src="{SMILIES_PATH}/oui-04.gif" alt="cool" />', $post);
		$post = preg_replace('#:bye:#iUs', '<img src="{SMILIES_PATH}/salut-02.gif" alt="bye" />', $post);
		$post = preg_replace('#:\)#iUs', '<img src="{SMILIES_PATH}/content-01.gif" alt="content" />', $post);
		
        // on remplace les url des smileys par une adresse générique : 
        $post = str_replace(array('../../../assets/img/smilies/'), array('{SMILIES_PATH}/'), $post);
        $post = str_replace(array('../../assets/img/smilies/'), array('{SMILIES_PATH}/'), $post);
    }
    



    /**
     * Vérifie que l'utilisateur à bien les droits pour accéder au forum.
     */
    private function checkAuthForum($forum, $user)
    {
        $result = false;

        if ($user->auth == "*" || !$forum->private)
        {
            $result = true;
        }
        else
        {
            foreach (explode(',', $user->auth) as $fId)
            {
                if ($fId == $forum->forum_id)
                {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }





    /**
     * Construit les statistiques pour l'écran d'accueil du forum (forum/resume)
     */
    private function statsHome($user)
    {
        $this->layout->addPlugin('jqPlot');

        // 1) Global stats
        $stats = array('stats');

        $data = $this->db->query("SELECT COUNT( * ) as 't' FROM  `forum_posts`")->result()[0];
        $stats['stats']['maxPosts'] = $data->t;
        $data = $this->db->query("SELECT COUNT( * ) as 't' FROM  `forum_topics`")->result()[0];
        $stats['stats']['maxTopics'] = $data->t;
        $data = $this->db->query("SELECT COUNT( DISTINCT(poster_id) ) as 't' FROM  `forum_posts`")->result()[0];
        $stats['stats']['maxCommiters'] = $data->t;

        // 2) Répartitions posts dans les différents forum 
        $sql = "SELECT COUNT( * ) AS  'nbrPosts', f.name FROM  `forum_posts` p,  `forum_forums` f WHERE p.forum_id = f.forum_id GROUP BY p.`forum_id`  ORDER BY  `nbrPosts` DESC";
        $stats['stats']['posts'] = array
        (
            0 => array('name' => '', 'value' => 0),
            1 => array('name' => '', 'value' => 0),
            2 => array('name' => '', 'value' => 0),
            3 => array('name' => 'Les autres', 'value' => 0),
        );

        // 2.1) Parcours des données pour les mettre en forme comme il faut (on ne garde que les 3 premiers forum et on regroupe les autres)
        $result = $this->db->query($sql)->result(); $idx = 0;
        foreach ($result as $dat)
        {
            if($idx <3)
            {
                $stats['stats']['posts'][$idx]['name'] = $dat->name;
                $stats['stats']['posts'][$idx]['value'] = $dat->nbrPosts;
            }
            else
            {
                $stats['stats']['posts'][3]['value'] += $dat->nbrPosts;
            }
            $idx++;
        }

        // 2.2) Graph design
        $js = "var graphData = [['{$stats['stats']['posts'][0]['name']}',{$stats['stats']['posts'][0]['value']}],";
        $js.= "['{$stats['stats']['posts'][1]['name']}',{$stats['stats']['posts'][1]['value']}],";
        $js.= "['{$stats['stats']['posts'][2]['name']}',{$stats['stats']['posts'][2]['value']}],";
        $js.= "['{$stats['stats']['posts'][3]['name']}',{$stats['stats']['posts'][3]['value']}]];";
        $stats['stats']['jqPlotData'] = $js;


        // 3) Rangs

        // 3.1) NbrCitation et NoteG
        $nbrPosts = $this->db->query("SELECT count(*) as 'posts' FROM forum_posts WHERE poster_id={$user->user_id}")->result()[0]->posts;
        $noteG = $this->db->query("SELECT rank FROM absg_users WHERE user_id={$user->user_id}")->result()[0]->rank;
        $noteG = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);
        $noteG = $noteG[3];
        

        // 3.2) Palliers
        $stepsLevels = getRanksSteps();
        $currentLevel = findRankStep('forum', $stepsLevels, array('forum' => $nbrPosts));
        $minPosts = ($currentLevel == count($stepsLevels['forum'])-1) ? 0 : $stepsLevels['forum'][$currentLevel];
        $maxPosts = $stepsLevels['forum'][min($currentLevel+1,count($stepsLevels['forum'])-1)];
        $nextLevel = findRankStep('forum', $stepsLevels, array('forum' => $maxPosts));


        // 4) Formatage des données a afficher
        $stats['stats']['rank'] = getUserRankStats($user);
        $stats['stats']['rank']['noteg'] = $noteG;
        $stats['stats']['rank']['boundMin'] = $minPosts ;
        $stats['stats']['rank']['boundMax'] = $maxPosts;
        $stats['stats']['rank']['nbrPosts'] = $nbrPosts;
        $stats['stats']['rank']['progression'] = 'width:' . round( (min($nbrPosts, $maxPosts) - $minPosts) / ($maxPosts - $minPosts )*100,0).'px;';
        $stats['stats']['rank']['progressionValue'] =  round((min($nbrPosts, $maxPosts) - $minPosts) / ($maxPosts - $minPosts )*100,0);
        $stats['stats']['rank']['nextReward'] = ($currentLevel < count($stepsLevels['forum'])-1 ) ? '+' . ($nextLevel - $currentLevel) . ' G' : '-';
        $stats['stats']['rank']['nextStep'] = $maxPosts;

        return $stats;
    }



    /**
     * Construit les statistiques pour l'écran browse d'un forum
     */
    private function statsForum($user)
    {
        $this->layout->addPlugin('jqPlot');

        // 1) Global stats
        $stats = array('stats');

        $data = $this->db->query("SELECT COUNT( * ) as 't' FROM  `forum_posts`")->result()[0];
        $stats['stats']['maxPosts'] = $data->t;
        $data = $this->db->query("SELECT COUNT( * ) as 't' FROM  `forum_topics`")->result()[0];
        $stats['stats']['maxTopics'] = $data->t;
        $data = $this->db->query("SELECT COUNT( DISTINCT(poster_id) ) as 't' FROM  `forum_posts`")->result()[0];
        $stats['stats']['maxCommiters'] = $data->t;

        // 2) Répartitions posts dans les différents forum 
        $sql = "SELECT COUNT( * ) AS  'nbrPosts', f.name FROM  `forum_posts` p,  `forum_forums` f WHERE p.forum_id = f.forum_id GROUP BY p.`forum_id`  ORDER BY  `nbrPosts` DESC";
        $stats['stats']['posts'] = array
        (
            0 => array('name' => '', 'value' => 0),
            1 => array('name' => '', 'value' => 0),
            2 => array('name' => '', 'value' => 0),
            3 => array('name' => 'Les autres', 'value' => 0),
        );

        // 2.1) Parcours des données pour les mettre en forme comme il faut (on ne garde que les 3 premiers forum et on regroupe les autres)
        $result = $this->db->query($sql)->result(); $idx = 0;
        foreach ($result as $dat)
        {
            if($idx <3)
            {
                $stats['stats']['posts'][$idx]['name'] = $dat->name;
                $stats['stats']['posts'][$idx]['value'] = $dat->nbrPosts;
            }
            else
            {
                $stats['stats']['posts'][3]['value'] += $dat->nbrPosts;
            }
            $idx++;
        }

        // 2.2) Graph design
        $js = "var graphData = [['{$stats['stats']['posts'][0]['name']}',{$stats['stats']['posts'][0]['value']}],";
        $js.= "['{$stats['stats']['posts'][1]['name']}',{$stats['stats']['posts'][1]['value']}],";
        $js.= "['{$stats['stats']['posts'][2]['name']}',{$stats['stats']['posts'][2]['value']}],";
        $js.= "['{$stats['stats']['posts'][3]['name']}',{$stats['stats']['posts'][3]['value']}]];";
        $stats['stats']['jqPlotData'] = $js;


        // 3) Rangs

        // 3.1) NbrCitation et NoteG
        $nbrPosts = $this->db->query("SELECT count(*) as 'posts' FROM forum_posts WHERE poster_id={$user->user_id}")->result()[0]->posts;
        $noteG = $this->db->query("SELECT rank FROM absg_users WHERE user_id={$user->user_id}")->result()[0]->rank;
        $noteG = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);
        $noteG = $noteG[3];
        

        // 3.2) Palliers
        $stepsLevels = getRanksSteps();
        $currentLevel = findRankStep('forum', $stepsLevels, array('forum' => $nbrPosts));
        $minPosts = ($currentLevel == count($stepsLevels['forum'])-1) ? 0 : $stepsLevels['forum'][$currentLevel];
        $maxPosts = $stepsLevels['forum'][min($currentLevel+1,count($stepsLevels['forum'])-1)];
        $nextLevel = findRankStep('forum', $stepsLevels, array('forum' => $maxPosts));


        // 4) Formatage des données a afficher
        $stats['stats']['rank'] = getUserRankStats($user);
        $stats['stats']['rank']['noteg'] = $noteG;
        $stats['stats']['rank']['boundMin'] = $minPosts ;
        $stats['stats']['rank']['boundMax'] = $maxPosts;
        $stats['stats']['rank']['nbrPosts'] = $nbrPosts;
        $stats['stats']['rank']['progression'] = 'width:' . round( (min($nbrPosts, $maxPosts) - $minPosts) / ($maxPosts - $minPosts )*100,0).'px;';
        $stats['stats']['rank']['progressionValue'] =  round((min($nbrPosts, $maxPosts) - $minPosts) / ($maxPosts - $minPosts )*100,0);
        $stats['stats']['rank']['nextReward'] = ($currentLevel < count($stepsLevels['forum'])-1 ) ? '+' . ($nextLevel - $currentLevel) . ' G' : '-';
        $stats['stats']['rank']['nextStep'] = $maxPosts;

        return $stats;
    }

    /**
     * Construit les statistiques pour l'écran de lecture d'une discussion
     */





    /**
     * Lecture d'une discution
     */
    public function random()
    {
        // On select un sujet au hasard (tout les forums sauf "Vati Gai"
        $topic_id = $this->db->query("SelecT topic_id FROM forum_topics Where forum_id <> 5 ORDER BY RAND() LIMIT 1")->result()[0]; 

    
        redirect('forum/read/' . $topic_id->topic_id . '/first' , 'refresh');
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */