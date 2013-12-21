<?php

/**
 * Textpattern Support Forum.
 *
 * @link    https://github.com/textpattern/textpattern-forum
 * @license MIT
 */

/*
 * Copyright (C) 2013 Team Textpattern
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
            $this->processUser();
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
        if ($address && $address !== '0.0.0.0' && filter_var($address, FILTER_FLAG_IPV4)) {
            $this->ip = (string) $address;
        }

        return $this;
    }

    /**
     * Checks login requests.
     */

    public function filterPageLogin()
    {
        if (isset($_POST['form_sent']) && isset($_GET['action']) &&
            $_GET['action'] === 'in' && isset($_POST['req_username'])
        ) {
            $sth = Db::pdo()->prepare(
                "SELECT id, username, email, registration_ip FROM users WHERE username = :username and ".
                "group_id = 0 limit 1"
            );

            $sth->execute(array(':username' => (string) $_POST['req_username']));

            if ($r = $sth->fetch()) {
                $this->email = $r['email'];
                $this->setIp($r['registration_ip']);

                if ($this->isBanned() === false && $data = $this->getRecord()) {
                    $date = date('c');

                    if (isset($data->ip)) {
                        $this->addBan(
                            'ip',
                            'SFSBOT: login '.$date.' - IP address found in StopForumSpam database.',
                            strtotime('+3 day')
                        );
                    }

                    if (isset($data->email)) {
                        $this->addBan(
                            'email',
                            'SFSBOT: login '.$date.' - email address found in StopForumSpam database.'
                        );
                    }

                    Db::pdo()->exec("DELETE FROM users WHERE id = ".intval($r['id']));
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
        global $cookie_name;

        if (strpos($_SERVER['REQUEST_URI'], 'post.php') !== false && isset($_POST['form_sent'])) {
            $id = (int) join('', array_slice(explode('|', $_COOKIE[$cookie_name]), 0, 1));
            $sth = Db::pdo()->prepare('SELECT email FROM users WHERE id = :user and num_posts = 0');
            $sth->execute(array(':user' => $id));

            if ($r = $sth->fetch()) {
                $this->email = $r['email'];

                if ($this->isBanned() === false && $data = $this->getRecord()) {
                    $date = date('c');

                    if (isset($data->email)) {
                        $this->addBan(
                            'email',
                            'SFSBOT: post '.$date.' - email address found in StopForumSpam database.'
                        );
                    } elseif (isset($data->ip)) {
                        $this->addBan(
                            'ip',
                            'SFSBOT: post '.$date.' - IP address found in StopForumSpam database.',
                            strtotime('+3 day')
                        );
                    }
                }
            } else {
                $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
            }

            return true;
        }
    }

    /**
     * Process user request.
     */

    public function processUser()
    {
        if ($this->isBanned() === false && $data = $this->getRecord()) {
            $date = date('c');

            if (isset($data->ip)) {
                $this->addBan(
                    'ip',
                    'SFSBOT: registeration '.$date.' - IP address found in StopForumSpam database.',
                    strtotime('+3 day')
                );
            }

            if (isset($data->email)) {
                $this->addBan(
                    'email',
                    'SFSBOT: registeration '.$date.' - email address found in StopForumSpam database.'
                );
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
        $query = http_build_query(array(
            'ip'    => $this->ip,
            'email' => $this->email,
        ));

        if (!$query) {
            return false;
        }

        $data = file_get_contents(
            'http://www.stopforumspam.com/api?f=json&unix&'.$query,
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
        if (empty($this->$type)) {
            return;
        }

        $sth = Db::pdo()->prepare("INSERT INTO bans SET $type = :value, message = :message, expire = :expires");

        foreach ((array) $this->$type as $value) {
            $sth->execute(array(
                ':value'    => $value,
                ':message'  => $message . "\nhttp://www.stopforumspam.com/search?q=" . urlencode($value),
                ':expires'  => $expires,
            ));
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
        $sth = Db::pdo()->prepare(
            "SELECT ip FROM bans WHERE ((ip != '' and ip = :ip) or (email != '' and email = :email)) and ".
            "IFNULL(expire, :expires) >= :expires limit 1"
        );

        $sth->execute(array(
            ':ip'      => $this->ip,
            ':email'   => $this->email,
            ':expires' => time(),
        ));

        return (bool) $sth->rowCount();
    }
}
