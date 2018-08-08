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

    // Ranks.

    $(function ()
    {
        var ranks = [
            {
                posts: 0,
                title: 'alpha'
            },
            {
                posts: 10,
                title: 'beta'
            },
            {
                posts: 35,
                title: 'gamma'
            },
            {
                posts: 50,
                title: 'delta'
            },
            {
                posts: 75,
                title: 'epsilon'
            },
            {
                posts: 100,
                title: 'zêta'
            },
            {
                posts: 150,
                title: 'êta'
            },
            {
                posts: 250,
                title: 'thêta'
            },
            {
                posts: 300,
                title: 'iota'
            },
            {
                posts: 350,
                title: 'kappa'
            },
            {
                posts: 400,
                title: 'lambda'
            },
            {
                posts: 500,
                title: 'mu'
            },
            {
                posts: 600,
                title: 'nu'
            },
            {
                posts: 700,
                title: 'xi'
            },
            {
                posts: 800,
                title: 'omicron'
            },
            {
                posts: 900,
                title: 'pi'
            },
            {
                posts: 1000,
                title: 'rho'
            },
            {
                posts: 1100,
                title: 'sigma'
            },
            {
                posts: 1200,
                title: 'tau'
            },
            {
                posts: 1300,
                title: 'upsilon'
            },
            {
                posts: 1400,
                title: 'phi'
            },
            {
                posts: 1500,
                title: 'chi'
            },
            {
                posts: 1600,
                title: 'psi'
            },
            {
                posts: 1700,
                title: 'omega'
            },
            {
                posts: 1800,
                title: 'zero'
            },
            {
                posts: 1900,
                title: 'ichi'
            },
            {
                posts: 2000,
                title: 'ni'
            },
            {
                posts: 2100,
                title: 'san'
            },
            {
                posts: 2200,
                title: 'yon'
            },
            {
                posts: 2300,
                title: 'go'
            },
            {
                posts: 2400,
                title: 'roku'
            },
            {
                posts: 2500,
                title: 'nana'
            },
            {
                posts: 2600,
                title: 'hachi'
            },
            {
                posts: 2700,
                title: 'kyū'
            },
            {
                posts: 2800,
                title: 'Mau'
            },
            {
                posts: 2900,
                title: 'Octopus'
            },
            {
                posts: 3000,
                title: 'Mautwo'
            },
            {
                posts: 3100,
                title: 'Byakko'
            },
            {
                posts: 3200,
                title: 'Kasha'
            },
            {
                posts: 3300,
                title: 'Bakeneko'
            },
            {
                posts: 3400,
                title: 'Nue'
            },
            {
                posts: 3500,
                title: 'Shachihoko'
            },
            {
                posts: 3600,
                title: 'Isuteritosu'
            },
            {
                posts: 3700,
                title: 'Ogopogo'
            },
            {
                posts: 3800,
                title: 'Wyvern'
            },
            {
                posts: 3900,
                title: 'Serpion'
            },
            {
                posts: 4000,
                title: 'Kusanagi'
            },
            {
                posts: 4200,
                title: 'Leviathan'
            },
            {
                posts: 4400,
                title: 'Zalamander'
            },
            {
                posts: 4600,
                title: 'Gizamaluk'
            },
            {
                posts: 4800,
                title: 'Vyraal'
            },
            {
                posts: 5000,
                title: 'Abelisk'
            },
            {
                posts: 5250,
                title: 'Sugari no Ontachi'
            },
            {
                posts: 5500,
                title: 'Tiamat'
            },
            {
                posts: 5750,
                title: 'Shinryu'
            },
            {
                posts: 6000,
                title: 'Bahamut'
            },
            {
                posts: 6250,
                title: 'Kirin'
            },
            {
                posts: 6500,
                title: 'Masamune'
            },
            {
                posts: 6750,
                title: 'Ryū'
            },
            {
                posts: 7000,
                title: 'Chrysophylax'
            },
            {
                posts: 7500,
                title: 'Glaurung'
            },
            {
                posts: 8000,
                title: 'Icefyre'
            },
            {
                posts: 8500,
                title: 'Mizuchi'
            },
            {
                posts: 9000,
                title: 'Kuzuryū'
            },
            {
                posts: 9500,
                title: 'Azure'
            },
            {
                posts: 10000,
                title: 'Mizuchi'
            },
            {
                posts: 11000,
                title: 'Watatsumi'
            },
            {
                posts: 12000,
                title: 'Ryūjin'
            },
            {
                posts: 13000,
                title: 'Zennyo Ryūō'
            },
            {
                posts: 14000,
                title: 'Amaterasu'
            },
            {
                posts: 15000,
                title: 'Code Is Pottery'
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

})();
