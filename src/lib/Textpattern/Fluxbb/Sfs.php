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
     * IP address.
     *
     * @var string
     */

    protected $ip;

    /**
     * The email.
     *
     * @var string
     */

    protected $email;

    /**
     * Threshold.
     *
     * @var int
     */

    protected $activityRange = 5256000;

    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];

        foreach (get_class_methods($this) as $method)
        {
            if (strpos($method, 'filterPage') === 0 && $this->$method() === true)
            {
                return;
            }
        }
    }

    /**
     * Checks registration requests.
     */

    public function filterPageRegister()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'register.php') !== false && isset($_POST['req_user']) && isset($_POST['req_email1']))
        {
            $this->email = (string) $_POST['req_email1'];
            $this->processUser();
            return true;
        }
    }

    /**
     * Checks login requests.
     */

    public function filterPageLogin()
    {
        if (isset($_POST['form_sent']) && isset($_GET['action']) && $_GET['action'] === 'in' && isset($_POST['req_username']))
        {
            $sth = Db::pdo()->prepare("SELECT email FROM users WHERE username = :username and group_id = 0");
            $sth->execute(array(':username' => $_POST['req_username']));

            if ($r = $sth->fetch())
            {
                $this->email = $r['email'];
                $this->processUser();
            }

            return true;
        }
    }

    /**
     * Check on first post.
     */

    public function filterPagePost()
    {
        global $cookie_name;

        if (strpos($_SERVER['REQUEST_URI'], 'post.php') !== false && isset($_POST['form_sent']))
        {
            $id = (int) join('', array_slice(explode('|', $_COOKIE[$cookie_name]), 0, 1));
            $sth = Db::pdo()->prepare('SELECT email FROM users WHERE id = :user and num_posts = 0');
            $sth->execute(array(':user' => $id));

            if ($r = $sth->fetch())
            {
                $this->email = $r['email'];

                if ($this->isBanned() === false && $data = $this->getRecord())
                {
                    if (isset($data->email))
                    {
                        $this->addBan('email', 'Email address found in StopForumSpam database.');
                    }
                    else if (isset($data->ip))
                    {
                        $this->addBan('ip', 'IP address found in StopForumSpam database.', strtotime('+14 day'));
                    }
                }
            }

            return true;
        }
    }

    /**
     * Process user request.
     */

    public function processUser()
    {
        if ($this->isBanned() === false && $data = $this->getRecord())
        {
            if (isset($data->ip))
            {
                $this->addBan('ip', 'IP address found in StopForumSpam database.', strtotime('+2 month'));
            }

            if (isset($data->email))
            {
                $this->addBan('email', 'Email address found in StopForumSpam database.');
            }
        }
    }

    /**
     * Gets a record from StopForumSpam database for the given user.
     *
     * @return \stdClass|bool
     */

    public function getRecord()
    {
        $data = file_get_contents('http://www.stopforumspam.com/api?f=json&unix&ip='.urlencode($this->ip).'&email='.urlencode($this->email), false, stream_context_create(array('http' => array('timeout' => 15))));
        $seen = time() - $this->activityRange;

        if ($data)
        {
            $data = json_decode($data);
            $out = (object) null;

            if ($data && !empty($data->success))
            {
                foreach (array('ip', 'email') as $name)
                {
                    if (isset($data->$name) && $this->$name && $data->$name->appears && $data->$name->lastseen >= $seen)
                    {
                        $out->$name = $data->$name;
                    }
                }

                if ($out)
                {
                    return $out;
                }
            }
        }

        return false;
    }

    /**
     * Add a ban.
     */

    public function addBan($type = 'ip', $message = '', $expires = null)
    {
        Db::pdo()->prepare("INSERT INTO bans SET $type = :value, message = :message, expire = :expires")->execute(array(
            ':value'    => $this->$type,
            ':message'  => $message,
            ':expires'  => $expires,
        ));

        @unlink('./cache/cache_bans.php');
    }

    /**
     * Whether the user is banned.
     *
     * @return bool
     */

    public function isBanned()
    {
        $sth = Db::pdo()->prepare("SELECT ip FROM bans WHERE ((ip != '' and ip = :ip) or (email != '' and email = :email)) and IFNULL(expire, :expires) >= :expires limit 1");

        $sth->execute(array(
            ':ip'      => $ip,
            ':email'   => $email,
            ':expires' => time(),
        ));

        return (bool) $sth->rowCount();
    }
}
