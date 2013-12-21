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

namespace Textpattern\Fluxbb\Textile;

use Netcarver\Textile\Parser as Textile;

/**
 * Forum flavoured Textile parser.
 */

class Parser extends Textile
{
    /**
     * {@inheritdoc}
     */

    public function __construct($doctype = 'html5')
    {
        parent::__construct($doctype);
    }

    /**
     * {@inheritdoc}
     */

    public function textileRestricted($text, $lite = 1, $noimage = 1, $rel = 'nofollow')
    {
        $text = parent::textileRestricted($text, $lite, $noimage, $rel);
        $text = $this->extraCodeLanguageHinting($text);
        return $text;
    }

    /**
     * Syntax highlighting, and language hinting.
     *
     * Adds 'language-n' class to bq. blocks. Language
     * can specified with the code blocks first line.
     * If the line matches a valid language, it will be
     * used as the class.
     *
     * @param  string $text
     * @return string
     */

    private function extraCodeLanguageHinting($text)
    {
        return preg_replace(
            '/<pre><code>(?:\/\/|#|;)?(?:\s+)?('.
            'apollo|bash|c|coffee|cs|clj|css|dart|go|hs|html|java|'.
            'js|json|lisp|lua|ml|n|perl|php|proto|python|ruby|rust|'.
            'scala|sh|sql|tex|text|txp|vb|vhdl|wiki|xml|xsl|xq|yaml'.
            ')(?:\n+)?/',
            '<pre class="prettyprint language-$1"><code>',
            $text
        );
    }
}
