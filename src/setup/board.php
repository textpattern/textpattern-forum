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
    //'Feature Ideas' => '',
    'Feedback' => array('Development', '[feedback] '),
    //'General Discussions' => '',
    //'How Do I…? & Other Questions' => '',
    'How-tos and Examples' => array('Archive', '[howto] '),
    //'Internationalisation' => '',
    //'Latest Happenings' => '',
    //'Let’s See Yours, Then' => '',
    'Mentions' => array('Latest Happenings', '[mention] '),
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
        'name'   => 'Official announcements',
        'desc'   => 'Latest official Textpattern project news from the core development team.<br><strong>External links:</strong> <a rel="external" href="http://textpattern.com/weblog/">Textpattern blog</a>.',
        'forums' => array('Official Announcements'),
    ),

    array(
        'name'   => 'Core development',
        'desc'   => 'Helping test current/future versions of Textpattern CMS. Experienced users only.<br><strong>External links:</strong> <a rel="external" href="https://github.com/textpattern">Textpattern on GitHub</a>, <a rel="external" href="https://code.google.com/p/textpattern/issues/list">Textpattern CMS Issue Tracker</a>.',
        'forums' => array('Development'),
    ),

    array(
        'name'   => 'Internationalisation',
        'desc'   => 'Translating and adapting Textpattern CMS for non-English users.<br><strong>External links:</strong> <a rel="external" href="https://github.com/textpattern/textpacks">Translations repository</a>, <a rel="external" href="https://github.com/textpattern/pophelp">Inline help translations repository</a>.',
        'forums' => array('Internationalisation'),
    ),

    array(
        'name'   => 'Feature ideas',
        'desc'   => 'Suggesting and discussing features you’d like to see added to the core in future Textpattern CMS releases.',
        'forums' => array('Feature Ideas'),
    ),

    // Assistance.

    array(
        'name'   => 'How do I…? and other questions',
        'desc'   => 'Requesting help with templates and asking questions.<br><strong>External links:</strong> <a rel="external" href="http://textpattern.net">Textpattern CMS User Documentation</a>, <a rel="external" href="http://txptips.com/">TXP Tips</a>.',
        'forums' => array('How Do I…? & Other Questions'),
    ),

    array(
        'name'   => 'Troubleshooting',
        'desc'   => 'Had a server meltdown? Textpattern CMS won’t run? Post your diagnostics reports and request help to track down problems.',
        'forums' => array('Troubleshooting')
    ),

    array(
        'name'   => 'Plugin author support',
        'desc'   => 'Support for existing third-party Textpattern plugins.<br><strong>External links:</strong> <a rel="external" href="http://textpattern.org">Textpattern CMS Plugins</a>.',
        'forums' => array('Plugin Author Support'),
    ),

    array(
        'name'   => 'Plugin discussions',
        'desc'   => 'Discussing third-party Textpattern plugins and their development—adapting plugins, adopting orphaned plugins, suggesting new plugins.',
        'forums' => array('Plugins', 'Plugin discussions'),
    ),

    array(
        'name'   => 'Theme author support',
        'desc'   => 'Support for existing, packaged Textpattern website themes and admin themes.<br><strong>External links:</strong> <a rel="external" href="http://textgarden.org/">Textpattern CMS Themes</a>.',
        'forums' => array('Packaged Designs', 'Theme author Support'),
    ),

    array(
        'name'   => 'Theme discussions',
        'desc'   => 'Building and distributing your own packaged Textpattern CMS-powered website themes and admin (control panel) themes.',
        'forums' => array('Presentation', 'Theme discussions')
    ),

    // Community.

    array(
        'name'   => 'General discussions',
        'desc'   => 'Discussing web development in general and other miscellaneous topics—anything not specifically Textpattern-related.',
        'forums' => array('General Discussions'),
    ),

    array(
        'name'   => 'Latest happenings',
        'desc'   => 'Recent and upcoming Textpattern community events and news, including discussions of official Textpattern announcements.',
        'forums' => array('Latest Happenings'),
    ),

    array(
        'name'   => 'Seeking Textpattern CMS pros',
        'desc'   => 'Hiring and looking for professional work. Getting in touch with experienced Textpattern CMS users for current, ongoing and future projects.',
        'forums' => array('Seeking Txp pros', 'Seeking TXP pros')
    ),

    array(
        'name'   => 'Showcase your Textpattern site',
        'desc'   => 'Showing the community your Textpattern CMS-powered websites and inviting helpful, constructive feedback.',
        'forums' => array('Let’s See Yours, Then')
    ),

    array(
        'name'   => 'Textpattern’s sites/social channels',
        'desc'   => 'Discussing the official Textpattern brand websites and social media channels.<br><strong>External links:</strong> <a rel="external" href="http://textpattern.com/">Textpattern.com</a>, <a rel="external" href="http://txpmag.com/">TXP magazine</a>.',
        'forums' => array('Textpattern’s Websites and Social Channels'),
    ),

    array(
        'name'   => 'Archives',
        'desc'   => 'Old stuff that we keep archived for historical reasons—be aware that topics within this forum are obsolete and may contain out-of-date information.',
        'forums' => array('Archive'),
    ),

    array(
        'name'   => 'Moderation',
        'desc'   => '<strong>Admins and moderators only.</strong> Questions and concerns regarding moderation of the Textpattern forum.',
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
