(function ()
{
    'use strict';

    requirejs.config({
        paths:
        {
            'jquery': '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min',
            'webfont': '//ajax.googleapis.com/ajax/libs/webfont/1.4.10/webfont'
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

        $('#search-query').val(topic+': ');
    });

    // Quoting.

    require(['jquery'], function ($)
    {
        var field = $('#quickpostform textarea[name=req_message]'), button;

        if (!field.length)
        {
            return;
        }

        $('.postlink a').eq(0).attr('href', '#quickpostform');

        button = $('<a href="#quickpostform">Quote</a>').on('click', function ()
        {
            var $this = $(this),
                post = $this.parents('.blockpost').eq(0),
                name = post.find('.postleft dl dt').eq(0).text(),
                message = post.find('.postmsg').eq(0).clone().find('.postedit, table').remove().end(),
                link = post.find('h2 a').eq(0).attr('href'),
                value = $.trim(field.val());

            // Remove quotes.

            message.find('blockquote').each(function ()
            {
                var bq = $(this), prev = bq.prev('p');

                if (prev.length && prev.find('strong').text().indexOf(' wrote:') !== -1)
                {
                    prev.remove();
                }

                bq.remove();
            });

            // Compress code blocks to a single line.

            message.find('pre').each(function ()
            {
                $(this).after('<p>@' + $.trim($('<div />').text($(this).html()).html().split('\n').slice(0, 1).join('')) + '...@</p>').remove();
            });

            // Links.

            message.find('a').each(function ()
            {
                $(this).prepend('"').append('":' + $('<div />').text($(this).attr('href')).html());
            });

            // Images.

            message.find('img').each(function ()
            {
                $(this).after('!' + $('<div />').text($(this).attr('src')).html() + '!').remove();
            });

            // Spans.

            message.find('strong').prepend('*').append('*');
            message.find('b').prepend('**').append('**');
            message.find('cite').prepend('??').append('??');
            message.find('em').prepend('_').append('_');
            message.find('i').prepend('__').append('__');
            message.find('del').prepend('-').append('-');
            message.find('ins').prepend('+').append('+');
            message.find('sub').prepend('~').append('~');
            message.find('sup').prepend('^').append('^');

            // Lists.

            $.each({'ol' : '#', 'ul' : '*'}, function (type, marker)
            {
                message.children(type).each(function ()
                {
                    var items = [], list = $(this);

                    list.find('li').each(function ()
                    {
                        var bullet = '';

                        for (var i = 0; i < $(this).parents(list).length / 2; i++)
                        {
                            bullet += marker;
                        }

                        items.push(bullet + ' ' + $.trim($(this).clone().children(type).remove().end().html()));
                    });

                    list.after('<p>' + items.join('\n') + '</p>').remove();
                });
            });

            message = $.trim(message.find('p').eq(0).text());

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
                title : 'psi'
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
                posts : 3005,
                title : 'Octopus'
            },
            {
                posts : 3006,
                title : '0ctocat'
            },
            {
                posts : 3077,
                title : 'Mautwo'
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
                posts : 4000,
                title : 'Isuteritosu'
            },
            {
                posts : 4200,
                title : 'Ogopogo'
            },
            {
                posts : 4300,
                title : 'Wyvern'
            },
            {
                posts : 4400,
                title : 'Serpion'
            },
            {
                posts : 4444,
                title : 'Kusanagi'
            },
            {
                posts : 4600,
                title : 'Leviathan'
            },
            {
                posts : 4700,
                title : 'Zalamander'
            },
            {
                posts : 4800,
                title : 'Gizamaluk'
            },
            {
                posts : 4900,
                title : 'Vyraal'
            },
            {
                posts : 5000,
                title : 'Abelisk'
            },
            {
                posts : 5555,
                title : 'Sugari no Ontachi'
            },
            {
                posts : 5600,
                title : 'Tiamat'
            },
            {
                posts : 5800,
                title : 'Shinryu'
            },
            {
                posts : 6000,
                title : 'Bahamut'
            },
            {
                posts : 6500,
                title : 'Kirin'
            },
            {
                posts : 6666,
                title : 'Masamune'
            },
            {
                posts : 6800,
                title : 'Ryū'
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
                posts : 8500,
                title : 'Mizuchi'
            },
            {
                posts : 9000,
                title : 'Kuzuryū'
            },
            {
                posts : 9500,
                title : 'Azure'
            },
            {
                posts : 10000,
                title : 'Mizuchi'
            },
            {
                posts : 10500,
                title : 'Watatsumi'
            },
            {
                posts : 11000,
                title : 'Ryūjin'
            },
            {
                posts : 11500,
                title : 'Zennyo Ryūō'
            },
            {
                posts : 12000,
                title : 'Amaterasu'
            },
            {
                posts : 30246,
                title : 'Code Is Pottery'
            },
            {
                posts : 36625,
                title : 'Sandal I\'d Own'
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
        var code = $('.prettyprint');

        if (code.length)
        {
            code.filter('.language-txp').addClass('language-html').removeClass('language-txp');

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
                families: ['PT+Serif:400,700,400italic,700italic:latin']
            }
        });
    });

    // Share and social embed widgets.

    require(['jquery'], function ($)
    {
        var permlink, buttons, text, title = $('#page-viewtopic .crumbs li:last-child a').eq(0), gistStyle = false;

        if (title.length)
        {
            permlink = 'http://' + window.location.hostname + '/' + title.attr('href');
            text = title.text();

            buttons = $('<p class="share-buttons" />')
                .append($('<iframe class="fb-like" scrolling="no" frameborder="0" allowTransparency="true"></iframe>').attr('src', '//www.facebook.com/plugins/like.php?href='+encodeURIComponent(permlink)+'&width=90&height=21&colorscheme=light&layout=button_count&action=like&show_faces=false&send=false&appId=581964255172661'))
                .append($('<span class="g-plus" data-action="share" data-height="20" data-annotation="bubble" />').attr('data-href', permlink))
                .append($('<a class="twitter-share-button" />').attr('data-text', text).attr('data-url', permlink));

            $('#page-viewtopic .crumbs').eq(0).after(buttons);

            require(['https://apis.google.com/js/plusone.js']);
        }

        // Embed widgets; turns plain links to tweet and gist widgets.

        var tweetRegex = /^https?:\/\/twitter\.com\/(#!\/)?[a-z0-9]+\/status(es)?\/[0-9]+$/i,
            gistRegex = /^https?:\/\/gist\.github\.com\/[a-z0-9]+\/[0-9]+$/i,
            youtubeRegex = /^https?:\/\/(?:www\.)?(?:youtube\.com\/watch(?:\/|\?v=)|youtu\.be\/)([a-z0-9\-\_]+)$/i,
            vimeoRegex = /^https?:\/\/(?:www\.)?vimeo\.com\/[0-9]+$/i;

        $('.postmsg > p > a').each(function ()
        {
            var $this = $(this), href = $this.attr('href'), matches;

            if ($this.parent().text() !== $this.text())
            {
                return;
            }

            if (gistRegex.test(href))
            {
                $.ajax(href + '.json', {dataType: 'jsonp'})
                    .done(function (data)
                    {
                        if (data && data.div)
                        {
                            if (gistStyle === false && data.stylesheet)
                            {
                                $('head').append($('<link rel="stylesheet" />').attr('href', 'https://gist.github.com' + data.stylesheet));
                                gistStyle = true;
                            }

                            $this.parent().after($(data.div).removeAttr('id')).remove();
                        }
                    });

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
                $this.parent().after(
                    $('<div class="embed-video embed-youtube" />').html(
                        $('<iframe frameborder="0" allowfullscreen></iframe>').attr('src', '//www.youtube-nocookie.com/embed/' + matches[1])
                    )
                ).remove();

                return;
            }

            matches = href.match(vimeoRegex);

            if (matches)
            {
                $this.parent().after(
                    $('<div class="class="embed-video embed-vimeo" />').html(
                        $('<iframe frameborder="0" allowfullscreen></iframe>').attr('src', '//player.vimeo.com/video/' + matches[1])
                    )
                ).remove();

                return;
            }
        });

        if (title.length || $('.twitter-tweet').length)
        {
            require(['//platform.twitter.com/widgets.js']);
        }
    });

    // Author names on new line.

    require(['jquery'], function ($)
    {
        $('<br>').insertBefore('.byuser');
    });

    // Analytics.

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-191562-28']);
    _gaq.push(['_setDomainName', 'none']);
    _gaq.push(['_gat._anonymizeIp']);
    _gaq.push(['_trackPageview']);
    require(['//www.google-analytics.com/ga.js']);

    // Ads.

    require(['jquery'], function ($)
    {
        if ($('.bsarocks').length)
        {
            require(['//s3.buysellads.com/ac/bsa.js']);
        }
    });

})();
