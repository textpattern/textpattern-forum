var $ = require('jquery/dist/jquery.slim');

import Prism from 'prismjs';
require('prismjs/components/prism-markup-templating');
require('prismjs/components/prism-apacheconf');
require('prismjs/components/prism-bash');
require('prismjs/components/prism-coffeescript');
require('prismjs/components/prism-git');
require('prismjs/components/prism-haml');
require('prismjs/components/prism-json');
require('prismjs/components/prism-less');
require('prismjs/components/prism-markdown');
require('prismjs/components/prism-nginx');
require('prismjs/components/prism-perl');
require('prismjs/components/prism-php');
require('prismjs/components/prism-ruby');
require('prismjs/components/prism-sass');
require('prismjs/components/prism-scss');
require('prismjs/components/prism-sql');
require('prismjs/components/prism-stylus');
require('prismjs/components/prism-textile');
require('prismjs/components/prism-yaml');

(function ()
{
    'use strict';

    // If JavaScript enabled, add a class to `<html>` tag.

    document.documentElement.className = 'js';

    // Load objects as variables.

    var code = document.querySelectorAll('pre code'),
        navmenu = document.getElementById('site-navigation');

    // Syntax highlighting, via 'Prism'.
    // Applies syntax highlighting to `code` HTML elements.
    // More info - http://prismjs.com.

    if (code.length) {
        var elems = document.querySelectorAll('.language-txp');

        [].forEach.call(elems, function(el) {
            el.classList.add('language-html');
            el.classList.remove('language-txp');
        });

        Prism.highlightAll();
    }

    // Responsive navigation menu.

    if (navmenu) {
        var navtoggle = document.getElementById('site-navigation-toggle');

        navtoggle.addEventListener('click', function(e)
        {
            e.preventDefault();
            navtoggle.classList.toggle('site-navigation-toggle-active');
            navmenu.classList.toggle('site-navigation-open');
        });
    }

    // Quoting.

    $(function ()
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

            field.val(value).focus();

            if ($.type(field[0].setSelectionRange) !== 'undefined') {
                field[0].setSelectionRange(value.length, value.length);
            }
        });

        $('.postfootright ul').append($('<li class="textile-quote-post" />').html($('<span />').html(button)));
    });

    // Embed widgets.

    $(function ()
    {
        var loadTwitter = false,
            tweetRegex = /^https?:\/\/twitter\.com\/(#!\/)?[a-z0-9]+\/status(es)?\/[0-9]+$/i,
            youtubeRegex = /^https?:\/\/(?:www\.)?(?:youtube\.com\/watch(?:\/|\?v=)|youtu\.be\/)([a-z0-9\-\_]+)$/i;

        $('.postmsg > p > a').each(function ()
        {
            var $this = $(this), href = $this.attr('href'), matches;

            if ($this.parent().text() !== $this.text()) {
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
            $('head').append('<meta name="twitter:widgets:csp" content="on">');

            window.twttr = (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                t = window.twttr || {};

                if (d.getElementById(id)){
                    return t;
                }

                js = d.createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);
                t._e = [];
                t.ready = function(f) {
                t._e.push(f);
            };

            return t;
            }(document, "script", "twitter-wjs"));

        }
    });

})();
