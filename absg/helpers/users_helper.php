<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Vérifie si la session de l'utilisateur est valide ou pas. Récupère et stock les 
 * infos de l'utilisateur ou bien redirige vers l'écran de login
 *
 * @param $CI, le context (à récupérer avec get_instance() par exemple)
 *
 */
if ( ! function_exists('checkUserSession'))
{
    function checkUserSession($CI)
    {
        // Récupérer les infos via la variable de session 
        $userdata =& $CI->session->userdata('user');

        // si la session est définie, on charge
        if ($userdata)
        {
            // on met à jour les infos de notifications

            $CI->benchmark->mark('Init_Notifications_start');
            $userdata = updateUserNotifications($CI, $userdata);
            $CI->benchmark->mark('Init_Notifications_end');

            // On met à jour (si besoin) les infos de présente (Passa G)
            $CI->benchmark->mark('Init_Presence_start');
            $userdata->passag = logPresence($userdata);
            $CI->benchmark->mark('Init_Presence_end');

            return $userdata;
        }

        // If here : session or user data are invalid : redirect to login screen (with 403 error code : not allowed)
        redirect('user/login', 'refresh');
    }
}


/**
 * Essaye de créer l'utilisateur avec les identifiants données.
 * Si identifiants erronés, retourne l'utilisateur anonyme
 *
 * @param $username, le login de l'utilisateur
 * @param $password, le mot de passe de l'utilisateur (non crypté)
 * @return array, avec les infos User
 *
 */
if ( ! function_exists('initUserFromLogin'))
{
    function initUserFromLogin($CI, $username, $password)
    {
        $encodedPwd = $CI->encrypt->sha1($password);

        // On regarde en BD si on trouve l'utilisateur demandé.
        $sql = "SELECT u.user_id, u.people_id, u.username, u.auth, u.noteg, u.rank, u.last_activity, p.firstname, p.lastname, p.surname, p.sex, p.birthday, p.rootfamilly FROM absg_users u NATURAL JOIN agenda_people p WHERE username_clean = ? AND password = ?";
        $userdata = $CI->db->query($sql, array(cleanUsername($username), $encodedPwd))->result()[0];

        //echo "Name : " . (cleanUsername($username)) . "<br/>";
        //echo "PWD : " . $encodedPwd . "<br/>";

        
        // On a pu récupérer les données utilisateur, alors l'identification a réussi. On connecte l'utilisateur
        if ($userdata)
        {
            $CI->session->unset_userdata('attemptNumber');
            $CI->session->set_userdata('user', $userdata);

            // Si mot de passe = <defaut> '0b9c2625dc21ef05f6ad4ddf47c5f203837aa32c'-"toto" , on force l'utilisateur à en changer :
            if ($password == '0b9c2625dc21ef05f6ad4ddf47c5f203837aa32c' && strpos($_SERVER["REQUEST_URI"], "/user/") === false)
            {
                redirect('user/profilpwd', 'refresh');
            }
        }

        return $userdata;
    }
}


/**
 * Transcrit une chaîne de caractère quelconque en une chaine équivalante
 * sans aucun caractères spéciaux, signe de ponctuation, ni majuscule
 *
 * @param $username, le login de l'utilisateur à convertir
 * @return string, le username "nettoyé"
 */
if ( ! function_exists('cleanUsername'))
{
    function cleanUsername($username)
    {
        // L'algo de cette méthode est partagé avec la méthode js cleanUsername de login.js. PEnser à modifier les 2 en même temps.
        
        // On met en minuscule
        $username = strtolower($username);

        // On supprime accent, et apostrophe
        $symbolToReplace = array('à', 'ä', 'â', '@', 'ç', 'É', 'é', 'è', 'ë', 'ê', 'ï', 'î', 'ô', 'ö', 'ù', 'û', 'ü');
        $escapedSymbols =  array("a", "a", "a", "a", "c", "e", "e", "e", "e", "e", "i", "i", "o", "o", "u", "u", "u");
        return str_replace($symbolToReplace, $escapedSymbols,  $username);
    }
}


/**
 * Construit la chaine "de $username" ou "d'$username" en fonction du nom
 *
 * @param $username, le login de l'utilisateur à traiter
 * @return string, le username 
 */
if ( ! function_exists('fromUsername'))
{
    function fromUsername($username)
    {
        // L'algo de cette méthode est partagé avec la méthode js cleanUsername de login.js. PEnser à modifier les 2 en même temps.
        $voyelles = array('a', 'e', 'i', 'o', 'u', 'y', 'h');
        return (in_array(substr(cleanUsername($username), 0,1), $voyelles)) ? 'd\''.$username : 'de '.$username;
    }
}



/**
 * Vérifie les dernières activités sur le site et notifie l'utilisateur en fonciton
 *
 * @param $CI, le context (à récupérer avec get_instance() par exemple)
 * @param $userdata, les infos de base de l'utilisateur (voir méthode checkUserSession)
 * @return $userdata, les infos de l'utilisateur avec les infos de notification en plus
 */
if ( ! function_exists('updateUserNotifications'))
{
    function updateUserNotifications($CI, &$userdata)
    {

        $data = explode(';', $userdata->last_activity);
        $userdata->notifications = array();
        $currentTime = time();


        // 1) Citations
        $citation = $CI->db->query('SELECT Count(*) as "t" FROM citations')->result()[0]->t;
        $userdata->notifications['citations'] = $citation - $data[2];

        // 2) Immt
        $immt = $CI->db->query('SELECT Count(*) as "t" FROM immt')->result()[0]->t;
        $userdata->notifications['immt'] = $immt - $data[3];

        // 3) Forum posts 
        // Ca ne sert a rien de récupérer les messages non lu trop vieux. donc on ne récupère que les nouveau message depuis la derni�re visite (max 90 jours)
        $lastvisit = max ($data[0], time() - 7776000);
        $result = $CI->db->query('SELECT p.*, f.private FROM forum_posts p NATURAL JOIN forum_forums f WHERE time >' . $lastvisit)->result();
        $posts = array();
        foreach ($result as $post)
        {
            $posts[] = $post;
        }
        $forumData = array();
        foreach (explode(',', $data[4]) as $pdata)
        {
            $d = explode('-', $pdata);
            if (isset($d[1]))
                $forumData[$d[0]] = $d[1];
        }

        $privatesForums = array();
        if( strlen($userdata->auth) > 1) 
           $privatesForums = explode(',', $userdata->auth);
        
        foreach ($posts as $post)
        {
            if (isset($forumData[$post->topic_id]))
            {
                // topic déjà connu, on incrémente avec le nouveau post
                $forumData[$post->topic_id]++;
            }
            else
            {
                // topic non connu, on check si le forum contenant le sujet est accessible à l'utilisateur
                if ($post->private == 0 || $userdata->auth == "*" || in_array($post->forum_id, $privatesForums))
                {
                    $forumData[$post->topic_id] = 1;
                }
            }
        }
        $forumFinalData = ""; $forumTotal = 0;
        foreach ($forumData as $t => $p)
        {
            if (!empty($t))
            {
                $forumTotal += $p;
                $forumFinalData .= "$t-$p,";
            }
        }
        $forumFinalData = substr($forumFinalData, 0, -1);
        
        // 4) AGPA notification
        $userdata->notifications['agpa'] = "!";


        // 5) Web3G notication
        if (count($data) >= 5)
        {
            $web3g = $CI->db->query('SELECT Count(*) as "t" FROM web3g WHERE last_update > '. $data[5])->result()[0]->t;
            $userdata->notifications['web3g'] = $web3g;
        }
        else
        {
            $data[5] = $currentTime;
            $userdata->notifications['web3g'] = 0;
        }
        
        // X) Save check in db
        $sql = 'UPDATE  absg_users SET  last_activity =  "'.$currentTime.';'.$CI->layout->getModuleName().';'.$data[2].';'.$data[3].';'.$forumFinalData.';'.$data[5].'" WHERE  user_id =' . $userdata->user_id;
        $CI->db->query($sql);
        $userdata->notifications['forum'] = array('total' => $forumTotal, 'posts' => $forumData);
        $userdata->notifications['absg'] = $userdata->notifications['citations'] + $userdata->notifications['immt'] + $forumTotal;

        return $userdata;
    }
}



/**
 * Met à jour les infos d'activité d'un module en particulier pour l'utilisateur courant
 *
 * @param $user_id, l'idenfiant de l'utilisateur concerné
 * @param $moduleName, le nom du module dont il faut mettre les infos à jour
 * @return $newData, les infos à enregistrer
 */
if ( ! function_exists('updateUserActivity'))
{
    function updateUserActivity($user_id, $moduleName, $newData)
    {
        // getData
        $CI = get_instance();
        $data = $CI->db->query('SELECT last_activity FROM absg_users WHERE user_id='.$user_id)->result()[0]->last_activity;
        $data = explode(';', $data);
        
        $idx = 0;

        switch ($moduleName) 
        {
            case 'citation':
                $idx = 2;
                break;
            case 'immt':
                $idx = 3;
                break;
            case 'forum':
                $idx = 4;
                break;
            case 'web3g':
                $idx = 5;
                break;
            
            default:
                $idx = 0;
                break;
        }

        // Update last time
        $data[0] = time();
        
        // Si index ok
        if ($idx > 0)
        {
            $dataString = '';
            // reconstruction des données d'activité
            for($i=0; $i< count($data); $i++)
            {
                if ($i == $idx)
                {
                    $dataString .= $newData . ';';
                }
                else
                {
                    $dataString .= $data[$i] . ';';
                }
            }
            $dataString = substr($dataString, 0, -1);

            // sauvegarde en base
            $CI->db->query('UPDATE  absg_users SET  last_activity =  "'.$dataString.'" WHERE  user_id =' . $user_id);

            // maj donnée de session
            $userdata =& $CI->session->userdata('user');
            $userdata->last_activity = $dataString;
            $CI->session->set_userdata('user', $userdata);
        }
    }
}


/**
 * Récupère les informations des utilisateurs en ligne
 *  0-3  min : couleur
 *  3-5  min : translucide
 *  5-10 min : translucide et grisé
 *
 * @param $CI, le context (à récupérer avec get_instance() par exemple)
 * @param $userdata, les infos de base de l'utilisateur (voir méthode checkUserSession)
 * @return $userdata, les infos de l'utilisateur avec les infos de notification en plus
 */
if ( ! function_exists('checkUsersOnline'))
{
    function checkUsersOnline($CI, $user_id)
    {
        $result = $CI->db->query('SELECT user_id, username, last_activity FROM absg_users ORDER BY  last_activity DESC, username ASC')->result();
        $users = array(
            'count' => 0,
            'users' => array());
        $currentTime = time();

        foreach ($result as $user)
        {
            if ($user->user_id ==  $user_id) continue;

            $data = explode(';', $user->last_activity);
            $deltaSeconds = $currentTime - $data[0];

            // Si la dernière activité de l'utilisateur à plus de 10min, on n'en tient pas compte
            if ($deltaSeconds > 600) break;

            // Sinon on l'affiche comme utilisateur actuellement sur le site (3 plages : 0-3min, 3-5min et 5-10min)
            $userData = array(
                'delta' => ($deltaSeconds <= 60) ? "{$deltaSeconds}s" : round($deltaSeconds / 60, 0) . " min", 
                'avatar' => $CI->layout->asset_avatar_url($user->user_id),
                'username' => $user->username,
                'module' => $data[1],
                'class' => ($deltaSeconds >300) ? 'f5-10' : (($deltaSeconds >180) ? 'f3-5' : 'f0-3'));

            $users['count']++;
            $users['users'][] = $userData;
        }

        return $users;
    }
}






/**
 * Log l'activité pour l'utilisateur
 *
 * @param $user [array], les infos de base de l'utilisateur (voir méthode checkUserSession)
 * @param $module [string], le module concerné par le log :     'absg','citation','immt','forum','agpa','agenda','web3g','cultureg','gtheque','wikig','olympiages','grenier', 'birthday'
 * @param $msgType [string], le type du log :                   'message', 'warning', 'error'
 * @param $msg [string], le message du log
 * @param $url [string], si nécessaire une url qui rendra cliquable le log
 */
if ( ! function_exists('addLog'))
{
    function addLog($user, $module, $msgType, $msg, $url=null)
    {
        $CI = get_instance();
        $date = time();
        $result = $CI->db->query("INSERT INTO absg_logs (`user_id`, `date`, `type`, `module`, `message`, `url`) VALUES ({$user->user_id}, {$date}, {$msgType}, {$module}, {$msg}, {$url});");
    }
}


/**
 * Log la présence de l'utilisateur
 *
 * @param $user [array], les infos de base de l'utilisateur (voir méthode checkUserSession)
 * @return $presence [array], le tableau avec les informations de présence des membres sur le site durant la journée
 */
if ( ! function_exists('logPresence'))
{
    function logPresence($user)
    {
        $CI = get_instance();
        
        // 0- Init des variables de temps
        $currentDate = time();
        $current = date("G", $currentDate);
        $startDate = (date('G') < 4) ? mktime(0,0,0) - 72000 : mktime(4,0,0);  // Une journée commence à 4 heures du matin et se termine à 4h du matin suivant
        $timeStep = 3600;                                                       // la présence est mesuré à intervales réguliers d'une heure

        $presence = array();
        $defaultStyle = ' previous';
        for ($i=0; $i<24; $i++)
        {
            $h=($i+4)%24;
            $style = (($h < 8) || ($h > 21)) ? 'night' : 'day';

            if ($h == $current) 
            {
                $style .= ' current';
                $defaultStyle = ' next';
            }
            else
            {
                $style .= $defaultStyle;
            }
            $presence[($i+4)%24] = array(
                'style' => $style,
                'users' => array()
            );
        }

        // 1- on supprime les données trop anciennes
        $CI->db->query("DELETE FROM absg_daily_presence WHERE date<{$startDate}");

        // 2- On récupère l'historique des présences de la journée pour tout le monde
        $result = $CI->db->query('SELECT p.*, u.username FROM absg_daily_presence p, absg_users u WHERE p.user_id = u.user_id')->result();
        foreach ($result as $presenceToken)
        {
            $hour = date("G", $presenceToken->date);
            if (!isset($presence[$hour]['users'][$presenceToken->user_id]))
            {
                $presence[$hour]['users'][$presenceToken->user_id] = array(
                    'username' => $presenceToken->username,
                    'avatar' => $CI->layout->asset_avatar_url($presenceToken->user_id)
                );
            }
        }


        // 3- On ajoute la présence de l'utilisateur en cours si besoin
        if (!isset($presence[$current]['users'][$user->user_id]))
        {
            $CI->db->query("INSERT INTO absg_daily_presence (`user_id`, `date`) VALUES ({$user->user_id}, {$currentDate});");
            $presence[$current]['users'][$user->user_id] = array(
                'username' => $user->username,
                'avatar' => $CI->layout->asset_avatar_url($user->user_id)
            );
        }



        return $presence;
    }
}










/**
 * Donne les paliers pour le calcul de la note G
 *
 * @param $user [array], les infos de base de l'utilisateur (voir méthode checkUserSession)
 */
if ( ! function_exists('computeRank'))
{
    function getRanksSteps()
    {
        return array(
            'absg'      => array(),
            'citation'  => array(0, 1, 2, 4, 6, 8, 10, 10, 15, 20, 25, 30, 35, 40, 45, 50, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100, 100, 110, 120, 130, 140, 150, 150, 160, 170, 180, 190, 200, 220, 240, 260, 280, 300),
            'immt'      => array(0, 1, 2, 4, 6, 8, 10, 10, 15, 20, 25, 30, 35, 40, 45, 50, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100, 100, 110, 120, 130, 140, 150, 150, 160, 170, 180, 190, 200, 220, 240, 260, 280, 300),
            'forum'     => array(0, 2, 5, 10, 15, 20, 25, 30, 40, 50, 75, 100, 125, 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 850, 1000, 1000, 1200, 1400, 1600, 1800, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5000, 6000, 7000, 8000, 9000, 10000, 10000),
            'agpa'      => array(0, 1, 2, 3, 4, 5, 5, 6, 7, 8, 9, 10, 10, 11, 12, 13, 14, 15, 15, 16, 17, 18, 19, 20, 20, 21, 22, 23, 24, 25, 25, 26, 27, 28, 29, 30, 30, 31, 32, 33, 34, 35, 35, 36),
            'agenda'    => array(),
            'web3g'     => array(),
            'cultureg'  => array(),
            'gtheque'   => array(),
            'wikig'     => array(),
            'olympiages' => array(),
            'grenier'   => array()
        );
    }

    function findRankStep($module, $stepsLevels, $data)
    {
        $result = 0;
        for ($i=0; $i < count($stepsLevels[$module]); $i++) 
        { 
            if ($data[$module] >= $stepsLevels[$module][$i])
            {
                continue;
            }
            $result= $i-1;
            break;
        }
        return $result;
    }

    function computeRank($user, $module='all')
    {
        // 0- Init variables
        $CI = get_instance();
        $stepsLevels = getRanksSteps();
        
        // 1- On récupère la note actuel                                        (0=>rang spécial, 1=>citation, 2=>immt, 3=>forum, 4=>agpa)
        $currentLevels = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);

        // 2- On récupère l'ensemble des données nécessaire en base
        $data = array (
            'citation'  => $CI->db->query("SELECT count(*) as 'citations' FROM citations WHERE poster_id={$user->user_id}")->result()[0]->citations,
            'immt'      => $CI->db->query("SELECT count(*) as 'immt' FROM immt WHERE user_id={$user->user_id}")->result()[0]->immt,
            'forum'     => $CI->db->query("SELECT count(*) as 'forum' FROM forum_posts WHERE poster_id={$user->user_id}")->result()[0]->forum,
            'agpa'      => $CI->db->query("SELECT COUNT(DISTINCT(year)) as 'agpa' FROM agpa_photos WHERE user_id={$user->user_id}")->result()[0]->agpa,
        );

        // 3- Pour chaque module on retrouve le palier
        switch ($module) 
        {
            case 'all':
            case 'citation':
                $currentLevels[1] = findRankStep('citation', $stepsLevels, $data);
                if ($module != 'all') break;
            case 'immt':
                $currentLevels[2] = findRankStep('immt', $stepsLevels, $data);
                if ($module != 'all') break;
            case 'forum':
                $currentLevels[3] = findRankStep('forum', $stepsLevels, $data);
                if ($module != 'all') break;
            case 'agpa':
                $currentLevels[4] = findRankStep('agpa', $stepsLevels, $data);
                if ($module != 'all') break;
        }

        // 4- Test pour savoir si rang spécial
        $code = "";
        if ($currentLevels[0])
        {
            $result = $CI->db->query("SELECT * FROM absg_ranks WHERE code = {$currentLevels[0]} ")->result()[0];
            $code = $result->code;
        }
        else
        {
            $rankNote = 0;
            for ($i=1; $i < count($currentLevels); $i++) 
            { 
                $rankNote += $currentLevels[$i];
            }
            $result = $CI->db->query("SELECT * FROM absg_ranks WHERE g_note = {$rankNote} or g_note=".($rankNote-1))->result()[0];
            $code = $result->code;
        }

        // 5- On met à jour les infos dans la base de données
        $gnote = '';
        foreach ($currentLevels as $key => $value) 
        {
            $gnote .= $value . ';';
        }
        $gnote = substr($gnote, 0, -1);

        $CI->db->query("UPDATE  absg_users SET  noteg=?, rank=? WHERE  user_id=?;", array($gnote, $code, $user->user_id));
        return $code . "-" . $gnote;
    }
}



/**
 * Retourne les statistique concernant le rang de l'utilisateur
 *
 * @param $user [array], les infos de base de l'utilisateur (voir méthode checkUserSession)
 * @param $module [string], indique si il suffit de mettre à jour seulement un module. par défaut : 'all'
 */
if ( ! function_exists('getUserRankStats'))
{
    function getUserRankStats($user, $module='all')
    {
        $CI = get_instance();
        $result = $CI->db->query("SELECT * FROM absg_ranks")->result();
        $data  = $CI->db->query("SELECT noteg, rank FROM absg_users WHERE user_id={$user->user_id}")->result()[0];

        // Récupérations des rangs
        $ranks = array();
        foreach ($result as $row) 
        {
            $ranks[$row->code] = $row;
        }

        // Calcul note G de l'utilisateur
        $noteG = 0;
        $currentLevels = ($user->noteg!='') ? explode(';', $user->noteg) : array(false, 0, 0, 0, 0);
        for ($i=1; $i < count($currentLevels) ; $i++) 
        { 
            $noteG += $currentLevels[$i];
        }

        $stats = array
        (
            'number' => $data->rank,
            'name' => $ranks[$data->rank]->title,
            'noteg' => $noteG ,
            'boundMin' => 0,
            'boundMax' => 90,
            'progression' => 'width:' . round(min($noteG, 90)/90*100,0).'px;',
            'progressionValue' =>  round(min($noteG, 90)/90*100,0),
            'nextAward' => ($noteG%2 == 0) ? '+2 G' : '+1 G',
            'src' => base_url() . '/assets/img/rangs/r'.$data->rank.'.png'
        );
        if ($noteG >= 90) $stats['nextAward'] = '-';

        return $stats;
    }
}
