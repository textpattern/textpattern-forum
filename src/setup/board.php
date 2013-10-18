<?php

echo "Reading config...\n";

$config = './public/config.php';

if (!file_exists($config))
{
    die('FluxBB not installed: config.php does not exists.');
}

include $config;

// Rename forums.

echo "Renaming forums...\n";

$rename = array(
    'Packaged Designs' => 'Theme Author Support',
    'Plugins'          => 'Plugin Discussions',
    'Presentation'     => 'Theme Discussions',
);

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE forums SET forum_name = :new WHERE forum_name = :old');

foreach ($rename as $old => $new)
{
    $sth->execute(array(':old' => $old, ':new' => $new));
}

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
    'Plugin Development' => array('Plugin Discussions', ''),
    'Plugin Requests' => array('Plugin Discussions', '[request] '),
    //'Plugins' => 'Plugin Discussions',
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
    'Official Announcements' => array(
        '<p>External link: <a rel="external" href="http://textpattern.com/weblog/">Weblog</a></p>',
    ),

    'Development' => array(
        '<p>Help test current and future versions of Textpattern. Experienced users only, please.</p>'.
        '<p>External links: <a rel="external" href="https://github.com/textpattern">GitHub</a>, <a rel="external" href="https://code.google.com/p/textpattern/issues/list">Issue tracker</a></p>',
    ),

    'Internationalisation' => array(
        '<p>Translating and adapting Textpattern for non-English users.</p>'.
        '<p>External link: <a rel="external" href="https://github.com/textpattern/textpacks">Translations repository</a></p>',
    ),

    'How Do I…? & Other Questions' => array(
        '<p>Requesting help with templates and asking questions.</p>'.
        '<p>External links: <a rel="external" href="http://textpattern.net">User Documentation</a>, <a rel="external" href="http://txptips.com/">TXP Tips</a></p>',
    ),

    'Troubleshooting' => array(
        '<p>Had a server meltdown, Textpattern won’t run? Post your diagnostics reports here.</p>',
    ),

    'Plugin Author Support' => array(
        '<p>External link: <a rel="external" href="http://textpattern.org">Textpattern Resources</a></p>',
    ),

    'Theme Author Support' => array(
        '<p>External link: <a rel="external" href="http://textgarden.org/">Textgarden</a></p>',
    ),

    'Plugin Discussions' => array(
        '',
    ),

    'Theme Discussions' => array(
        '',
    ),

    // Community.

    'General Discussions' => array(
        '<p>Web development, miscellaneous topics, anything not really Textpattern-related.</p>',
    ),

    'Latest Happenings' => array(
        '<p>Recent and upcoming TXP community events and news.</p>',
    ),

    'Seeking TXP pros' => array(
        '<p>Hiring and looking for work.</p>',
    ),

    'Textpattern’s Websites and Social Channels' => array(
        '<p>External links: <a rel="external" href="http://textpattern.com/">Textpattern.com</a>, <a rel="external" href="http://txpmag.com/">TXP</a></p>'.
        '<p>Social channels: <a rel="external" href="https://twitter.com/textpattern">@textpattern</a>, <a rel="external" href="https://twitter.com/txpmag">@txpmag</a>, <a rel="external" href="https://twitter.com/txpforum">@txpforum</a> on Twitter | <a rel="external" href="https://www.facebook.com/groups/textpattern/">Textpattern CMS</a> on Facebook | <a rel="external" href="https://plus.google.com/communities/111366418300163664690">Textpattern community</a>, <a rel="external" href="https://plus.google.com/107663405417732990755">Textpattern CMS</a>, <a rel="external" href="https://plus.google.com/102240548936231123918">TXP</a> on Google+</p>',
    ),

    'Archive' => array(
        '<p>Old stuff.</p>',
    ),

    'Moderation' => array(
        '<p>(Admins and moderators only) Questions and concerns regarding moderation of the Textpattern forum</p>',
    ),
);

$sth = Textpattern\Fluxbb\Db::pdo()->prepare('UPDATE forums SET forum_desc = :forum_desc, sort_by = :sort_by, disp_position = :disp_position WHERE forum_name = :forum_name');

$i = 1;

foreach ($update as $name => $data)
{
    $sth->execute(array(
        ':forum_name'    => $name,
        ':forum_desc'    => $data[0],
        ':sort_by'       => 0,
        ':disp_position' => $i++,
    ));
}

echo "Clearing FluxBB cache...\n";

foreach (glob('./public/cache/*.php') as $file)
{
    unlink($file);
}
