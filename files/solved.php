<?php

/**
 * File call for mark topic like solved.
 *
 * @version 1.0
 * @package FluxSolved
 * @copyright Copyright (c) 2010 Guillaume Kulakowski and contributors
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */

function solve_topic($topic_id)
{
	global $db, $lang_solved;

	// Mark topic as solved
	$result = $db->query('SELECT * FROM '.$db->prefix.'topics WHERE id='.$topic_id) or error('Unable to fetch topic', __FILE__, __LINE__, $db->error());
	$topic = $db->fetch_assoc($result);

	$subject = $topic['subject'];
	$is_solved = ( strpos($subject, $lang_solved['__solved__']) === 0 ) ? true : false;
	if ( $is_solved )
		$subject = str_replace($lang_solved['__solved__'], '', $subject);
	else
		$subject = $lang_solved['__solved__'].$subject;

	$db->query('UPDATE '.$db->prefix.'topics SET subject="'.$db->escape($subject).'" WHERE id='.$topic_id) or error('Unable to mark solved topic', __FILE__, __LINE__, $db->error());
}


define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
if ($tid < 1)
	message($lang_common['Bad request']);

// Fetch some info about the post, the topic and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, p.posted, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$tid.' ORDER BY p.id') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_topic = $db->fetch_assoc($result);

if ($pun_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == PUN_ADMIN || ($pun_user['g_moderator'] == '1' && array_key_exists($pun_user['username'], $mods_array))) ? true : false;

// Do we have permission to edit this post?
if ( ($cur_topic['poster_id'] != $pun_user['id'] && !$is_admmod) || ($cur_topic['closed'] && !$is_admmod) )
	message($lang_common['No permission']);

// Load the solved.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/solved.php';

$is_solved = ( strpos($cur_topic['subject'], $lang_solved['__solved__']) === 0 ) ? true : false;

if (isset($_POST['solved']))
{
	if ($is_admmod)
		confirm_referrer('solved.php');

		solve_topic($tid);
		redirect('viewtopic.php?id='.$cur_topic['tid'], $lang_solved['Post solve redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $is_solved ? $lang_solved['Mark as unsolved'] : $lang_solved['Mark as solved']);
define ('PUN_ACTIVE_PAGE', 'index');
require PUN_ROOT.'header.php';

require PUN_ROOT.'include/parser.php';
$cur_topic['message'] = parse_message($cur_topic['message'], $cur_topic['hide_smilies']);

?>
<div class="linkst">
	<div class="inbox">
		<ul class="crumbs">
			<li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li>
			<li><span>»&#160;</span><a href="viewforum.php?id=<?php echo $cur_topic['fid'] ?>"><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?></a></li>
			<li><span>»&#160;</span><a href="viewtopic.php?pid=<?php echo $tid ?>#p<?php echo $tid ?>"><?php echo pun_htmlspecialchars($cur_topic['subject']) ?></a></li>
			<li><span>»&#160;</span><strong><?php echo $is_solved ? $lang_solved['Mark as unsolved'] : $lang_solved['Mark as solved'] ?></strong></li>
		</ul>
	</div>
</div>

<div class="blockform">
	<h2><span><?php echo $is_solved ? $lang_solved['Mark as unsolved'] : $lang_solved['Mark as solved'] ?></span></h2>
	<div class="box">
		<form method="post" action="solved.php?tid=<?php echo $tid ?>">
			<div class="inform">
				<div class="forminfo">
					<h3><span><?php printf( $lang_solved['Topic by'], '<strong>'.pun_htmlspecialchars($cur_topic['poster']).'</strong>', format_time($cur_topic['posted'])) ?></span></h3>
					<p><?php echo '<strong>'.$lang_solved['Warning'].'</strong>' ?><br /><?php echo $lang_solved['Solved info'] ?></p>
				</div>
			</div>
			<p class="buttons"><input type="submit" name="solved" value="<?php echo $is_solved ? $lang_solved['Unsolved'] : $lang_solved['Solved'] ?>" /> <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>

<div id="postreview">
	<div class="blockpost">
		<div class="box roweven">
			<div class="inbox">
				<div class="postbody">
					<div class="postleft">
						<dl>
							<dt><strong><?php echo pun_htmlspecialchars($cur_topic['poster']) ?></strong></dt>
							<dd><span><?php echo format_time($cur_topic['posted']) ?></span></dd>
						</dl>
					</div>
					<div class="postright">
						<div class="postmsg">
							<?php echo $cur_topic['message']."\n" ?>
						</div>
					</div>
				</div>
				<div class="clearer"></div>
			</div>
		</div>
	</div>
</div>
<?php

require PUN_ROOT.'footer.php';
