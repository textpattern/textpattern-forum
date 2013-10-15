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

include $config;

$options = array(
    'o_board_title'           => 'Textpattern CMS Support Forum',
    'o_board_desc'            => '',
    'o_timeout_visit'         => 2400,
    'o_timeout_online'        => 300,
    'o_redirect_delay'        => 1,
    'o_smilies'               => 0,
    'o_smilies_sig'           => 0,
    'o_make_links'            => 0,
    'o_show_post_count'       => 1,
    'o_default_style'         => 'Textpattern',
    'o_topic_review'          => 10,
    'o_disp_topics_default'   => 25,
    'o_disp_posts_default'    => 10,
    'o_indent_num_spaces'     => 4,
    'o_quickpost'             => 1,
    'o_users_online'          => 0,
    'o_censoring'             => 0,
    'o_show_dot'              => 1,
    'o_quickjump'             => 1,
    'o_gzip'                  => 0,
    'o_avatars'               => 1,
    'o_avatars_dir'           => 'img/avatars',
    'o_avatars_width'         => 250,
    'o_avatars_height'        => 250,
    'o_avatars_size'          => 512000,
    'o_search_all_forums'     => 1,
    'o_topic_subscriptions'   => 1,
    'o_regs_allow'            => 1,
    'o_regs_verify'           => 1,
    'o_rules'                 => 1,
    'o_maintenance'           => 0,
    'o_additional_navlinks'   => '',
    'p_message_bbcode'        => 0,
    'p_message_img_tag'       => 0,
    'p_message_all_caps'      => 0,
    'p_subject_all_caps'      => 0,
    'p_sig_bbcode'            => 0,
    'p_sig_img_tag'           => 0,
    'p_sig_length'            => 400,
    'p_sig_lines'             => 4,
    'p_allow_banned_email'    => 0,
    'p_allow_dupe_email'      => 0,
    'o_forum_subscriptions'   => 1,
    'p_force_guest_email'     => 1,
    'o_show_version'          => 0,
    'o_show_user_info'        => 1,
    'o_default_lang'          => 'English',
    'o_default_user_group'    => 5,
    'o_regs_report'           => 0,
    'o_default_email_setting' => 2,
    'o_topic_views'           => 1,
    'o_signatures'            => 1,
    'o_default_dst'           => 0,
    'o_quote_depth'           => 3,
    'o_feed_type'             => 2,
    'o_feed_ttl'              => 0,
);

echo "Updating FluxBB options...\n";

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE config SET conf_value = :value WHERE conf_name = :name');

foreach ($options as $name => $value)
{
    if ($sth->execute(array(':name'  => $name, ':value' => $value)))
    {
        echo "Updated {$name} to {$value}\n";
    }
    else
    {
        echo "Failed to update {$name}\n";
    }
}

echo "Clearing FluxBB cache...\n";

foreach (glob('./public/cache/*.php') as $file)
{
    unlink($file);
}
