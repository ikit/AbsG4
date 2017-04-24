<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Layout
{
    private $CI;
    private $var = array();
    private $theme = 'default';
    private $isInit = false;
    private $loadedPlugins = array();

    
    private $monthShort = array('Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec');
    private $monthLong = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
     
/*
|===============================================================================
| Constructeur
|===============================================================================
*/
     
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('file');

        $this->var['output'] = '';
        $this->var['footerContent'] = '';
        $this->var['css'] = array();
        $this->var['js'] = array();

        // Le titre 
        $this->var['title'] = "Absolument G";
        // ucfirst($this->CI->router->fetch_method()) . ' - ' . ucfirst($this->CI->router->fetch_class());

        // Nous initialisons la variable $charset avec la même valeur que la clé de configuration initialisée dans le fichier config.php
        $this->var['charset'] = $this->CI->config->item('charset');

        // On initialise les variables principales du theme
        $this->var['citation'] = $this->asset_citation_data();
        $this->var['immt'] = $this->asset_immt_data();

        $this->var['base_url'] = base_url();
        $this->var['asset_base_src'] = base_url() . 'assets/';
        $this->var['theme_base_src'] = base_url() . 'assets/theme/' . $this->theme . '/';
    }


    public function initEmpty($moduleName='', $login=false)
    {
        // Initialisation du timezone par défaut :
        date_default_timezone_set ( 'Europe/Paris' );


        if (!$this->isInit)
        {
            $user = null;
            $this->var['selected_module'] = $moduleName;

            if (!$login)
            {
                $user = checkUserSession(get_instance());
            }

            if ($user != null)
            {
                $this->var['user'] = $user;
                $this->var['avatar_url'] = $this->asset_avatar_url($user->user_id);
                $this->var['users_online'] = checkUsersOnline(get_instance(), $user->user_id);
            }

            $this->isInit = true;

            return $user;
        }
        return $this->var['user'];
    }



    public function init($moduleName='', $login=false)
    {
        // Initialisation du timezone par défaut :
        date_default_timezone_set ( 'Europe/Paris' );




        if (!$this->isInit)
        {
            $user = null;
            $this->var['selected_module'] = $moduleName;

            if (!$login)
            {
                $user = checkUserSession(get_instance());
            }
            
            // Test si on charge le theme special pour halloween)
            // if ($user != null && $user->user_id == 2)
            // {
				// $this->setTheme("halloween");
            // }

            $this->addCss('global');
            $this->addJs('jquery');
            $this->addJs('global');


            if ($user != null)
            {
                $this->var['user'] = $user;
                $this->var['avatar_url'] = $this->asset_avatar_url($user->user_id);
                $this->var['users_online'] = checkUsersOnline(get_instance(), $user->user_id);
            }
            $this->isInit = true;

            return $user;
        }
        return $this->var['user'];
    }

    public function getModuleName()
    {
        return (isset($this->var['selected_module']) && !empty($this->var['selected_module'])) ? $this->var['selected_module'] : 'absg';
    }
     
/*
|===============================================================================
| Méthodes pour charger les vues
|   . view
|   . views
|===============================================================================
*/
    public function view($name, $data = array())
    {
        if (!$this->isInit) $this->init();

        $this->var['output'] .= $this->CI->load->view($name, $data, true);
        $this->addCss('$name');
        $this->CI->load->view('../views/themes/' . $this->theme, $this->var);
        return $this;
    }
    public function views($name, $data = array())
    {
        if (!$this->isInit) $this->init();
        
        $this->var['output'] .= $this->CI->load->view($name, $data, true);
        $this->addCss('$name');
        return $this;
    }
    public function footer($name, $data = array())
    {
        if (!$this->isInit) $this->init();

        $this->var['footerContent'] .= $this->CI->load->view($name, $data, true);
        $this->addCss('$name');
        return $this;
    }
/*
|===============================================================================
| Méthodes concernant le cache
|   
|===============================================================================
*/
    public function deleteCache($uri_string)
    {
        $path = $this->CI->config->item('cache_path');
        $cache_path = ($path == '') ? APPPATH .'cache/' : $path;

        $uri =  $this->CI->config->item('base_url') . $this->CI->config->item('index_page') . $uri_string;
        $cache_path .= md5($uri);

        if (file_exists($cache_path))
        {
            return unlink($cache_path);
        }
        else
        {
            return TRUE;
        }
    }


    public function createCache($uri_string, $snippet)
    {
        $path = $this->CI->config->item('cache_path');
        $cache_path = ($path == '') ? APPPATH .'cache/' : $path;

        $uri =  $this->CI->config->item('base_url') . $this->CI->config->item('index_page') . $uri_string;
        $cache_path .= md5($uri);

        if (file_exists($cache_path))
        {
            unlink($cache_path);
        }
        
        if ( ! write_file($cache_path, $snippet))
        {
             return 'ERROR : Unable to write the cache file';
        }

        return true;
    }


    public function getCache($uri_string)
    {
        $path = $this->CI->config->item('cache_path');
        $cache_path = ($path == '') ? APPPATH .'cache/' : $path;

        $uri =  $this->CI->config->item('base_url') . $this->CI->config->item('index_page') . $uri_string;
        $cache_path .= md5($uri);

        if (file_exists($cache_path))
        {
            return read_file($cache_path);
        }
        else
        {
            return "Error : Cache file (" . $cache_path . ") not found";
        }
    }


/*
|===============================================================================
| Méthodes pour ajouter des feuilles de CSS et de JavaScript
|   . addCss
|   . addJs
|===============================================================================
*/
    public function addCss($filename)
    {
        if( is_string($filename) 
            AND !empty($filename) 
            AND file_exists('./assets/theme/' . $this->theme . '/css/' . $filename . '.css')
            AND !in_array($this->css_url($filename), $this->var['css']))
        {
            $this->var['css'][] = $this->css_url($filename);
            return true;
        }
        return false;
    }
     
    public function addJs($filename)
    {
        if( is_string($filename)
            AND !empty($filename)
            AND file_exists('./assets/theme/' . $this->theme . '/js/' . $filename . '.js')
            AND !in_array($this->js_url($filename), $this->var['js']))
        {
            $this->var['js'][] = $this->js_url($filename);
            return true;
        }
        return false;
    }

    public function addPlugin($pluginName)
    {
        if ($pluginName === "jqPlot" && !in_array('jqPlot', $this->loadedPlugins))
        {
            $this->var['css'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jqplot/jquery.jqplot.css';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jqplot/jquery.jqplot.js';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jqplot/plugins/jqplot.pieRenderer.min.js';
            $this->loadedPlugins[] = "jqPlot";
            return true;
        }
        elseif ($pluginName === "tinymce" && !in_array('tinymce', $this->loadedPlugins))
        {
            $this->var['css'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/tinymce/themes/modern/theme.min.js';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/tinymce/tinymce.min.js';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/tinymce/jquery.tinymce.min.js';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/tinymce/tableplugin.js';
            $this->loadedPlugins[] = "tinymce";
            return true;
        }
        elseif ($pluginName === "jquery-ui" && !in_array('jquery-ui', $this->loadedPlugins))
        {
            $this->var['css'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jquery-ui/css/vader/jquery-ui-1.10.3.custom.min.css';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jquery-ui/js/jquery-ui.min.js';
            $this->loadedPlugins[] = "jquery-ui";
            return true;
        }
        elseif ($pluginName === "lightbox" && !in_array('lightbox', $this->loadedPlugins))
        {
            $this->var['css'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/lightbox/css/lightbox.css';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/lightbox/js/lightbox-2.6.min.js';
            $this->loadedPlugins[] = "lightbox";
            return true;
        }
        elseif ($pluginName === "jquery-ias" && !in_array('jquery-ias', $this->loadedPlugins))
        {
            $this->var['css'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jquery-ias/css/style.css';
            $this->var['js'][] =  base_url() . 'assets/theme/' . $this->theme . '/plugin/jquery-ias/js/jquery-ias.min.js';
            $this->loadedPlugins[] = "jquery-ias";
            return true;
        }

        return false;
    }



/*
|===============================================================================
| Méthodes pour modifier les variables envoyées au layout
|   . setTitle
|   . setTheme
|===============================================================================
*/
    public function setTitle($title)
    {
        if(is_string($title) AND !empty($title))
        {
            $this->var['title'] = $title;
            return true;
        }
        return false;
    }

    public function setTheme($theme)
    {
        if( is_string($theme) 
            AND !empty($theme) 
            AND file_exists('absg/views/themes/' . $theme . '.php'))
        {
            $this->theme = $theme;
            return true;
        }
        return false;
    }

    public function setSelectedModule($moduleName)
    {
        if(is_string($moduleName) AND !empty($moduleName) )
        {
            $this->var['selected_module'] = $moduleName;
            return true;
        }
        return false;
    }

/*
|===============================================================================
| Helper to build url for assets
|   . css_url
|   . js_url
|   . img_url
|===============================================================================
*/
    function css_url($filename)
    {
        return base_url() . 'assets/theme/' . $this->theme . '/css/' . $filename . '.css';
    }
    function js_url($filename)
    {
        return base_url() . 'assets/theme/' . $this->theme . '/js/' . $filename . '.js';
    }
    function asset_img($filename)
    {

        return base_url() . 'assets/theme/' . $this->theme . '/img/' .  $filename;
    }
    function asset_avatar_url($userId, $forceEvmt=null)
    {
		// Si l'événement n'est pas forcé, on le détermine en fonction de la date
		if ($forceEvmt === null)
		{
			$m = date("m");
			$d = date("d");
			if ($m == 10 && $d >= 25)
			{
				$forceEvmt='-H';
			}
			else
			{
				$forceEvmt='';
			}
		}
        return base_url() . 'assets/img/avatars/' . str_pad($userId, 3, "0", STR_PAD_LEFT) . $forceEvmt . '.png';
    }
    function asset_rank_mini($rankCode)
    {
        return base_url() . '/assets/img/rangs/full/r' . $rankCode . '-mini.jpg';
    }
    function asset_rank_maxi($rankCode)
    {
        return base_url() . '/assets/img/rangs/full/r' . $rankCode . '-maxi.jpg';
    }
    function asset_citation_data()
    {
        $IC = get_instance();

        // Récupérer une citation alétoirement
        // $sql = 'SELECT c.`citation` , p.`firstname` , p.`surname` FROM  `citations` c,  `agenda_people` p WHERE c.`author_id` = p.people_id';
        $sql = 'SELECT c.`citation` , p.`firstname` , p.`surname` FROM  `citations` c,  `agenda_people` p WHERE c.`author_id` = p.people_id AND c.citation_id >= RAND() * (SELECT MAX(citation_id) FROM citations) LIMIT 1';
        $citation = $IC->db->query($sql)->result()[0];


        $data = array();
        $data['author'] = (!empty($citation->surname)) ? $citation->surname : $citation->firstname ;
        $data['citation'] = $citation->citation;
        return $data;
    }
    function asset_immt_data()
    {
        // get the date about the immt
        //$date = 
        $data = array();
        $data['max_url'] = base_url() . 'assets/img/immt/z00001.png';
        $data['min_url'] = base_url() . 'assets/img/immt/z00001.png';
        $data['title'] = 'Mon rideau !';
        return $data;
    }
    function displayed_date($datetime, $format='normal')
    {

        $result = '';
        // 1) récupérer les infos brutes
        $y = date("Y", $datetime);
        $m = date("n", $datetime);
        $d = date("j", $datetime);
        $h = date("G", $datetime);
        $n = date("i", $datetime);

        // 3) Formater la date
        switch ($format) 
        {
            case 'date':
                $m -=1;
                $result = "{$d} {$this->monthShort[$m]} {$y}";
                break;
            case 'quick':
                $m -=1;
                $result = "{$d} {$this->monthShort[$m]} - {$h}h{$n}";
                break;
            case 'short':
                $result = "{$d} / {$m} / {$y} à {$h}h{$n}";
                break;
            case 'shortdate':
                $result = "{$d} / {$m} / {$y}";
                break;

            case 'normal':
            default:
                $m -=1;
                $result = "{$d} {$this->monthShort[$m]} {$y} à {$h}h{$n}";
                break;
        }

//echo "\n----------\n$result";
        return $result;
    }
}
 

    

/* End of file layout.php */
/* Location: ./application/libraries/layout.php */