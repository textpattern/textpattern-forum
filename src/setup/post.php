<?php

echo "Checking installation...\n";

$config = './public/config.php';

if (!file_exists($config))
{
    die('FluxBB not installed: config.php does not exists.');
}

echo "Removing installers...\n";
`rm -Rf public/install.php`;
`rm -Rf public/db_update.php`;

echo "Removing admin option pages that must be managed from the git repository...\n";

foreach (array(
    'public/admin_censoring.php',
    'public/admin_groups.php',
    'public/admin_categories.php',
    'public/admin_forums.php',
    'public/admin_permissions.php',
) as $file)
{
    echo "  {$file}\n";
    file_put_contents($file, "This file is managed from the git source repository.\n");
}

include $config;

$options = array(
    'o_additional_navlinks'   => '4 = <a href="help.php">Help</a>',
    //'o_admin_email'           => '',
    //'o_announcement'          => 0,
    //'o_announcement_message'  => '',
    'o_avatars'               => 1,
    'o_avatars_dir'           => 'img/avatars',
    'o_avatars_height'        => 60,
    'o_avatars_size'          => 10240,
    'o_avatars_width'         => 60,
    'o_base_url'              => TEXTPATTERN_FORUM_BASE_URL,
    'o_board_desc'            => '',
    'o_board_title'           => 'Textpattern CMS Support Forum',
    'o_censoring'             => 0,
    'o_date_format'           => 'Y-m-d',
    'o_default_dst'           => 0,
    'o_default_email_setting' => 2,
    'o_default_lang'          => 'English',
    'o_default_style'         => 'Textpattern',
    'o_default_timezone'      => 0,
    'o_default_user_group'    => 5,
    'o_disp_posts_default'    => 10,
    'o_disp_topics_default'   => 25,
    'o_feed_ttl'              => 30,
    'o_feed_type'             => 1,
    'o_forum_subscriptions'   => 1,
    'o_gzip'                  => 0,
    'o_indent_num_spaces'     => 4,
    //'o_mailing_list'          => '',
    'o_maintenance'           => 0,
    'o_maintenance_message'   => 'Sorry, we are currently doing maintenance, please come back later.',
    'o_make_links'            => 0,
    'o_quickjump'             => 1,
    'o_quickpost'             => 1,
    'o_quote_depth'           => 3,
    'o_redirect_delay'        => 1,
    'o_regs_allow'            => 1,
    'o_regs_report'           => 0,
    'o_regs_verify'           => 1,
    'o_report_method'         => 2,
    'o_rules'                 => 1,
    'o_rules_message'         => '<p>You <strong>must</strong> provide a real email address, or you will not be able to successfully register and participate in the forum. Your email address is only used to help us prevent spam from flooding the forum, and to allow you to make use of forum features (such as topic subscription). It is <strong>never</strong> shared or sold to third parties, nor will you be subscribed to any kind of mailing lists. You may allow or disallow other forum members from seeing your email address; guests cannot view any of your profile information.</p>
<p>All registrations are monitored by real people, and we take various proactive measures against spam. If <a href="http://en.wikipedia.org/wiki/Spam_(electronic)#Newsgroup_and_forum" rel="external">spam</a> or <a href="http://en.wikipedia.org/wiki/Troll_(Internet)" rel="external">trolling</a> is your intention, we suggest you spare your energy!</p>
<p>Most of the email addresses we ban are those that have been used to abuse the forum. Some email domains, however, are banned only because those mail providers refuse to accept the registration email the forum software sends (which is required, as mentioned above), and bounce the email back to us. While we make all possible efforts on our part to reduce the risk of undeliverable mail, it is recommended that you <a href="help.php#forum-help-undeliverable-mail">check to see if your email address domain is one of those known to be affected</a> (if so, you will need to provide an alternate email address).</p>',
    'o_search_all_forums'     => 1,
    'o_show_dot'              => 1,
    'o_show_post_count'       => 1,
    'o_show_user_info'        => 1,
    'o_show_version'          => 0,
    'o_signatures'            => 1,
    'o_smilies'               => 0,
    'o_smilies_sig'           => 0,
    'o_smtp_host'             => null,
    'o_smtp_pass'             => null,
    'o_smtp_ssl'              => 0,
    'o_smtp_user'             => 0,
    'o_timeout_online'        => 300,
    'o_timeout_visit'         => 2400,
    'o_time_format'           => 'H:i:s',
    'o_topic_review'          => 0,
    'o_topic_subscriptions'   => 1,
    'o_topic_views'           => 1,
    'o_users_online'          => 0,
    //'o_webmaster_email'       => '',
    'p_allow_banned_email'    => 0,
    'p_allow_dupe_email'      => 0,
    'p_force_guest_email'     => 1,
    'p_message_all_caps'      => 0,
    'p_message_bbcode'        => 0,
    'p_message_img_tag'       => 0,
    'p_sig_all_caps'          => 0,
    'p_sig_bbcode'            => 0,
    'p_sig_img_tag'           => 0,
    'p_sig_length'            => 400,
    'p_sig_lines'             => 4,
    'p_subject_all_caps'      => 0,
);

echo "Updating FluxBB options...\n";

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE config SET conf_value = :value WHERE conf_name = :name');

foreach ($options as $name => $value)
{
    if ($sth->execute(array(':name' => $name, ':value' => $value)))
    {
        echo "  {$name}\n";
    }
    else
    {
        echo "Failed to update {$name}\n";
    }
}

echo "Updating user options...\n";

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE users SET style = :style');
$sth->execute(array(':style' => $options['o_default_style']));

echo "Clearing FluxBB cache...\n";

foreach (glob('./public/cache/*.php') as $file)
{
    unlink($file);
}
