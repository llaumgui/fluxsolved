##
##
##        Mod title:  Flux solved
##
##      Mod version:  1.0
##  Works on FluxBB:  1.4
##     Release date:  2010-11-13
##      Review date:  2010-xx-xx
##           Author:  Guillaume Kulakowski (guillaume [AT] llaumgui [DOT] com)
##
##      Description:  This mod allow to mark a topic like solved
##
##   Repository URL:  http://projects.llaumgui.com/index.php/p/fluxsolved
##
##   Affected files:  viewtopic.php
##
##       Affects DB:  No
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at
##                    your own risk. Backup your forum database and any and
##                    all applicable files before proceeding.
##
## Know limitation:  Not compatible with a forum multi-language


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

files/solved.php to /solved.php

files/lang/English/solved.php to lang/English/solved.php

files/lang/French/solved.php to lang/French/solved.php


#
#---------[ 2. OPEN ]---------------------------------------------------------
#

viewtopic.php.php


#
#---------[ 3. FIND (line: 23) ]---------------------------------------------
#

// Load the viewtopic.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/topic.php';


#
#---------[ 4. AFTER, ADD ]-------------------------------------------------
#

/* Mod - Mark solved - start */
require PUN_ROOT.'lang/'.$pun_user['language'].'/solved.php';
/* Mod - Mark solved - end */


#
#---------[ 4. FIND (line: 99) ]-------------------------------------------------
#

	if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
		$post_link = "\t\t\t".'<p class="postlink conr"><a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a></p>'."\n";


#
#---------[ 5. Replace by ]-------------------------------------------------
#
if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
	{
		$post_link = "\t\t\t".'<p class="postlink conr"><a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a></p>'."\n";
		/* Mod - Mark solved - start */
		$is_solved = ( strpos($cur_topic['subject'], $lang_solved['__solved__']) === 0 ) ? true : false;
		if ( $cur_topic['poster_id'] == $pun_user['username'] || $is_admmod )
			$post_link .= "\t\t\t".'<p class="postlink conr '.($is_solved ? 'unmarksolved' : 'marksolved').'"><a href="solved.php?tid='.$id.'">'. ($is_solved ? $lang_solved['Mark as unsolved'] : $lang_solved['Mark as solved']).'</a></p>'."\n";
		/* Mod - Mark solved - end */
	}


#
#---------[ 6. SAVE/UPLOAD ]-------------------------------------------------
#
