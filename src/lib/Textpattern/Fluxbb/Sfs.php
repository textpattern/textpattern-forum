<?php

/**
 * Textpattern Support Forum.
 *
 * @link    https://github.com/textpattern/textpattern-forum
 * @license MIT
 */

/*
 * Copyright (C) 2021 Team Textpattern
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Textpattern\Fluxbb;

/**
 * Checks StopForumSpam database against user-data.
 *
 * <code>
 * new \Textpattern\Fluxbb\Sfs;
 * </code>
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
        $this->setIp($_SERVER['REMOTE_ADDR']);

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'filterPage') === 0 && $this->$method() === true) {
                return;
            }
        }
    }

    /**
     * Checks registration requests.
     */

    public function filterPageRegister()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'register.php') !== false &&
            isset($_POST['req_user']) && isset($_POST['req_email1'])
        ) {
            $this->email = (string) $_POST['req_email1'];

            if ($this->isBanned() === false && $data = $this->getRecord()) {
                sleep(5);
                setcookie('textpattern_fluxbb_message', 2);
                header('Location: '.\TEXTPATTERN_FORUM_BASE_URL.'/register.php?agree=Agree');
                die;
            }

            return true;
        }
    }

    /**
     * Sets the IP.
     *
     * @param  string $address The address
     * @return Sfs
     */

    public function setIp($address)
    {
        if ($address && filter_var($address, FILTER_FLAG_IPV4)) {
            $this->ip = (string) $address;
        }

        return $this;
    }

    /**
     * Checks login requests.
     */

    public function filterPageLogin()
    {
        global $db;

        if (isset($_POST['form_sent']) && isset($_GET['action']) &&
            $_GET['action'] === 'in' && isset($_POST['req_username'])
        ) {
            $sth = Db::pdo()->prepare(
                "SELECT id, username, email, registration_ip FROM ".$db->prefix."users WHERE username = :username and ".
                "group_id = 0 limit 1"
            );

            $sth->execute(array(':username' => (string) $_POST['req_username']));

            if ($r = $sth->fetch()) {
                $this->email = $r['email'];
                $this->setIp($r['registration_ip']);
                $data = false;

                if ($this->isBanned() || $data = $this->getRecord()) {
                    sleep(5);
                    Db::pdo()->exec("DELETE FROM ".$db->prefix."users WHERE id = ".intval($r['id']));
                }

                if ($data) {
                    setcookie('textpattern_fluxbb_message', 1);
                    header('Location: '.\TEXTPATTERN_FORUM_BASE_URL.'/index.php');
                    die;
                }
            }

            return true;
        }
    }

    /**
     * Check on first post.
     */

    public function filterPagePost()
    {
        global $cookie_name, $db;

        if (strpos($_SERVER['REQUEST_URI'], 'post.php') !== false && isset($_POST['form_sent'])) {
            $id = (int) join('', array_slice(explode('|', $_COOKIE[$cookie_name]), 0, 1));
            $sth = Db::pdo()->prepare('SELECT email FROM '.$db->prefix.'users WHERE id = :user and num_posts = 0');
            $sth->execute(array(':user' => $id));

            if ($r = $sth->fetch()) {
                $this->email = $r['email'];
                $data = false;

                if ($this->isBanned() || $data = $this->getRecord()) {
                    sleep(5);
                    Db::pdo()->exec("DELETE FROM ".$db->prefix."users WHERE id = {$id}");
                }

                if ($data) {
                    setcookie('textpattern_fluxbb_message', 1);
                    header('Location: '.\TEXTPATTERN_FORUM_BASE_URL.'/index.php');
                    die;
                }
            } else {
                $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
            }

            return true;
        }
    }

    /**
     * Gets a record from StopForumSpam database for the given user.
     *
     * @return \stdClass|bool
     */

    public function getRecord()
    {
        $query = http_build_query(array(
            'ip'    => $this->ip,
            'email' => $this->email,
        ));

        if (!$query) {
            return false;
        }

        $data = file_get_contents(
            'https://www.stopforumspam.com/api?f=json&unix&'.$query,
            false,
            stream_context_create(array('http' => array('timeout' => 15)))
        );

        $seen = time() - $this->activityRange;

        if ($data) {
            $data = json_decode($data);
            $out = (object) null;
            $return = false;

            if ($data && !empty($data->success)) {
                foreach (array('ip', 'email') as $name) {
                    if (isset($data->$name) && $this->$name) {
                        if (!is_array($data->$name)) {
                            $data->$name = array($data->$name);
                        }

                        $items = array();

                        foreach ($data->$name as $item) {
                            if ($item->appears && $item->lastseen >= $seen) {
                                $items[] = $item;
                            }
                        }

                        if ($items) {
                            $out->$name = $items;
                            $return = true;
                        }
                    }
                }

                if ($return) {
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
        global $db;

        if (empty($this->$type)) {
            return;
        }

        $message .= ' - https://www.stopforumspam.com/search?q=';
        $sth = Db::pdo()->prepare("INSERT INTO ".$db->prefix."bans SET $type = :value, message = :message, expire = :expires");

        foreach ((array) $this->$type as $value) {
            $sth->bindValue(':value', $value);
            $sth->bindValue(':message', $message . urlencode($value));
            $sth->bindValue(':expires', $expires, \PDO::PARAM_INT);
            $sth->execute();
        }

        @unlink('./cache/cache_bans.php');
    }

    /**
     * Whether the user is banned.
     *
     * @return bool
     */

    public function isBanned()
    {
        global $db;

        $sth = Db::pdo()->prepare(
            "SELECT ip FROM ".$db->prefix."bans WHERE ((ip != '' and ip = :ip) or (email != '' and email = :email)) and ".
            "IFNULL(expire, :expires) >= :expires limit 1"
        );

        $sth->bindValue(':ip', $this->ip);
        $sth->bindValue(':email', $this->email);
        $sth->bindValue(':expires', time(), \PDO::PARAM_INT);
        $sth->execute();

        return (bool) $sth->rowCount();
    }
}
