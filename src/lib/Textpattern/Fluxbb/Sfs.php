<?php

namespace Textpattern\Fluxbb;

/**
 * Checks StopForumSpam database against user-data.
 *
 * @example
 * use Textpattern\Fluxbb\Sfs;
 * new Sfs;
 */

class Sfs
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        global $db_host, $db_name, $db_username, $db_password, $db_prefix;

        $ip = $_SERVER['REMOTE_ADDR'];
        $since = time() - 3600*24*60;
        $user = $email = '';
        $action = false;

        if (strpos($_SERVER['REQUEST_URI'], 'register.php') !== false && isset($_POST['req_user']) && isset($_POST['req_email1']))
        {
            $user = (string) $_POST['req_user'];
            $email = (string) $_POST['req_email1'];
            $action = 'register';
        }

        if (isset($_POST['form_sent']) && isset($_GET['action']) && $_GET['action'] === 'in' && isset($_POST['req_username']))
        {
            $action = 'login';
            $user = $_POST['req_username'];
        }

        if (!$action)
        {
            return;
        }

        $pdo = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch user details.

        if ($action === 'login')
        {
            $sth = $pdo->prepare("SELECT group_id, email FROM {$db_prefix}users WHERE username = :username");
            $sth->execute(array(':username' => $user));
            $r = $sth->fetch();

            if (!$r || $r['group_id'] != 0)
            {
                return;
            }

            $email = $r['email'];
        }

        // Check if the user is already banned.

        $sth = $pdo->prepare("SELECT ip FROM {$db_prefix}bans WHERE ((ip != '' and ip = :ip) or (email != '' and email = :email)) and IFNULL(expire, :expires) >= :expires limit 1");

        $sth->execute(array(
            ':ip'      => $ip,
            ':email'   => $email,
            ':expires' => time(),
        ));

        if ($sth->rowCount())
        {
            return;
        }

        // Get records from StopForumSpam database.

        $data = file_get_contents('http://www.stopforumspam.com/api?f=json&unix&ip='.urlencode($ip).'&email='.urlencode($email).'&username='.urlencode($user), false, stream_context_create(array('http' => array('timeout' => 15))));

        if (!$data)
        {
            return;
        }

        $data = json_decode($data);

        if (!$data || empty($data->success))
        {
            return;
        }

        // Add new bans to the database.

        if ($ip && isset($data->ip) && $data->ip->appears && $data->ip->lastseen >= $since)
        {
            $pdo->prepare("INSERT INTO {$db_prefix}bans SET ip = :ip, message = :message, expire = :expires")->execute(array(
                ':ip'       => $ip,
                ':message'  => 'IP address found in StopForumSpam database.',
                ':expires'  => strtotime('+2 month'),
            ));

            @unlink('./cache/cache_bans.php');
        }

        if ($email && isset($data->email) && $data->email->appears && $data->email->lastseen > $since)
        {
            $pdo->prepare("INSERT INTO {$db_prefix}bans SET email = :email, message = :message")->execute(array(
                ':email'    => $email,
                ':message'  => 'Email address found in StopForumSpam database.',
            ));

            @unlink('./cache/cache_bans.php');
        }
    }
}
