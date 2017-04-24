<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Ajoute un "evenement" / log sur le site 
 * @param $user_id : 
 * @param $timeStamp :
 * @param $type : 
 * @param $module : 
 * @param $message :
 * @param $url :
 *
 */
if ( ! function_exists('logMessage'))
{
    function logMessage($user_id, $timeStamp, $type, $module, $message, $url)
    {
        $CI = get_instance();
        $CI->db->query("INSERT INTO absg_logs (`user_id`, `date`, `type`, `module`, `message`, `url`) VALUES (?, ?, ?, ?, ?, ?);", array($user_id, $timeStamp, $type, $module, $message, $url));

        // Get data from database
        $sql = 'SELECT l.*, u.username FROM absg_logs l, absg_users u WHERE l.user_id=u.user_id AND l.type="message" ORDER BY date DESC LIMIT 10';
        $result = $CI->db->query($sql)->result();
        $activities = array();
        foreach ($result as $row)
        {
            $row->module_icon_url = base_url() . 'assets/theme/default/img/log-' . $row->module . '.png';
            $row->module_icon_title = moduleToTitle($row->module);
            $row->date = $CI->layout->displayed_date($row->date, 'quick');
            $row->avatar_url = $CI->layout->asset_avatar_url($row->user_id);
            $activities[] = $row;
        }

        // Build html snippet
        $i = 0;
        $snippet_cache = "";
        foreach ($activities as $data)
        {
            $snippet_cache .= "<a class=\"activity";
            if ($i == 0) 
            { 
                $snippet_cache .= ' first'; 
            }
            elseif ($i==count($activities)-1) 
            {
                $snippet_cache .= ' last';
            }
            $snippet_cache .= "\"";
            if ($data->url !== null) 
            {
                $snippet_cache .= ' href="'.base_url().$data->url.'"';
            }

            $snippet_cache .= "><img src=\"{$data->module_icon_url}\" title=\"{$data->module_icon_title}\"/>";
            $snippet_cache .= "<img src=\"{$data->avatar_url}\" title=\"{$data->username}\"/>";
            $snippet_cache .= "<span class=\"date\">{$data->date}</span>";
            $snippet_cache .= "<span class=\"message\">{$data->message}</span></a>";
            $i++;
        }

        // Save cache
        $CI->layout->createCache("lastactivities_welcom_snippet", $snippet_cache);
    }
}


/**
 * 
 *
 * @param $moduleName : le nom du module
 *
 */
if ( ! function_exists('moduleToTitle'))
{
    function moduleToTitle($moduleName)
    {
        $result = '';

        switch ($moduleName) 
        {
            case 'absg' :
                $result = 'Absolument G';
                break;
            case 'citation' :
                $result = 'Citations';
                break;
            case 'immt' :
                $result = 'Image du moment';
                break;
            case 'forum' :
                $result = 'Forum';
                break;
            case 'agpa' :
                $result = 'A.G.P.A.';
                break;
            case 'agenda' :
                $result = 'Agenda';
                break;
            case 'web3g' :
                $result = 'Web des G';
                break;
            case 'cultureg' :
                $result = 'Culture G';
                break;
            case 'gtheque' :
                $result = 'G-thèque';
                break;
            case 'wikig' :
                $result = 'Wiki';
                break;
            case 'olympiages' :
                $result = 'OlympiaGes';
                break;
            case 'grenier' :
                $result = 'Grenier';
                break;
            default:
                $result = '?';
                break;
        }

        return $result;
    }
}

