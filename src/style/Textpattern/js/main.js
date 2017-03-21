(function ()
{
    'use strict';

    document.documentElement.className = 'js';

    requirejs.config({
        paths: {
            'jquery': 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min',
            'recaptcha': 'https://www.google.com/recaptcha/api/js/recaptcha_ajax'
        }
    });

    // Detect whether user enabled 'Do No Track' in their browser, and honour it.

    define('track', function ()
    {
        return {
            allow : navigator.doNotTrack !== 'yes' && navigator.doNotTrack !== '1' && window.doNotTrack !== 'yes' && window.doNotTrack !== '1'
        };
    });

    // Quoting.

    require(['jquery'], function ($)
    {
        var field = $('#quickpostform textarea[name=req_message]'), button;

        if (!field.length) {
            return;
        }

        $('.postlink a').eq(0).attr('href', '#quickpostform');

        button = $('<a href="#quickpostform">Quote</a>').on('click', function ()
        {
            var $this = $(this),
                tagStart,
                tagEnd,
                paragraph,
                post = $this.parents('.blockpost').eq(0),
                name = post.find('.postleft dl dt').eq(0).text(),
                message = post.find('.postmsg').eq(0).clone().find('.postedit, table').remove().end(),
                link = post.find('h2 a').eq(0).attr('href'),
                postId = post.attr('id').substr(1),
                value = $.trim(field.val());

            // Remove quotes.

            message.find('blockquote').each(function ()
            {
                var bq = $(this), prev = bq.prev('h6, p');

                if (prev.length && $.trim(prev.text()).substr(-1) === ':') {
                    prev.remove();
                }

                bq.remove();
            });

            // Compress code blocks to a single line.

            message.find('pre').each(function ()
            {
                $(this).after('<p>@' + $.trim($('<div />').text($(this).text()).html().split('\n').slice(0, 1).join('')) + '...@</p>').remove();
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
            message.find('code').prepend('@').append('@');

            // Headings.

            message.find('h1, h2, h3, h4, h5, h6').prepend('*').append('*');

            // Lists. Textile will wrap the lists in paragraphs as of 3.5.x.

            $.each({'ol' : '#', 'ul' : '*'}, function (type, marker)
            {
                message.children(type).each(function ()
                {
                    var items = [], list = $(this);

                    list.find('li').each(function ()
                    {
                        var bullet = '';

                        for (var i = 0; i < $(this).parents(list).length / 2; i++) {
                            bullet += marker;
                        }

                        items.push(bullet + ' ' + $.trim($(this).clone().children(type).remove().end().html()));
                    });

                    list.after('<p>' + items.join('\n') + '</p>').remove();
                });
            });

            paragraph = message.find('h1, h2, h3, h4, h5, h6, p');

            if (paragraph.length > 1) {
                tagStart = '\n\nbq.. ';
                tagEnd = '\n\np. ';
            } else {
                tagStart = '\n\nbq. ';
                tagEnd = '\n\n';
            }

            message = $.trim(paragraph.append('\n\n').text());

            if (value) {
                value = $.trim(value).replace(/\r?\n\r?\np\.$/, '') + '\n\n';
            }

            name = $.trim(name).replace(/==/, '');

            if (message) {
                value = value + 'h6. ==' + name + '== wrote "#' + postId + '":./' + link + ':' + tagStart + message + tagEnd;
            } else {
                value = value + 'h6. In reply to ==' + name + '== "#' + postId + '":./' + link + ':\n\n';
            }

            field.val(value).trigger('autosize.resize').focus();

            if ($.type(field[0].setSelectionRange) !== 'undefined') {
                field[0].setSelectionRange(value.length, value.length);
            }
        });

        $('.postfootright ul').append($('<li class="textile-quote-post" />').html($('<span />').html(button)));
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

            for (i = 0; i < ranks.length; i++) {
                if (ranks[i].posts > posts) {
                    break;
                }

                rank = ranks[i];
            }

            if (rank) {
                $this.find('.usertitle').after('<dd class="userrank"><span title="Ranked level '+ (i - 1) +'">'+rank.title+'</span></dd>');
            }
        });
    });

    // Syntax highlighting, via 'Prism'.
    // Applies syntax highlighting to `code` HTML elements.
    // More info - http://prismjs.com.

    require(['jquery'], function ($)
    {
        var code = $('.prism');

        if (code.length) {
            code.filter('.language-txp').addClass('language-html').removeClass('language-txp');

            require(['prism.@@timestamp'], function ()
            {
                Prism.highlightAll();
            });
        }
    });

    // Focus to the username/password on the login page.

    require(['jquery'], function ($)
    {
        if (!$('#page-login').length) {
            return;
        }

        var user = $('#login [name=req_username]'), pass = $('#login [name=req_password]');

        if (user.val() === '' || pass.val() !== '') {
            user.focus();
        } else {
            pass.focus();
        }
    });

    // Forms.

    require(['jquery'], function ($)
    {
        $('input[type!=hidden][name^=req_], textarea[name^=req_]').prop('required', true);

        $('#qjump select').change(function ()
        {
            window.location = 'viewforum.php?id=' + $(this).val();
        });
    });

    // Responsive navigation menu, via 'Responsive Nav'.
    // More info - https://github.com/viljamis/responsive-nav.js.

    require(['responsivenav.@@timestamp'], function ()
    {
        responsiveNav('.site-navigation', {
            transition: 400,
            insert: 'before',
            navClass: 'site-navigation'
        });
    });

    // reCaptcha.

    require(['jquery'], function ($)
    {
        // jshint camelcase: false

        var widget = $('.recaptcha-widget');

        if (!widget.length) {
            return;
        }

        require(['recaptcha'], function ()
        {
            window.Recaptcha.create(
                widget.attr('data-recaptcha-key'),
                widget.get(0),
                {
                    theme: 'custom',
                    custom_theme_widget: 'recaptcha_widget'
                }
            );

            $('a.recaptcha-reload').on('click', function (e)
            {
                e.preventDefault();
                window.Recaptcha.reload();
            });

            $('a.recaptcha-switch-audio').on('click', function (e)
            {
                e.preventDefault();
                window.Recaptcha.switch_type('audio');
            });

            $('a.recaptcha-switch-image').on('click', function (e)
            {
                e.preventDefault();
                window.Recaptcha.switch_type('image');
            });

            $('a.recaptcha-show-help').on('click', function (e)
            {
                e.preventDefault();
                window.Recaptcha.showhelp();
            });
        });
    });

    // Embed widgets.

    require(['jquery'], function ($)
    {
        var gistStyle = false,
            loadTwitter = false,
            tweetRegex = /^https?:\/\/twitter\.com\/(#!\/)?[a-z0-9]+\/status(es)?\/[0-9]+$/i,
            gistRegex = /^https?:\/\/gist\.github\.com\/[a-z0-9]+\/[0-9]+$/i,
            youtubeRegex = /^https?:\/\/(?:www\.)?(?:youtube\.com\/watch(?:\/|\?v=)|youtu\.be\/)([a-z0-9\-\_]+)$/i;

        $('.postmsg > p > a').each(function ()
        {
            var $this = $(this), href = $this.attr('href'), matches;

            if ($this.parent().text() !== $this.text()) {
                return;
            }

            if (gistRegex.test(href)) {
                $.ajax(href + '.json', {dataType: 'jsonp'})
                    .done(function (data)
                    {
                        if (data && data.div) {
                            if (gistStyle === false && data.stylesheet) {
                                $('head').append($('<link rel="stylesheet" />').attr('href', 'https://gist.github.com' + data.stylesheet));
                                gistStyle = true;
                            }

                            $this.parent().after($(data.div).removeAttr('id')).hide();
                        }
                    });

                return;
            }

            if (tweetRegex.test(href)) {
                loadTwitter = true;

                $this.parent().after(
                    $('<blockquote class="twitter-tweet" data-dnt="true" />').html($this.parent().html())
                ).hide();

                return;
            }

            matches = href.match(youtubeRegex);

            if (matches) {
                $this.parent().after(
                    $('<div class="embed-video embed-youtube" />').html(
                        $('<iframe frameborder="0" allowfullscreen></iframe>').attr('src', 'https://www.youtube-nocookie.com/embed/' + matches[1])
                    )
                ).hide();

                return;
            }
        });

        if (loadTwitter) {
            $('head').append('<meta name="twitter:widgets:csp" content="on" />');
            require(['https://platform.twitter.com/widgets.js']);
        }
    });

    // Google Analytics

    require(['track'], function(track)
    {
        if (track.allow) {
            /* jshint ignore:start */
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            /* jshint ignore:end */
            ga('create', 'UA-191562-28', 'auto', {
                anonymizeIp: true
            });
            ga('send', 'pageview');
        }
    });

})();
