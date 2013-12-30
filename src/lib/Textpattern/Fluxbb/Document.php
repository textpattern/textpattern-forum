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
 * Modifies page contents.
 */

class Document
{
    /**
     * Message.
     *
     * @var string
     */

    protected $message = '';

    /**
     * Constructor.
     */

    public function __construct()
    {
        if (isset($_COOKIE['textpattern_fluxbb_message'])) {
            switch ((int) $_COOKIE['textpattern_fluxbb_message']) {
                case 1:
                    $this->message = <<<EOF
                        <p class="alert-block error">
                            In scans we have found that your IP or email has been involved
                            in spam activity. This process is automatic and isn't specifically targeting you.
                            To protect our users, we have removed your unused, newly created account.
                            If you are certain you are innocent, make sure there isn't malware in
                            your network or that you aren't using a public proxy server that is being abused 
                            in such activity.
                        </p>
EOF;
                    break;
                case 2:
                    $this->message = <<<EOF
                        <p class="alert-block error">
                            Either your IP or the specified email address has been involved in spam activity.
                            Please try a different email address and make sure there isn't malware in
                            your network or that you aren't using a public proxy server.
                        </p>
EOF;
                    break;
                case 3:
                    $this->message = '<p class="alert-block error">Incorrect CAPTCHA. Please try again.</p>';

                    foreach (array(
                        'req_user',
                        'req_email1',
                        'req_email2',
                        'timezone',
                        'email_setting',
                    ) as $name) {
                        if (isset($_GET[$name]) && !isset($_POST[$name])) {
                            $_POST[$name] = (string) $_GET[$name];
                        }
                    }

                    break;
            }

            setcookie('textpattern_fluxbb_message', '', time() - 3600);
        }

        ob_start(array($this, 'buffer'));
    }

    /**
     * Buffer handler.
     */

    public function buffer($buffer)
    {
        $buffer = str_replace(
            array(
                '<!--[if lte IE 6]><script type="text/javascript" src="style/imports/minmax.js"></script><![endif]-->',
                '<link rel="stylesheet" type="text/css" href="style/Textpattern.css" />',
            ),
            '',
            $buffer
        );

        if ($this->message) {
            $buffer = str_replace('<div class="container">', '<div class="container">'.$this->message, $buffer);
        }

        $help = '';

        if (preg_match('#<li id="navextra1">(.*?)</li>#', $buffer, $matches)) {
            $help = $matches[1];
            $buffer = str_replace($matches[0], '', $buffer);
        }

        if (preg_match('#<li id="navprofile"><a href="profile\.php\?id=([0-9]+)">(.*?)</a></li>#', $buffer, $matches)) {
            $buffer = str_replace($matches[0], '', $buffer);
            $profile = (int) $matches[1];
            $logout = '';

            if (preg_match('#<li id="navlogout">(.*?)</li>#', $buffer, $matches)) {
                $logout = $matches[1];
                $buffer = str_replace($matches[0], '', $buffer);
            }

            $buffer = preg_replace(
                '#(<div id="brdwelcome" class="inbox">.*?<li><span>.*?)<strong>(.*?)</strong>(</span></li>)#s',
                '$1<strong><a href="profile.php?id='.$profile.'">$2</a></strong> | '.
                $logout.' | '.$help.'$3',
                $buffer
            );
        } else {

            $login = $register = '';

            if (preg_match('#<li id="navlogin"(?: class="isactive")?>(.*?)</li>#', $buffer, $matches)) {
                $login = $matches[1];
                $buffer = str_replace($matches[0], '', $buffer);
            }

            if (preg_match('#<li id="navregister"(?: class="isactive")?>(.*?)</li>#', $buffer, $matches)) {
                $register = $matches[1];
                $buffer = str_replace($matches[0], '', $buffer);
            }

            $buffer = preg_replace(
                '#(<div id="brdwelcome" class="inbox">.*?<p class="conl">)(.*?)(</p>)#s',
                '$1$2 '.$register.' | '.$login.' | '.$help.'$3',
                $buffer
            );
        }

        $buffer = preg_replace('#<li class="postquote">.*?</li>#', '', $buffer);

        $buffer = preg_replace('/ (onchange|onsubmit|onclick)=".*?"/', '', $buffer);

        $buffer = preg_replace('/<a href="javascript:.*?">.*?<\/a>/', '', $buffer);

        $buffer = preg_replace('/<script type="text\/javascript">.*?<\/script>/s', '', $buffer);

        $buffer = preg_replace(
            '/<ul class="bblinks">.*?<\/ul>/s',
            '<p class="textile-help-links">Formatting: '.
            '<a target="_blank" href="http://textpattern.com/textile-reference-manual">Textile</a>'.
            '</p>',
            $buffer
        );

        if (strpos($_SERVER['REQUEST_URI'], 'viewtopic.php') === false) {
            return $buffer;
        }

        $meta = array();

        // Add open graph metas to the topic pages.

        if (preg_match('#<title>(.*?) \(\w+ [0-9]+\) / (.*?) / (.*?)</title>#', $buffer, $title)) {
            if (preg_match(
                '#<dd class="postavatar">'.
                '<img src="([a-z0-9\./\:]+)\?m=[0-9]{0,}" width="60" height="60" alt="" />'.
                '</dd>#',
                $buffer,
                $avatar
            )) {
                $meta['og:image'] = $avatar[1];
            }

            if (preg_match('#<div class="postmsg">(.*?)</div>#s', $buffer, $post)) {
                $meta['og:description'] = trim(str_replace(
                    array("\t", "\n"),
                    ' ',
                    substr(strip_tags($post[1]), 0, 140)
                )).'&hellip;';
            }

            $meta['og:title'] = htmlspecialchars($title[1]);
            $meta['og:site_name'] = htmlspecialchars($title[3]);
            $meta['og:type'] = 'website';

            foreach ($meta as $name => &$value) {
                $value = '<meta property="'.$name.'" content="'.$value.'">';
            }

            $buffer = str_replace('</head>', implode("\n", $meta) . "\n</head>", $buffer);
        }

        return $buffer;
    }
}
