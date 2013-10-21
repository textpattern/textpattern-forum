<?php

echo "Reading config...\n";

$config = './public/config.php';

if (!file_exists($config))
{
    die('FluxBB not installed: config.php does not exist.');
}

include $config;

echo "Moving topics...\n";

// Move topics.

$move = array(
    'Announcements Discussion' => array('Latest Happenings', ''),
    //'Archive' => 'Archive',
    'Contribute' => array('Development', '[contrib] '),
    'Developer Support' => array('Development', ''),
    //'Development' => 'Development',
    'Existing Issues' => array('Development', '[issue] '),
    'Feature Ideas' => array('Development', '[idea] '),
    'Feedback' => array('Development', '[feedback] '),
    //'General Discussions' => '',
    //'How Do I…? & Other Questions' => '',
    'How-tos and Examples' => array('Archive', '[howto] '),
    //'Internationalisation' => '',
    //'Latest Happenings' => '',
    'Let’s See Yours, Then' => array('General Discussions', '[website] '),
    'Mentions' => array('Mentions', '[mention] '),
    //'Moderation' => '',
    //'Official Announcements' => '',
    'Orphan Plugins' => array('Archive', '[plugin] '),
    //'Packaged Designs',
    'Past Inquiries' => array('Seeking Txp Pros', ''),
    'Plugin Archives' => array('Archive', ''),
    //'Plugin Author Support' => '',
    'Plugin Development' => array('Plugins', ''),
    'Plugin Requests' => array('Plugins', '[request] '),
    //'Plugins' => 'Plugins',
    //'Presentation' => '',
    'Previous Troubles' => array('Troubleshooting', '[resolved] '),
    //'Seeking Txp Pros' => '',
    'Textile' => array('General Discussions', '[textile] '),
    //'Textpattern’s Websites and Social Channels' => '',
    //'Troubleshooting' => '',
    'User Documentation' => array('Archive', '[wiki] '),
);

// Add in International Users category.

$forums = Textpattern\Fluxbb\Db::pdo()->query("SELECT forum_desc, forum_name FROM forums WHERE cat_id IN(SELECT id FROM categories WHERE cat_name = 'International Users')");

if ($forums)
{
    foreach ($forums->fetchAll() as $r)
    {
        $move[$r['forum_name']] = array('Archive', '['.substr($r['forum_desc'], 0, 5).'] ');
    }
}

$forum = Textpattern\Fluxbb\Db::pdo()->prepare('SELECT id, num_topics, num_posts, last_post, last_post_id, last_poster FROM forums WHERE forum_name = :forum_name limit 1');

$moveTopic = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE topics SET forum_id = :new_forum, subject = CONCAT(:tags, subject) WHERE forum_id = :old_forum');

$updateCounts = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE forums SET num_topics = num_topics + :topic, num_posts = num_posts + :posts WHERE id = :id');

$updatePoster = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE forums SET last_post = :last_post, last_post_id = :last_post_id, last_poster = :last_poster WHERE id = :id and last_post_id < :last_post_id');

$deleteSubs = Textpattern\Fluxbb\Db::pdo()->prepare('DELETE FROM forum_subscriptions WHERE forum_id = :id');

$deletePerms = Textpattern\Fluxbb\Db::pdo()->prepare('DELETE FROM forum_perms WHERE forum_id = :id');

$deleteForum = Textpattern\Fluxbb\Db::pdo()->prepare('DELETE FROM forums WHERE id = :id');

foreach ($move as $from => $to)
{
    // Get the current forum data.

    if ($forum->execute(array(':forum_name' => $from)) === false)
    {
        break;
    }

    if (!($r = $forum->fetch()))
    {
        continue;
    }

    // New forum id.

    if ($forum->execute(array(':forum_name' => $to[0])) === false)
    {
        break;
    }
    
    if (!($id = $forum->fetchColumn()))
    {
        continue;
    }

    // Move topics.

    if (
        $moveTopic->execute(array(
            ':new_forum' => $id,
            ':old_forum' => $r['id'],
            ':tags'      => $to[1],
        )) === false
    )
    {
        break;
    }

    // Update counts.

    if (
        $updateCounts->execute(array(
            ':topic' => (int) $r['num_topics'],
            ':posts' => (int) $r['num_posts'],
            ':id'    => $id,
        )) === false
    )
    {
        break;
    }

    // Update last poster.

    if (
        $updatePoster->execute(array(
            ':last_post'    => (int) $r['last_post'],
            ':last_post_id' => (int) $r['last_post_id'],
            ':last_poster'  => $r['last_poster'],
            ':id'           => $id,
        )) === false
    )
    {
        break;
    }

    // Delete old forum.

    $deleteSubs->execute(array(':id' => $r['id']));
    $deletePerms->execute(array(':id' => $r['id']));
    $deleteForum->execute(array(':id' => $r['id']));
}

// Remove categories.

$deleteCat = Textpattern\Fluxbb\Db::pdo()->prepare('DELETE FROM categories WHERE cat_name = :name');
$deleteCat->execute(array(':name' => 'International Users'));

// Updating forums.

echo "Updating forum meta data...\n";

$update = array(

    // Textpattern.

    array(
        'name'   => 'Official Announcements',
        'desc'   => '<p>External link: <a rel="external" href="http://textpattern.com/weblog/">Weblog</a></p>',
        'forums' => array('Official Announcements'),
    ),

    array(
        'name'   => 'Development',
        'desc'   => '<p>Help test current and future versions of Textpattern. Experienced users only, please.</p>'.
        '<p>External links: <a rel="external" href="https://github.com/textpattern">GitHub</a>, <a rel="external" href="https://code.google.com/p/textpattern/issues/list">Issue tracker</a></p>',
        'forums' => array('Development'),
    ),

    array(
        'name'   => 'Internationalisation',
        'desc'   => '<p>Translating and adapting Textpattern for non-English users.</p>'.
        '<p>External link: <a rel="external" href="https://github.com/textpattern/textpacks">Translations repository</a></p>',
        'forums' => array('Internationalisation'),
    ),

    // Assistance.

    array(
        'name'   => 'How Do I…? & Other Questions',
        'desc'   => '<p>Requesting help with templates and asking questions.</p>'.
        '<p>External links: <a rel="external" href="http://textpattern.net">User Documentation</a>, <a rel="external" href="http://txptips.com/">TXP Tips</a></p>',
        'forums' => array('How Do I…? & Other Questions'),
    ),

    array(
        'name'   => 'Troubleshooting',
        'desc'   => '<p>Had a server meltdown, Textpattern won’t run? Post your diagnostics reports here.</p>',
        'forums' => array('Troubleshooting')
    ),

    array(
        'name'   => 'Plugin Author Support',
        'desc'   => '<p>External link: <a rel="external" href="http://textpattern.org">Textpattern Resources</a></p>',
        'forums' => array('Plugin Author Support'),
    ),

    array(
        'name'   => 'Theme author Support',
        'desc'   => '<p>External link: <a rel="external" href="http://textgarden.org/">Textgarden</a></p>',
        'forums' => array('Packaged Designs', 'Theme author Support'),
    ),

    array(
        'name'   => 'Plugin discussions',
        'desc'   => '',
        'forums' => array('Plugins', 'Plugin discussions'),
    ),

    array(
        'name'   => 'Theme discussions',
        'desc'   => '',
        'forums' => array('Presentation', 'Theme discussions')
    ),

    // Community.

    array(
        'name'   => 'General Discussions',
        'desc'   => '<p>Web development, miscellaneous topics, anything not really Textpattern-related.</p>',
        'forums' => array('General Discussions'),
    ),

    array(
        'name'   => 'Latest Happenings',
        'desc'   => '<p>Recent and upcoming TXP community events and news.</p>',
        'forums' => array('Latest Happenings'),
    ),

    array(
        'name'   => 'Seeking TXP pros',
        'desc'   => '<p>Hiring and looking for work.</p>',
        'forums' => array('Seeking Txp pros', 'Seeking TXP pros')
    ),

    array(
        'name'   => 'Textpattern’s Websites and Social Channels',
        'desc'   => '<p>External links: <a rel="external" href="http://textpattern.com/">Textpattern.com</a>, <a rel="external" href="http://txpmag.com/">TXP</a></p>'.
        '<p>Social channels: <a rel="external" href="https://twitter.com/textpattern">@textpattern</a>, <a rel="external" href="https://twitter.com/txpmag">@txpmag</a>, <a rel="external" href="https://twitter.com/txpforum">@txpforum</a> on Twitter | <a rel="external" href="https://www.facebook.com/groups/textpattern/">Textpattern CMS</a> on Facebook | <a rel="external" href="https://plus.google.com/communities/111366418300163664690">Textpattern community</a>, <a rel="external" href="https://plus.google.com/107663405417732990755">Textpattern CMS</a>, <a rel="external" href="https://plus.google.com/102240548936231123918">TXP</a> on Google+</p>',
        'forums' => array('Textpattern’s Websites and Social Channels'),
    ),

    array(
        'name'   => 'Archive',
        'desc'   => '<p>Old stuff.</p>',
        'forums' => array('Archive'),
    ),

    array(
        'name'   => 'Moderation',
        'desc'   => '<p>(Admins and moderators only) Questions and concerns regarding moderation of the Textpattern forum</p>',
        'forums' => array('Moderation'),
    ),
);

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE forums SET forum_name = :new_name, forum_desc = :forum_desc, sort_by = :sort_by, disp_position = :disp_position WHERE forum_name = :forum_name');

$i = 1;

foreach ($update as $data)
{
    foreach ($data['forums'] as $name)
    {
        $sth->execute(array(
            ':forum_name'    => $name,
            ':new_name'      => $data['name'],
            ':forum_desc'    => $data['desc'],
            ':sort_by'       => 0,
            ':disp_position' => $i,
        ));
    }

    $i++;
}

echo "Clearing FluxBB cache...\n";

foreach (glob('./public/cache/*.php') as $file)
{
    unlink($file);
}
