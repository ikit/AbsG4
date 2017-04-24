<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zaffa extends CI_Controller 
{


    private function init()
    {
	// Init layout
	$user = $this->layout->init("zaffa");
	$this->layout->addCss("zaffa");
	$this->layout->addJs("zaffa");
	
	// Si pas admin : on ne fait rien
	if ($user->auth != '*') 
	{
		show_404();
	}
	
	return $user;
    }


    /**
     * Index Page for this controller.
     *
     */
    public function index()
    {
	$user = $this->init();
	$this->layout->view('zaffa/index', null);
 
 // Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}
    }


  public function mktime($date="0-0-0-1-1-1")
  {
    // Init layout
		$user = $this->layout->init("zaffa");

		// Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}
   
   
   echo "format input date : Hr-min-Sec-Day-Month-Year<br/>";
   echo "Your input : " . $date."<br/>";
   
   $data = explode('-', $date);
   $time = mktime($data[0], $data[1], $data[2], $data[4], $data[3], $data[5]);
   echo "Result = " . $time . ' <=> ' . date('d/m/Y H:i:s', $time) ;
  }



  /**
   * Affiche la configuration php du site
   */
  public function phpinfo()
  {
    $user = $this->init();
    $this->layout->view('zaffa/phpinfo', null);
  }
	
	

	/**
	 * Methode pour (re)initialiser les meta donnees des membres : noteG, rangs, notifications.
	 */
	public function resetUsersData()
	{
		// Init layout
		$user = $this->layout->init("zaffa");

		// Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}


		// 1) On recupere la liste des utilisateurs
		$sql = "SELECT * FROM absg_users";
		$users = $this->db->query($sql)->result();

		echo "Recuperation des users :<br/><ul>";
		foreach ($users as $user) 
		{
			$newNoteG = computeRank($user, 'all');
			echo "<li>" . $user->username . " : maj notg : " . $newNoteG . "<br/>";

			$data = explode('-', $newNoteG);
			$rank = $data[0];
			$dg = explode(';', $data[1]);
			$g = $dg[1] + $dg[2] + $dg[3] + $dg[4];
			echo "G=" . $g . " Rank=" . $rank . "<br/></li>";
		}
		echo "</ul>";

	}


	
  public function execBashCommand($commandID)
  {
    // Init layout
    $user = $this->init();
    
    // Si pas admin : on ne fait rien
    if ($user->auth != '*') 
    {
      show_404();
    }
    
    $result = "no command found"; 
    switch($commandID)
    {
      case 'resetAgpaPhotoOwner':
        $result = shell_exec("chmod -R a+w assets/img/agpa");
        break;
      case 'test':
        $result = shell_exec("pwd");
        break;
    }
    
    print_r($result);
  }  
 

	
	/**
	 * Réinitialise le password de l'utilisateur avec 'toto'
	 */
	public function resetPassword($userId)
	{
		// Init layout
		$user = $this->layout->init("zaffa");

		// Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}
		
		// Save data in database
		$this->db->query("UPDATE absg_users SET password='0b9c2625dc21ef05f6ad4ddf47c5f203837aa32c' WHERE user_id=".$userId);

	}




	/**
	 * Méthode pour (ré)initialiser les méta données des membres : noteG, rangs, notifications.
	 */
	public function newActivity()
	{
		// Init layout
		$user = $this->layout->init("zaffa");

		// Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}


		$this->layout->view('zaffa/newActivity',null);
	}


	public function addNewActivity()
	{
		// Init layout
		$user = $this->layout->init("zaffa");

		// Si pas admin : on ne fait rien
		if ($user->auth != '*') 
		{
			show_404();
		}

		// Save data in database
		$user_id = $this->input->post('userId');
		$date = explode(',', $this->input->post('date')); // date format : aaaa,mm,jj,hh,min
		$timeStamp = mktime($date[3],$date[4],0,$date[1],$date[2],$date[0]);
		$type = $this->input->post('type');
		$module = $this->input->post('module');
		$message = $this->input->post('message');
		$url = $this->input->post('url');
		
		logMessage($user_id, $timeStamp, $type, $module, $message, $url);

		// redirect to welcom
		redirect('', 'refresh');
	}






	


	
	
	
	
	
	
	
	
	
	

	/*
-- Migration phpBB3 (absolumentg.free.fr) > AbsG v4
-- Mise à jours des id des forum .
-- Idem pour table forum_posts

UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =10;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =11;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =14;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =15;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =16;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =19;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =21;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =26;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =27;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =31;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =32;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =33;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =34;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =35;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =38;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =39;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =40;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =43;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =44;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =46;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =47;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =49;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =50;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =53;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =55;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =56;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =60;
UPDATE  forum_topics SET  forum_id =  '1' WHERE  forum_id =61;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =3;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =22;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =23;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =28;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =41;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =51;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =57;
UPDATE  forum_topics SET  forum_id =  '2' WHERE  forum_id =62;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =4;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =13;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =18;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =30;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =42;
UPDATE  forum_topics SET  forum_id =  '3' WHERE  forum_id =52;
UPDATE  forum_topics SET  forum_id =  '4' WHERE  forum_id =8;
UPDATE  forum_topics SET  forum_id =  '5' WHERE  forum_id =54;
UPDATE  forum_topics SET  forum_id =  '6' WHERE  forum_id =24;
UPDATE  forum_topics SET  forum_id =  '6' WHERE  forum_id =25;
UPDATE  forum_topics SET  forum_id =  '6' WHERE  forum_id =48;
UPDATE  forum_topics SET  forum_id =  '7' WHERE  forum_id =59;
UPDATE  forum_topics SET  forum_id =  '8' WHERE  forum_id =64;


-- Migration phpBB3 (absolumentg.free.fr) > AbsG v4
-- Mise à jours des id des membres .

UPDATE  forum_topics SET  last_poster_id =  '20' WHERE  last_poster_id =103;
UPDATE  forum_topics SET  last_poster_id =  '21' WHERE  last_poster_id =104;
UPDATE  forum_topics SET  last_poster_id =  '22' WHERE  last_poster_id =105;
UPDATE  forum_topics SET  last_poster_id =  '23' WHERE  last_poster_id =106;
UPDATE  forum_topics SET  last_poster_id =  '24' WHERE  last_poster_id =124;
UPDATE  forum_topics SET  last_poster_id =  '25' WHERE  last_poster_id =125;
UPDATE  forum_topics SET  last_poster_id =  '26' WHERE  last_poster_id =122;
UPDATE  forum_topics SET  last_poster_id =  '27' WHERE  last_poster_id =117;
UPDATE  forum_topics SET  last_poster_id =  '28' WHERE  last_poster_id =121;
UPDATE  forum_topics SET  last_poster_id =  '29' WHERE  last_poster_id =119;
UPDATE  forum_topics SET  last_poster_id =  '30' WHERE  last_poster_id =123;
UPDATE  forum_topics SET  last_poster_id =  '31' WHERE  last_poster_id =120;
UPDATE  forum_topics SET  last_poster_id =  '32' WHERE  last_poster_id =118;
UPDATE  forum_topics SET  last_poster_id =  '1' WHERE  last_poster_id =108;

UPDATE  forum_topics SET  first_poster_id =  '20' WHERE  first_poster_id =103;
UPDATE  forum_topics SET  first_poster_id =  '21' WHERE  first_poster_id =104;
UPDATE  forum_topics SET  first_poster_id =  '22' WHERE  first_poster_id =105;
UPDATE  forum_topics SET  first_poster_id =  '23' WHERE  first_poster_id =106;
UPDATE  forum_topics SET  first_poster_id =  '24' WHERE  first_poster_id =124;
UPDATE  forum_topics SET  first_poster_id =  '25' WHERE  first_poster_id =125;
UPDATE  forum_topics SET  first_poster_id =  '26' WHERE  first_poster_id =122;
UPDATE  forum_topics SET  first_poster_id =  '27' WHERE  first_poster_id =117;
UPDATE  forum_topics SET  first_poster_id =  '28' WHERE  first_poster_id =121;
UPDATE  forum_topics SET  first_poster_id =  '29' WHERE  first_poster_id =119;
UPDATE  forum_topics SET  first_poster_id =  '30' WHERE  first_poster_id =123;
UPDATE  forum_topics SET  first_poster_id =  '31' WHERE  first_poster_id =120;
UPDATE  forum_topics SET  first_poster_id =  '32' WHERE  first_poster_id =118;
UPDATE  forum_topics SET  first_poster_id =  '1' WHERE  first_poster_id =108;

UPDATE  forum_posts SET  poster_id =  '20' WHERE  poster_id =103;
UPDATE  forum_posts SET  poster_id =  '21' WHERE  poster_id =104;
UPDATE  forum_posts SET  poster_id =  '22' WHERE  poster_id =105;
UPDATE  forum_posts SET  poster_id =  '23' WHERE  poster_id =106;
UPDATE  forum_posts SET  poster_id =  '24' WHERE  poster_id =124;
UPDATE  forum_posts SET  poster_id =  '25' WHERE  poster_id =125;
UPDATE  forum_posts SET  poster_id =  '26' WHERE  poster_id =122;
UPDATE  forum_posts SET  poster_id =  '27' WHERE  poster_id =117;
UPDATE  forum_posts SET  poster_id =  '28' WHERE  poster_id =121;
UPDATE  forum_posts SET  poster_id =  '29' WHERE  poster_id =119;
UPDATE  forum_posts SET  poster_id =  '30' WHERE  poster_id =123;
UPDATE  forum_posts SET  poster_id =  '31' WHERE  poster_id =120;
UPDATE  forum_posts SET  poster_id =  '32' WHERE  poster_id =118;
UPDATE  forum_posts SET  poster_id =  '1' WHERE  poster_id =108;


UPDATE  immt SET  user_id =  '20' WHERE  user_id =103;
UPDATE  immt SET  user_id =  '21' WHERE  user_id =104;
UPDATE  immt SET  user_id =  '22' WHERE  user_id =105;
UPDATE  immt SET  user_id =  '23' WHERE  user_id =106;
UPDATE  immt SET  user_id =  '24' WHERE  user_id =124;
UPDATE  immt SET  user_id =  '25' WHERE  user_id =125;
UPDATE  immt SET  user_id =  '26' WHERE  user_id =122;
UPDATE  immt SET  user_id =  '27' WHERE  user_id =117;
UPDATE  immt SET  user_id =  '28' WHERE  user_id =121;
UPDATE  immt SET  user_id =  '29' WHERE  user_id =119;
UPDATE  immt SET  user_id =  '30' WHERE  user_id =123;
UPDATE  immt SET  user_id =  '31' WHERE  user_id =120;
UPDATE  immt SET  user_id =  '32' WHERE  user_id =118;
UPDATE  immt SET  user_id =  '1' WHERE  user_id =108;

*/


	/**
 	 * Methode pour traiter les posts importés depuis phpBB3 (absolumentg.free.fr) vers absolument 4G.
 	 * Convertit la majorités des BBCodes, images, styles.
 	 * Les attachments ne sont pas gérés.
	 */
	public function phpBBPostMigration($startElement, $nbElement, $do=false)
	{
		// Init layout
		$user = $this->layout->init("zaffa");

		// Si pas Olivier : on ne fait rien
		if ($user->user_id != 2) 
		{
			show_404();
		}

		$sql = "SELECT * FROM  `forum_posts` LIMIT {$startElement}, {$nbElement}";
		//$sql = "SELECT * FROM  `forum_posts` WHERE post_id=25187";
		$posts = $this->db->query($sql)->result();


		foreach ($posts as $data) 
		{
			// Pour chaque post on regarde si il y a des choses qu'on ne sait pas afficher

			echo "\n------------\n{$data->post_id}";
			// 1) Passe 1 : les bbcode simples
			$ret = preg_replace('#\[b:\w+\](.+)\[\/b:\w+\]#iUs', '<span style="font-weight: bold;">$1</span>', $data->text);
			$ret = preg_replace('#\[u:\w+\](.+)\[\/u:\w+\]#iUs', '<span style="text-decoration: underline;">$1</span>', $ret);
			$ret = preg_replace('#\[i:\w+\](.+)\[\/i:\w+\]#iUs', '<span style="font-style: italic;">$1</span>', $ret);
			$ret = preg_replace('#\[strike:\w+\](.+)\[\/strike:\w+\]#iUs', '<span style="text-decoration: line-through;">$1</span>', $ret);
			$ret = preg_replace('#\[sup:\w+\](.+)\[\/sup:\w+\]#iUs', '<span class="bb-sup">$1</span>', $ret);
			$ret = preg_replace('#\[sub:\w+\](.+)\[\/sub:\w+\]#iUs', '<span class="bb-sub">$1</span>', $ret);

			$ret = preg_replace('#\[left:\w+\](.+)\[\/left:\w+\]#iUs', '<p style="text-align:left;">$1</p>', $ret);
			$ret = preg_replace('#\[right:\w+\](.+)\[\/right:\w+\]#iUs', '<p style="text-align:right;">$1</p>', $ret);
			$ret = preg_replace('#\[center:\w+\](.+)\[\/center:\w+\]#iUs', '<p style="text-align:center;">$1</p>', $ret);
			$ret = preg_replace('#\[justify:\w+\](.+)\[\/justify:\w+\]#iUs', '<p style="text-align:justify;">$1</p>', $ret);

			$ret = preg_replace('#\[blockquote:\w+\](.+)\[\/blockquote:\w+\]#iUs', '<p style="margin-left: 20px;">$1</p>', $ret);
			$ret = preg_replace('#\[pre:\w+\](.+)\[\/pre:\w+\]#iUs', '<p style="white-space: pre;">$1</p>', $ret);
			$ret = preg_replace('#\[quote:\w+\](.+)\[\/quote:\w+\]#iUs', '<p class="bb-quote">$1</p>', $ret);
			$ret = preg_replace('#\[quote\=(.+)\:\w+\](.+)\[\/quote:\w+\]#iUs', '<p class="bb-quote"><p class="bb-quote-by">$1 :</p>$2</p>', $ret);
			$ret = preg_replace('#\[code:\w+\](.+)\[\/code:\w+\]#iUs', '<p class="bb-code">$1</p>', $ret);

			$ret = preg_replace('#\[url:\w+\](.+)\[\/url:\w+\]#iUs', '<a href="$1" title="Ouvrir dans un nouvel onglet">$1</a>', $ret); 
			$ret = preg_replace('#\[url\=(.+):\w+\](.+)\[\/url:\w+\]#iUs', '<a href="$1" title="Ouvrir dans un nouvel onglet">$2</a>', $ret);
			$ret = preg_replace('#\[img:\w+\](.+)\[\/img:\w+\]#iUs', '<img src="$1" alt="Image" />', $ret); 
			$ret = preg_replace('#\[smiley:\w+\].*/smilies/(.+)\[\/smiley:\w+\]#iUs', '<img src="{SMILIES_PATH}/$1" alt="smiley" />', $ret); 
			$ret = preg_replace('#\[vignette\=(.+):\w+\](.+)\[\/vignette:\w+\]#iUs', '<a href="$2" title="Télécharger l\'originale" target="_blank" style="text-decoration: none"><img src="$2" alt="Image" width="$1px" class="bb-thumb" /></a>', $ret); 
			$ret = preg_replace('#\[lightbox\=(.+):\w+\](.+)\[\/lightbox:\w+\]#iUs', '<a href="$1" data-lightbox="lightbox[imageForum]" title="zoom">$2</a>', $ret);

			$ret = preg_replace('#\[size\=(.+):\w+\](.+)\[\/size:\w+\]#iUs', '<span style="font-size:$1%;">$2</span>', $ret);
			$ret = preg_replace('#\[color\=(.+):\w+\](.+)\[\/color:\w+\]#iUs', '<span style="color:$1">$2</span>', $ret); 

			$ret = preg_replace('#\[table:\w+\](.+)\[\/table:\w+\]#iUs', '<table class="bb-table">$2</table>', $ret);
			$ret = preg_replace('#\[table\=(.+):\w+\](.+)\[\/table:\w+\]#iUs', '<table class="bb-table" style="$1">$2</table>', $ret);
			$ret = preg_replace('#\[tr:\w+\](.+)\[\/tr:\w+\]#iUs', '<tr>$1</tr>', $ret);
			$ret = preg_replace('#\[td:\w+\](.*)\[\/td:\w+\]#iUs', '<td valign="top">$1</td>', $ret);
			$ret = preg_replace('#\[td\=(.+):\w+\](.*)\[\/td:\w+\]#iUs', '<td style="$1" valign="top">$2</td>', $ret);

			$ret = preg_replace('#\[list:\w+\](.+)\[\/list:u:\w+\]#iUs', '<ul>$1</ul>', $ret);
			$ret = preg_replace('#\[list\=(.+):\w+\](.+)\[\/list:\w+\]#iUs', '<ol style="list-style-type: decimal;">$1</ol>', $ret);
			$ret = preg_replace('#\[\*:\w+\](.+)\[/\*:m:\w+\]#iUs', '<li>$1</li>', $ret);


			// La balise Video
			// Koreus
			$ret = preg_replace('#\[video:\w+\].*koreus.*video\/(.+)\.html\[\/video:\w+\]#iUs', '<object width="640" height="384"><param value="http://www.koreus.com/video/$1" name="movie"/><param value="transparent" name="wmode"/><embed width="640" height="384" wmode="transparent" type="application/x-shockwave-flash" src="http://www.koreus.com/video/$1"/></object>', $ret);
			// Youtube
			$ret = preg_replace('#\[video:\w+\].*youtube.*v=([^&]*)\[\/video:\w+\]#iUs', '<object width="640" height="384"><param name="movie" value="http://www.youtube.com/v/$1"></param><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" width="640" height="384"></embed></object>', $ret);
			
			// Dailymotion
			$ret = preg_replace('#\[video:\w+\].*dailymotion\.com\/video\/(.*?)\[\/video:\w+\]#iUs', '<object width="480" height="293"><param name="movie" value="http://www.dailymotion.com/swf/$1"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param><embed src="http://www.dailymotion.com/swf/$1" type="application/x-shockwave-flash" width="480" height="293" allowFullScreen="true" allowScriptAccess="always"></embed></object>', $ret);



			// les cas à traiter à la mano : attachments, ...




			$sql = "UPDATE `absolumentg`.`forum_posts` SET `text` =" . $this->db->escape($ret) . " WHERE `forum_posts`.`post_id` = {$data->post_id};";
			$this->db->query($sql);
			echo "\n-> done";
		}
	}





}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */