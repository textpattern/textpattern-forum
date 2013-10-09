<?php

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
        return preg_replace('/<pre><code>(?:\/\/|#|;)?(?:\s+)?(apollo|bash|c|coffee|cs|clj|css|dart|go|hs|html|java|js|json|lisp|lua|ml|n|perl|php|proto|python|ruby|rust|scala|sh|sql|tex|text|vb|vhdl|wiki|xml|xsl|xq|yaml)(?:\n+)?/', '<pre class="language-$1"><code>', $text);
    }
}
