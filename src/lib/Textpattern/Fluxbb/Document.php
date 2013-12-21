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
     * Constructor.
     */

    public function __construct()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'viewtopic.php') !== false) {
            ob_start(array($this, 'buffer'));
        }
    }

    /**
     * Buffer handler.
     */

    public function buffer($buffer)
    {
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
