<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * A partir des infos du sujet et de l'utilisateur, détermine l'icone à afficher pour le sujet
 *
 * @param $topic, les informations sur le sujet de discussion
 * @param $forumData, les informations sur les sujets non lu du forum
 *
 */
if ( ! function_exists('topicIcon'))
{
    function topicIcon($topic, $forumData)
    {
        $CI = get_instance();
        $activity = "normal";

        if (isset($forumData[$topic->topic_id]))
        {
            $activity = "new";
        }
        

        if ($topic->last_post_time < time() - 7776000) // 7776000 = 3*30 jours
		{
			$activity = "archived";
		}

        return "$topic->type $activity";
    }
}





