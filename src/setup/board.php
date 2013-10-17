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
    //'User Documentation' => '',
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

echo "Clearing FluxBB cache...\n";

foreach (glob('./public/cache/*.php') as $file)
{
    unlink($file);
}
