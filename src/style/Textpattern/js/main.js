(function ()
{
    'use strict';

    requirejs.config({
        paths:
        {
            'jquery': '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min',
            'webfont' : '//ajax.googleapis.com/ajax/libs/webfont/1.4.10/webfont'
        },
        shim:
        {
            'placeholder': ['jquery']
        }
    });

    define('modernizr', function ()
    {
        return window.Modernizr;
    });

    // Placeholder polyfill.

    require(['jquery'], function ($)
    {
        var placeholder = $('textarea[placeholder], input[placeholder]');

        if (placeholder.length)
        {
            require(['placeholder'], function ()
            {
                placeholder.placeholder();
            });
        }
    });

    // Search widget.

    require(['jquery'], function ($)
    {
        var topic = $('#page-viewtopic .crumbs li:last-child a').eq(0).text();

        if (!topic.length)
        {
            return;
        }

        $('.search-form input[name=q]').val(topic+': ');
    });

    // Textile help.

    require(['jquery'], function ($)
    {
        $('.bblinks').after('<p class="textile-help-links">Formatting: <a target="_blank" href="http://textpattern.com/textile-reference-manual">Textile</a></p>').remove();
    });

    // Syntax highlighting.

    require(['jquery'], function ($)
    {
        var code = $('pre code');

        if (code.length)
        {
            code.parent().addClass('prettyprint');

            require(['prettify'], function ()
            {
                prettyPrint();
            });
        }
    });

    // Hack-fix for the iOS orientationchange zoom bug (NOTE: fixed in iOS 6).

    require(['jquery', 'modernizr'], function ($, Modernizr)
    {
        if (Modernizr.touch)
        {
            var meta = $('meta[name=viewport]'), scales = [1, 1], fix = function ()
            {
                meta.attr('content', 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1]);
            };

            fix();
            scales = [0.25, 1.6];
            $(document).one('gesturestart', fix);
        }
    });

    // Test for SVG support via Modernizr, if yes then replace PNGs with SVGs.

    require(['jquery', 'modernizr'], function ($, Modernizr)
    {
        if (Modernizr.svg)
        {
            $('img.svg').attr('src', function ()
            {
                return $(this).attr('src').replace('.png', '.svg');
            });
        }
    });

    // Responsive navigation.

    require(['responsivenav'], function ()
    {
        responsiveNav('.nav-collapse', {
            animate: true,
            transition: 400,
            label: 'Menu',
            insert: 'before',
            customToggle: '',
            openPos: 'relative',
            jsClass: 'js'
        });
    });

    // Fonts.

    require(['webfont'], function ()
    {
        WebFont.load({
            google:
            {
                families: ['PT+Serif:400,700,400italic,700italic:latin', 'Cousine::latin']
            }
        });
    });

    // Twitter widgets.

    require(['jquery'], function ($)
    {
        var statusRegex = /^https?:\/\/twitter\.com\/(#!\/)?[a-z0-9]+\/status(es)?\/[0-9]+$/i, statusLinks = $('.postmsg > p > a').filter(function ()
        {
            var $this = $(this);
            return statusRegex.test($this.attr('href')) && $this.parent().text() === $this.text();
        });

        if (statusLinks.length)
        {
            statusLinks.html('').parent().wrap('<blockquote class="twitter-tweet"></blockquote>');
        }

        if ($('.twitter-share-button').length || statusLinks.length)
        {
            require(['//platform.twitter.com/widgets.js']);
        }
    });

    // Embed Gists.

    require(['jquery'], function ($)
    {
        var gistRegex = /^https?:\/\/gist\.github\.com\/[a-z0-9]+\/[0-9]+$/i;

        $('.postmsg > p > a').each(function ()
        {
            var $this = $(this);

            if (gistRegex.test($this.attr('href')) && $this.parent().text() === $this.text())
            {
                $this.parent().after($('<script></script>').attr('src', $this.attr('href'))).remove();
            }
        });
    });

    // Analytics.

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-191562-28']);
    _gaq.push(['_setDomainName', 'none']);
    _gaq.push(['_gat._anonymizeIp']);
    _gaq.push(['_trackPageview']);
    require(['//www.google-analytics.com/ga.js']);

})();
