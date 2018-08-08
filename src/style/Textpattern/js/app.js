//import {$,jQuery} from 'jquery';

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

})();
