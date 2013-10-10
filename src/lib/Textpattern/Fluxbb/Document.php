<?php

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
        if (strpos($_SERVER['REQUEST_URI'], 'viewtopic.php') !== false)
        {
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

        if (preg_match('#<title>(.*?) \(\w+ [0-9]+\) / (.*?) / (.*?)</title>#', $buffer, $title))
        {
            if (preg_match('#<dd class="postavatar"><img src="([a-z0-9\./\:]+)\?m=[0-9]{0,}" width="60" height="60" alt="" /></dd>#', $buffer, $avatar))
            {
                $meta['og:image'] = $avatar[1];
            }

            if (preg_match('#<div class="postmsg">(.*?)</div>#s', $buffer, $post))
            {
                $meta['og:description'] = trim(str_replace(array("\t", "\n"), ' ', substr(strip_tags($post[1]), 0, 140))).'&hellip;';
            }

            $meta['og:title'] = htmlspecialchars($title[1]);
            $meta['og:site_name'] = htmlspecialchars($title[3]);
            $meta['og:type'] = 'website';

            foreach ($meta as $name => &$value)
            {
                $value = '<meta property="'.$name.'" content="'.$value.'">';
            }

            $buffer = str_replace('</head>', implode("\n", $meta) . "\n</head>", $buffer);
        }

        return $buffer;
    }
}
