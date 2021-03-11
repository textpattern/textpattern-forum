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
 * Filter incoming requests.
 */

class Filter
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->preventUnverified();
        //$this->preventMassBan();
    }

    /**
     * Prevent creating login cookies as guest or unverified.
     */

    protected function preventUnverified()
    {
        global $cookie_name;

        if (isset($_COOKIE[$cookie_name])) {
            $id = (int) join('', array_slice(explode('|', $_COOKIE[$cookie_name]), 0, 1));

            if (!$id) {
                unset($_COOKIE[$cookie_name]);
                return;
            }

            $sth = Db::pdo()->prepare('SELECT id FROM fbb_users WHERE id = :user and group_id = 0');
            $sth->execute(array(':user' => $id));

            if ($sth->rowCount()) {
                unset($_COOKIE[$cookie_name]);
                return;
            }
        }
    }

    /**
     * Prevent admins from banning us all by IP.
     *
     * @since 0.1.1
     */

//    protected function preventMassBan()
//    {
//        if (isset($_POST['ban_ip'])) {
//            $ip = trim((string) $_POST['ban_ip']);
//
//            if (filter_var($ip, FILTER_FLAG_IPV4) === false) {
//                $GET['ban_ip'] = $_POST['ban_ip'] = '';
//            }
//        }
//    }
}
