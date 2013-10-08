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

    // Quoting.

    require(['jquery'], function ($)
    {
        var field = $('#quickpostform textarea[name=req_message]'), button;

        if (!field.length)
        {
            return;
        }

        button = $('<a href="#quickpostform">Quote</a>').on('click', function ()
        {
            var $this = $(this), post = $this.parents('.blockpost').eq(0), name = post.find('.postleft dl dt').eq(0).text(), message = post.find('.postmsg').eq(0).clone().find('.postedit').remove().end(), link = post.find('h2 a').eq(0).attr('href'), value = $.trim(field.val());

            // Remove quotes.

            message.find('blockquote').prev('p').filter(function ()
            {
                return $(this).find('strong').length && $(this).text().indexOf(' wrote:') !== -1;
            }).remove();

            message.find('blockquote').remove();

            // Compress code blocks to a single line.

            message.find('pre').each(function ()
            {
                $(this).after('<p>@' + $.trim($('<div />').text($(this).html()).html().replace('\r', '').split('\n').slice(0, 1).join('')) + '...@</p>').remove();
            });

            message = $.trim(message.text()).replace('\r', '').split('\n\n').slice(0, 1).join('');

            if (value)
            {
                value = value + '\n\n';
            }

            value = value + '*' + name + ' wrote:*\n\nbq. ' + message + ' "[...]":./' + link + '\n\n';
            field.val(value).focus();

            if ($.type(field[0].setSelectionRange) !== 'undefined')
            {
                field[0].setSelectionRange(value.length, value.length);
            }
        });

        $('.postfootright ul').append($('<li class="textile-quote-post" />').html(button));
    });

    // Ranks.

    require(['jquery'], function ($)
    {
        var ranks =
        [
            {
                posts : 0,
                title : 'alpha'
            },
            {
                posts : 10,
                title : 'beta'
            },
            {
                posts : 35,
                title : 'gamma'
            },
            {
                posts : 50,
                title : 'delta'
            },
            {
                posts : 75,
                title : 'epsilon'
            },
            {
                posts : 100,
                title : 'zêta'
            },
            {
                posts : 150,
                title : 'êta'
            },
            {
                posts : 250,
                title : 'thêta'
            },
            {
                posts : 300,
                title : 'iota'
            },
            {
                posts : 350,
                title : 'kappa'
            },
            {
                posts : 400,
                title : 'lambda'
            },
            {
                posts : 500,
                title : 'mu'
            },
            {
                posts : 600,
                title : 'nu'
            },
            {
                posts : 700,
                title : 'xi'
            },
            {
                posts : 800,
                title : 'omicron'
            },
            {
                posts : 900,
                title : 'pi'
            },
            {
                posts : 1000,
                title : 'rho'
            },
            {
                posts : 1100,
                title : 'sigma'
            },
            {
                posts : 1200,
                title : 'tau'
            },
            {
                posts : 1300,
                title : 'upsilon'
            },
            {
                posts : 1400,
                title : 'phi'
            },
            {
                posts : 1500,
                title : 'chi'
            },
            {
                posts : 1600,
                title : 'phi'
            },
            {
                posts : 1700,
                title : 'omega'
            },
            {
                posts : 2000,
                title : 'zero'
            },
            {
                posts : 2100,
                title : 'ichi'
            },
            {
                posts : 2200,
                title : 'ni'
            },
            {
                posts : 2300,
                title : 'san'
            },
            {
                posts : 2400,
                title : 'yon'
            },
            {
                posts : 2500,
                title : 'go'
            },
            {
                posts : 2600,
                title : 'roku'
            },
            {
                posts : 2700,
                title : 'nana'
            },
            {
                posts : 2800,
                title : 'hachi'
            },
            {
                posts : 2900,
                title : 'kyū'
            },
            {
                posts : 3000,
                title : 'Mau'
            },
            {
                posts : 3100,
                title : 'Byakko'
            },
            {
                posts : 3200,
                title : 'Kasha'
            },
            {
                posts : 3300,
                title : 'Bakeneko'
            },
            {
                posts : 3400,
                title : 'Nue'
            },
            {
                posts : 3500,
                title : 'Shachihoko'
            },
            {
                posts : 4444,
                title : 'Kusanagi'
            },
            {
                posts : 5555,
                title : 'Sugari no Ontachi'
            },
            {
                posts : 6666,
                title : 'Masamune'
            },
            {
                posts : 7000,
                title : 'Chrysophylax'
            },
            {
                posts : 7500,
                title : 'Glaurung'
            },
            {
                posts : 8000,
                title : 'Icefyre'
            },
            {
                posts : 9000,
                title : 'Amaterasu'
            }
        ];

        $('#page-viewtopic .blockpost .postleft > dl').each(function ()
        {
            var i, rank, $this = $(this), posts = parseInt($this.find('dd span').filter(function ()
            {
                return $(this).text().indexOf('Posts: ') === 0;
            }).eq(0).text().replace('Posts: ', '').replace(',', ''), 10);

            for (i = 0; i < ranks.length; i++)
            {
                if (ranks[i].posts > posts)
                {
                    break;
                }

                rank = ranks[i];
            }

            if (rank)
            {
                $this.find('.usertitle').after('<dd class="userrank"><span title="Ranked level '+ (i - 1) +'">'+rank.title+'</span></dd>');
            }
        });
    });

    // Textile help.

    require(['jquery'], function ($)
    {
        $('.bblinks').after('<p class="textile-help-links">Formatting: <a target="_blank" href="http://textpattern.com/textile-reference-manual">Textile</a></p>').remove();
    });

    // Syntax highlighting.

    require(['jquery'], function ($)
    {
        var code = $('pre code'), languageRegex = /^(apollo|bash|c|coffee|cs|clj|css|dart|go|hs|html|java|js|json|lisp|lua|ml|n|perl|php|proto|python|ruby|rust|scala|sh|sql|tex|text|vb|vhdl|wiki|xml|xsl|xq|yaml)([\r\n|\r|\n]+)/;

        if (code.length)
        {
            code.parent().each(function ()
            {
                var $this = $(this), matches = $this.text().match(languageRegex);

                if (matches)
                {
                    $this.html($this.html().replace(matches[1] + matches[2], '')).attr('class', 'prettyprint language-' + matches[1]);
                }
                else
                {
                    $this.addClass('prettyprint');
                }
            });

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

    // Share and social embed widgets.

    require(['jquery'], function ($)
    {
        var permlink, buttons, text, title = $('#page-viewtopic .crumbs li:last-child a').eq(0);

        if (title.length)
        {
            permlink = encodeURIComponent(title.attr('href'));
            text = encodeURIComponent(title.text());

            buttons = $('<p class="share-buttons" />')
                .append($('<a class="facebook-share-button">Share on Facebook</a>').attr('href', 'https://www.facebook.com/sharer/sharer.php?u='+permlink))
                .append($('<a class="twitter-share-button">Tweet</a>').attr('data-text', text).attr('data-url', permlink).attr('href', 'https://twitter.com/share?url='+permlink+'&text='+text))
                .append($('<span class="g-plus" data-action="share" />').attr('data-href', permlink));

            $('#page-viewtopic .crumbs').eq(0).after(buttons);
            require(['https://apis.google.com/js/plusone.js']);
        }

        // Embed widgets; turns plain links to tweet and gist widgets.

        var tweetRegex = /^https?:\/\/twitter\.com\/(#!\/)?[a-z0-9]+\/status(es)?\/[0-9]+$/i;
        var gistRegex = /^https?:\/\/gist\.github\.com\/[a-z0-9]+\/[0-9]+$/i;
        var youtubeRegex = /^https?:\/\/(?:www\.)?(?:youtube\.com\/watch(?:\/|\?v=)|youtu\.be\/)([a-z0-9\-\_]+)$/i;

        $('.postmsg > p > a').each(function ()
        {
            var $this = $(this), href = $this.attr('href'), matches;

            if ($this.parent().text() !== $this.text())
            {
                return;
            }

            if (gistRegex.test(href))
            {
                $this.parent().after($('<script></script>').attr('src', href)).remove();
                return;
            }

            if (tweetRegex.test(href))
            {
                $this.html('').parent().wrap('<blockquote class="twitter-tweet"></blockquote>');
                return;
            }

            matches = href.match(youtubeRegex);

            if (matches)
            {
                $this.parent().after($('<iframe width="420" height="315" frameborder="0" allowfullscreen></iframe>').attr('src', '//www.youtube-nocookie.com/embed/' + matches[1])).remove();
            }
        });

        if (title.length || $('.twitter-tweet').length)
        {
            require(['//platform.twitter.com/widgets.js']);
        }
    });

    // Analytics.

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-191562-28']);
    _gaq.push(['_setDomainName', 'none']);
    _gaq.push(['_gat._anonymizeIp']);
    _gaq.push(['_trackPageview']);
    require(['//www.google-analytics.com/ga.js']);

})();
