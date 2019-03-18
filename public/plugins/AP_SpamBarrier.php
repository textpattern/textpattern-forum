<?php
/***********************************************************************

  This software is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  This software is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
    exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);
define('PLUGIN_VERSION', '1.0.6');

// Load the AP_SpamBarrier.php language file
if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/AP_SpamBarrier.php'))
	$langPatcher = require PUN_ROOT.'lang/'.$pun_user['language'].'/AP_SpamBarrier.php';
else
	$langPatcher = require PUN_ROOT.'lang/English/AP_SpamBarrier.php';

if (isset($_POST['form_sent']))
{
	// Lazy referer check (in case base_url isn't correct)
	if (!preg_match('#/admin_loader\.php#i', $_SERVER['HTTP_REFERER']))
		message($lang_common['Bad referrer']);

	$form = array_map('trim', $_POST['form']);

	while (list($key, $input) = @each($form))
	{
		// Only update values that have changed
		if ((isset($pun_config['o_'.$key])) || ($pun_config['o_'.$key] == NULL))
		{
			if ($pun_config['o_'.$key] != $input)
			{
				if ($input != '' || is_int($input))
					$value = '\''.$db->escape($input).'\'';
				else
					$value = 'NULL';

				$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
			}
		}
	}

	// Regenerate the config cache
	require_once PUN_ROOT.'include/cache.php';
	generate_config_cache();

	redirect('admin_loader.php?plugin=AP_SpamBarrier.php', $lang_ap_spambarrier['Redirect message']);
}
else if (isset($_POST['search_users']))
{
	// Display the admin navigation menu
?>
<div class="linkst">
	<div class="inbox">
		<div><a href="javascript:history.go(-1)"><?php echo $lang_ap_spambarrier['Go_back'] ?></a></div>
	</div>
</div>

<div id="users2" class="blocktable">
	<h2><span><?php echo $lang_ap_spambarrier['SB_users'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_ap_spambarrier['Col_Username'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_ap_spambarrier['Col_Email'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_ap_spambarrier['Col_Posts'] ?></th>
					<th class="tc4" scope="col"><?php echo $lang_ap_spambarrier['Col_Website'] ?></th>
					<th class="tc5" scope="col"><?php echo $lang_ap_spambarrier['Col_Signature'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_ap_spambarrier['Col_Registered'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php
$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id > 1 AND num_posts=0 AND signature IS NOT NULL ORDER BY registered DESC LIMIT 50') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());

// If there are users with URLs in their signatures but 0 posts
if ($db->num_rows($result))
{
	while ($cur_user = $db->fetch_assoc($result))
	{
		if (isset($signature_cache[$cur_post['poster_id']]))
			$signature = $signature_cache[$cur_post['poster_id']];
		else
		{
			$signature = parse_signature($cur_post['signature']);
			$signature_cache[$cur_post['poster_id']] = $signature;
		}
		echo "\t\t\t\t\t\t".'<tr><td class="tcl"><a href="profile.php?id='.$cur_user['id'].'">'.pun_htmlspecialchars($cur_user['username']).'</a></td><td class="tc2">'.$cur_user['email'].'</td><td class="tc3">'.forum_number_format($cur_post['num_posts']).'</td><td class="tc4">'.pun_htmlspecialchars($cur_user['url']).'</td><td class="tc5">'.$signature.'</td><td class="tcr">'.format_time($cur_user['registered'], true).'</td></tr>'."\n";
	}
}
	else
		echo "\t\t\t\t".'<tr><td class="tcl" colspan="6">'.$lang_ap_spambarrier['No_match'].'</td></tr>'."\n";
?>
			</tbody>
			</table>
		</div>
	</div>
</div>

<div class="linksb">
	<div class="inbox">
		<div><a href="javascript:history.go(-1)"><?php echo $lang_ap_spambarrier['Go_back'] ?></a></div>
	</div>


<?php
}
else
{
	// Collect some statistics from the database
	$stats = array();
	
	$result = $db->query('SELECT MIN(date) FROM '.$db->prefix.'test_registrations') or error('Error1', __FILE__, __LINE__, $db->error());
	$stats['collecting_since'] = $db->result($result);

	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=0') or error('Error2', __FILE__, __LINE__, $db->error());
	$stats['num_nospam'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=1') or error('Error3', __FILE__, __LINE__, $db->error());
	$stats['num_honeypot'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=2') or error('Error4', __FILE__, __LINE__, $db->error());
	$stats['num_blacklist'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=3') or error('Error4', __FILE__, __LINE__, $db->error());
	$stats['num_dnsbl'] = $db->result($result);
	
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=0 AND date > '.(time() - 7*24*60*60)) or error('Error5', __FILE__, __LINE__, $db->error());
	$stats['avg_nospam'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=1 AND date > '.(time() - 7*24*60*60)) or error('Error6', __FILE__, __LINE__, $db->error());
	$stats['avg_honeypot'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=2 AND date > '.(time() - 7*24*60*60)) or error('Error7', __FILE__, __LINE__, $db->error());
	$stats['avg_blacklist'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=3 AND date > '.(time() - 7*24*60*60)) or error('Error7', __FILE__, __LINE__, $db->error());
	$stats['avg_dnsbl'] = $db->result($result);

	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 0 AND date GROUP BY DATE(FROM_UNIXTIME(date)) ORDER BY num_blocked DESC LIMIT 1') or error('Error8', __FILE__, __LINE__, $db->error());
	list($stats['most_nospam_date'], $stats['most_nospam_num']) = $db->fetch_row($result);
	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 1 AND date GROUP BY DATE(FROM_UNIXTIME(date)) ORDER BY num_blocked DESC LIMIT 1') or error('Error9', __FILE__, __LINE__, $db->error());
	list($stats['most_honeypot_date'], $stats['most_honeypot_num']) = $db->fetch_row($result);
	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 2 AND date GROUP BY DATE(FROM_UNIXTIME(date)) ORDER BY num_blocked DESC LIMIT 1') or error('Error10', __FILE__, __LINE__, $db->error());
	list($stats['most_blacklist_date'], $stats['most_blacklist_num']) = $db->fetch_row($result);
	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 3 AND date GROUP BY DATE(FROM_UNIXTIME(date)) ORDER BY num_blocked DESC LIMIT 1') or error('Error10', __FILE__, __LINE__, $db->error());
	list($stats['most_dnsbl_date'], $stats['most_dnsbl_num']) = $db->fetch_row($result);

	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 1 AND date > '.(time()-14*24*60*60).' GROUP BY DATE(FROM_UNIXTIME(date))') or error('Unable to fetch honeypot 14 day log', __FILE__, __LINE__, $db->error());
	while ($cur_date = $db->fetch_assoc($result))
		$stats['last_14days_honeypot'][$cur_date['day']] = $cur_date['num_blocked'];
	
	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 2 AND date > '.(time()-14*24*60*60).' GROUP BY DATE(FROM_UNIXTIME(date))') or error('Unable to fetch sfs 14 day log', __FILE__, __LINE__, $db->error());
	while ($cur_date = $db->fetch_assoc($result))
		$stats['last_14days_sfs'][$cur_date['day']] = $cur_date['num_blocked'];
	
	$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam = 3 AND date > '.(time()-14*24*60*60).' GROUP BY DATE(FROM_UNIXTIME(date))') or error('Unable to fetch dnsbl 14 day log', __FILE__, __LINE__, $db->error());
	while ($cur_date = $db->fetch_assoc($result))
		$stats['last_14days_dnsbl'][$cur_date['day']] = $cur_date['num_blocked'];


	// Display the admin navigation menu
	generate_admin_menu($plugin);
?>
	<div class="block">
		<h2><span>SpamBarrier - v<?php echo PLUGIN_VERSION ?></span></h2>
		<div class="box">
			<div class="inbox">
				<p><?php echo $lang_ap_spambarrier['Description'] ?></p>
			</div>
		</div>
	</div>
	<div class="blockform">
		<h2 class="block2"><span><?php echo $lang_ap_spambarrier['Options'] ?></span></h2>
		<div class="box">
			<form method="post" action="admin_loader.php?plugin=AP_SpamBarrier.php">
				<p class="submittop"><input type="submit" name="save" value="<?php echo $lang_ap_spambarrier['Save'] ?>" /></p>
				<div class="inform">
					<input type="hidden" name="form_sent" value="1" />
					<fieldset>
						<legend><?php echo $lang_ap_spambarrier['Settings'] ?></legend>
						<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['HP_check'] ?></th>
								<td>
									<input type="radio" name="form[sb_check_hp]" value="1"<?php if ($pun_config['o_sb_check_hp'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_check_hp]" value="0"<?php if ($pun_config['o_sb_check_hp'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['HP_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['HP_custom_field'] ?></th>
								<td>
									<input type="radio" name="form[sb_custom_field]" value="1"<?php if ($pun_config['o_sb_custom_field'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_custom_field]" value="0"<?php if ($pun_config['o_sb_custom_field'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['HP_custom_field_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['HP_custom_field_name'] ?></th>
								<td>
									<input type="text" name="form[sb_custom_field_name]" size="20" maxlength="30" value="<?php echo pun_htmlspecialchars($pun_config['o_sb_custom_field_name']) ?>" />
									<span><?php echo $lang_ap_spambarrier['HP_custom_field_name_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['SFS_reg_check'] ?></th>
								<td>
									<input type="radio" name="form[sb_check_sfs_register]" value="1"<?php if ($pun_config['o_sb_check_sfs_register'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_check_sfs_register]" value="0"<?php if ($pun_config['o_sb_check_sfs_register'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['SFS_reg_descrition'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['SFS_login_check'] ?></th>
								<td>
									<input type="radio" name="form[sb_check_sfs_login]" value="1"<?php if ($pun_config['o_sb_check_sfs_login'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_check_sfs_login]" value="0"<?php if ($pun_config['o_sb_check_sfs_login'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['SFS_log_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['Enable_SFS_report'] ?></th>
								<td>
									<input type="radio" name="form[sb_sfs_report]" value="1"<?php if ($pun_config['o_sb_sfs_report'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_sfs_report]" value="0"<?php if ($pun_config['o_sb_sfs_report'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['SFS_report_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['SFS_API'] ?></th>
								<td>
									<input type="text" name="form[sb_sfs_api_key]" size="20" maxlength="30" value="<?php echo pun_htmlspecialchars($pun_config['o_sb_sfs_api_key']) ?>" />
									<span><?php echo $lang_ap_spambarrier['SFS_api_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['DNSBL_login_check'] ?></th>
								<td>
									<input type="radio" name="form[sb_check_dnsbl_login]" value="1"<?php if ($pun_config['o_sb_check_dnsbl_login'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_check_dnsbl_login]" value="0"<?php if ($pun_config['o_sb_check_dnsbl_login'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['DNSBL_login_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['DNSBL_reg_check'] ?></th>
								<td>
									<input type="radio" name="form[sb_check_dnsbl_register]" value="1"<?php if ($pun_config['o_sb_check_dnsbl_register'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[sb_check_dnsbl_register]" value="0"<?php if ($pun_config['o_sb_check_dnsbl_register'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ap_spambarrier['No'] ?></strong>
									<span><?php echo $lang_ap_spambarrier['DNSBL_reg_description'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ap_spambarrier['DNSBL_list'] ?></th>
								<td>
									<span><?php echo $lang_ap_spambarrier['DNSBL_list_description'] ?></span><br />
									<textarea name="form[sb_dnsbl_names]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_sb_dnsbl_names']) ?></textarea>
								</td>
							</tr>
						</table>
						</div>
					</fieldset>
				</div>
			<p class="submitend"><input type="submit" name="save" value="<?php echo $lang_ap_spambarrier['Save'] ?>" /></p>
			</form>
		</div>
	</div>

	<div class="blockform block2">
		<h2><span><?php echo $lang_ap_spambarrier['Search_users'] ?></span></h2>
		<div class="box">
			<form method="post" action="admin_loader.php?plugin=AP_SpamBarrier.php">
				<div class="inbox">
					<p><?php echo $lang_ap_spambarrier['Search_description'] ?>
					</p>
				</div>
				<p class="submitend">
					<input type="submit" name="search_users" value="<?php echo $lang_ap_spambarrier['Go!'] ?>" />
				</p>
			</form>
		</div>
	</div>

	<div class="blockform block2">
		<h2><span><?php echo $lang_ap_spambarrier['Registration_stats'] ?></span></h2>
		<div id="adstats" class="box">
			<div class="inbox">
				<dl>
					<dt><?php echo $lang_ap_spambarrier['Collecting_since'] ?></dt>
					<dd>
						<?php echo ($stats['collecting_since'] != '') ? date($pun_config['o_date_format'], $stats['collecting_since']).' ('.floor((time()-$stats['collecting_since'])/(60*60*24)).' days)' : $lang_ap_spambarrier['N_A'] ?>
					</dd>
					<dt><?php echo $lang_ap_spambarrier['Total'] ?></dt>
					<dd>
						<?php echo $lang_ap_spambarrier['NS'] ?> <?php echo $stats['num_nospam'] ?><br />
						<?php echo $lang_ap_spambarrier['BBH'] ?> <?php echo $stats['num_honeypot'] ?><br />
						<?php echo $lang_ap_spambarrier['BBS'] ?> <?php echo $stats['num_blacklist'] ?><br />
						<?php echo $lang_ap_spambarrier['BBD'] ?> <?php echo $stats['num_dnsbl']."\n" ?>
					</dd>
					<dt><?php echo $lang_ap_spambarrier['Avg_7d'] ?></dt>
					<dd>
						<?php echo $lang_ap_spambarrier['NS'] ?><?php echo round($stats['avg_nospam'], 2) ?> <?php echo $lang_ap_spambarrier['per_day'] ?><br />
						<?php echo $lang_ap_spambarrier['BBH'] ?><?php echo round($stats['avg_honeypot'], 2) ?> <?php echo $lang_ap_spambarrier['per_day'] ?><br />
						<?php echo $lang_ap_spambarrier['BBS'] ?><?php echo round($stats['avg_blacklist'], 2) ?> <?php echo $lang_ap_spambarrier['per_day'] ?><br />
						<?php echo $lang_ap_spambarrier['BBD'] ?><?php echo round($stats['avg_dnsbl'], 2) ?> <?php echo $lang_ap_spambarrier['per_day'] ?>
					</dd>
					<dt><?php echo $lang_ap_spambarrier['Max_day'] ?></dt>
					<dd>
						<?php echo $lang_ap_spambarrier['NS'] ?><?php echo ($stats['most_nospam_num'] > 0) ? $stats['most_nospam_num'].' ('.$stats['most_nospam_date'].')' : '0' ?><br />
						<?php echo $lang_ap_spambarrier['BBH'] ?><?php echo ($stats['most_honeypot_num'] > 0) ? $stats['most_honeypot_num'].' ('.$stats['most_honeypot_date'].')' : '0' ?><br />
						<?php echo $lang_ap_spambarrier['BBS'] ?><?php echo ($stats['most_blacklist_num'] > 0) ? $stats['most_blacklist_num'].' ('.$stats['most_blacklist_date'].')'."\n" : '0' ?><br />
						<?php echo $lang_ap_spambarrier['BBD'] ?><?php echo ($stats['most_dnsbl_num'] > 0) ? $stats['most_dnsbl_num'].' ('.$stats['most_dnsbl_date'].')'."\n" : '0'."\n" ?>
					</dd>
                    <dt><?php echo $lang_ap_spambarrier['Block_14d'] ?></dt>
					<dd>
<?php
$result = $db->query('SELECT DATE(FROM_UNIXTIME(date)) AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam > 0 AND date > '.(time()-14*24*60*60).' GROUP BY DATE(FROM_UNIXTIME(date))
') or error($lang_ap_spambarrier['Unable_14d'], __FILE__, __LINE__, $db->error());

// If there are topics in this forum.
if ($db->num_rows($result))
{
	echo "\t\t\t\t\t\t".'<table>'."\n";
	echo "\t\t\t\t\t\t".'<tr><td style="padding: 0; border: 0; width:20%">Date</td><td style="padding: 0; border: 0; width:20%">Total</td><td style="padding: 0; border: 0; width:20%">HoneyPot</td><td style="padding: 0; border: 0; width:20%">SFS</td><td style="padding: 0; border: 0; width:20%">DNSBL</td></tr>'."\n";

	while ($cur_date = $db->fetch_assoc($result))
	{
		$day_honeypot = ($stats['last_14days_honeypot'][$cur_date['day']] != '') ? $stats['last_14days_honeypot'][$cur_date['day']] : '0';
		$day_sfs = ($stats['last_14days_sfs'][$cur_date['day']] != '') ? $stats['last_14days_sfs'][$cur_date['day']] : '0';
		$day_dnsbl = ($stats['last_14days_dnsbl'][$cur_date['day']] != '') ? $stats['last_14days_dnsbl'][$cur_date['day']] : '0';
		echo "\t\t\t\t\t\t".'<tr><td style="padding: 0; border: 0">'.$cur_date['day'].'</td><td style="padding: 0; border: 0">'.$cur_date['num_blocked'].'</td><td style="padding: 0; border: 0">'.$day_honeypot.'</td><td style="padding: 0; border: 0">'.$day_sfs.'</td><td style="padding: 0; border: 0">'.$day_dnsbl.'</td></tr>'."\n";
	}

	echo "\t\t\t\t\t\t".'</table>'."\n";
}
else
	echo $lang_ap_spambarrier['N_A'];
?>
					</dd>
				</dl>
			</div>
		</div>
	</div>


<?php
}
?>