<!DOCTYPE html>
<html lang="<pun_language>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style/Textpattern/css/style.@@timestamp.css">
    <link rel="dns-prefetch" href="https://code.jquery.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffda44">
    <meta name="application-name" content="Textpattern Forum">
    <link rel="icon" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="favicon-16x16.png">
    <pun_head>
    <link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;order=posted&amp;type=rss" title="RSS new topics feed">
</head>
<body id="page-help" itemscope itemtype="https://schema.org/WebPage">
    <meta itemprop="accessibilityControl" content="fullKeyboardControl">
    <meta itemprop="accessibilityControl" content="fullMouseControl">
    <meta itemprop="accessibilityHazard" content="noFlashingHazard">
    <meta itemprop="accessibilityHazard" content="noMotionSimulationHazard">
    <meta itemprop="accessibilityHazard" content="noSoundHazard">
    <meta itemprop="accessibilityAPI" content="ARIA">
    <div class="wrapper">
        <header class="site-header" itemscope itemtype="https://schema.org/Organization">
            <h1 class="masthead" itemprop="name"><a rel="home" itemprop="url" href="https://textpattern.com/" title="Go to the Textpattern homepage">Textpattern CMS</a></h1>
            <meta itemprop="logo" content="https://textpattern.io/assets/img/branding/textpattern/textpattern.png">
            <div itemscope itemtype="https://schema.org/WebSite">
                <meta itemprop="url" content="https://forum.textpattern.io/">
                <form class="search-form" role="search" method="get" action="https://forum.textpattern.io/search.php" itemprop="potentialAction" itemscope itemtype="https://schema.org/SearchAction">
                    <meta itemprop="target" content="https://forum.textpattern.io/search.php?action=search&show_as=posts&sort_dir=DESC&keywords={keywords}">
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="show_as" value="posts">
                    <input type="hidden" name="sort_dir" value="DESC">
                    <input name="keywords" type="search" size="32" placeholder="Search forum…" itemprop="query-input">
                </form>
            </div>
        </header>
        <div class="nav-container">
            <nav class="site-navigation" aria-label="Site navigation" itemscope itemtype="https://schema.org/SiteNavigationElement">
                <ul>
                    <li><a itemprop="url" href="https://textpattern.com/start">Get started</a></li>
                    <li><a itemprop="url" href="https://docs.textpattern.io/">Documentation</a></li>
                    <li><a itemprop="url" href="https://themes.textpattern.com/">Themes</a></li>
                    <li><a itemprop="url" href="http://textpattern.org/">Plugins</a></li>
                    <li class="active"><a itemprop="url" href="./">Forum</a></li>
                    <li><a itemprop="url" href="https://textpattern.com/weblog">Blog</a></li>
                    <li><a itemprop="url" href="https://textpattern.com/about">About</a></li>
                </ul>
            </nav>
        </div>
        <main aria-label="Main content">
            <div class="container">
                <h1 class="accessibility">Textpattern CMS support forum</h1>
                <pun_navlinks>
                <pun_status>
                <pun_announcement>
                <div class="layout-3col-2span">
                    <h2>Forum help</h2>
                    <h3 id="forum-help-textile-formatting">Formatting</h3>
                    <p>The forum uses <a href="https://textpattern.com/textile-reference-manual">Textile markup language</a> for posts and signatures. The forum’s Textile has few additional features, including media embedding support and syntax highlighting. If you’re not already familiar with Markdown, take a quick look at <a href="https://textpattern.com/textile-reference-manual">Textile reference</a>. The few most useful Textile tags you can use to format your posts include:</p>
                    <ul>
                        <li><code>!https://textpattern.com/hi.png!</code> → <img src="https://textpattern.com/hi.png" alt=""></li>
                        <li><code>"link":https://textpattern.com</code> → <a href="https://textpattern.com">link</a></li>
                        <li><code>@inline code@</code> → <code>inline code</code></li>
                        <li><code>*strong*</code> → <strong>strong</strong></li>
                        <li><code>_emphasis_</code> → <em>emphasis</em></li>
                        <li><code>-strikethrough-</code> → <del>strikethrough</del></li>
                        <li><code>+underline+</code> → <ins>underline</ins></li>
                    </ul>
                    <h5>Displaying code blocks</h5>
                    <p>You can use the Textile <code>bc.</code> tag to add code snippets to your forum posts.</p>
                    <pre class="prism"><code class="language-txp">bc. &lt;txp:permlink&gt;
    &lt;txp:title /&gt;
&lt;/txp:permlink&gt;</code></pre>
                    <p>The forum also supports syntax highlighting. Add an optional language identifier to your code block after the <code>bc.</code> tag then start your code on a new line, and it will get highlighted. For example:</p>
                    <pre class="prism"><code class="language-html">bc. html
&lt;h1 id="main-heading"&gt;Hello World!&lt;/h1&gt;</code></pre>
                    <p>Supported language identifiers include: <code>apacheconf</code>, <code>clike</code>, <code>coffeescript</code>, <code>css</code>, <code>git</code>, <code>haml</code>, <code>javascript</code> (or <code>js</code>), <code>json</code>, <code>less</code>, <code>markup</code> (or <code>html</code>), <code>markdown</code>, <code>nginx</code>, <code>perl</code>, <code>php</code>, <code>ruby</code>, <code>sass</code>, <code>scss</code>, <code>sql</code>, <code>stylus</code>, <code>textile</code>, <code>txp</code> and <code>yaml</code>.</p>
                    <h5>Embedding media</h5>
                    <p>You can embed media to your posts from third party services to linking to the resource using normal Textile link syntax.</p>
                    <pre class="prism"><code class="language-textile">"$":http://www.youtube.com/watch?v=BKQ6nINAeq8
"$":https://twitter.com/textpattern/status/386111138699935744
"$":https://gist.github.com/gocom/5431041</code></pre>
                    <p>A link to a supported service becomes an embedded object if it is in its own paragraph. Links within text will stay as normal text links to avoid disturbing reading and text flow. We currently support embedding content from <a href="http://youtube.com/">YouTube</a>, <a href="https://twitter.com">Twitter</a> and <a href="https://gist.github.com/">GitHub Gist</a>.</p>
                    <h3 id="forum-help-undeliverable-mail">Why can’t I create a forum account or receive forum emails?</h3>
                    <p>The most common cause of problems when creating forum accounts are email spam filters and blacklists. You must supply a working email address in order to create an account; when you sign-up, the forum software will send you the password you may use to login, to verify that your email address is correct and working properly. Some email servers refuse to accept these emails or incorrectly intercept them as spam. This is particularly common with free web mail services.</p>
                    <p>The following email providers seem to have problems accepting email from this forum, for one reason or another:</p>
                    <ul>
                        <li>bk.ru</li>
                        <li>comcast.net</li>
                        <li>hotmail.com</li>
                        <li>inbox.ru</li>
                        <li>list.ru</li>
                        <li>mail.ru</li>
                    </ul>
                    <p>It is therefore recommended that you use an address from another provider.</p>

                    <h3 id="forum-help-conduct">How should I ask for help on the forum?</h3>
                    <p>The Textpattern CMS Support Forum is for discussion and support of all things Textpattern. Please bear in mind that it is a community forum—when you ask for assistance, you are asking volunteers and fellow users, not paid technical support staff. The usual common-sense conventions of etiquette and politeness apply.</p>
                    <p>A few rules of thumb specific to the Textpattern forum:</p>
                    <ol>
                        <li>Please make an effort to check the <a href="https://textpattern.com/support" rel="external">Textpattern CMS User Documentation</a> before posting, and check for duplicate threads on the forum.</li>
                        <li>Try to choose the most appropriate forum for your question. ‘Troubleshooting’ is for when you’ve tried something and it doesn’t work as expected. ‘How do I…?’ is self explanatory. Questions about plugins should go to the ‘Plugins’ forum, not ‘How do I…?’ or ‘Troubleshooting’.</li>
                        <li>If you’re asking for help with a problem or error, please describe the problem clearly and unambiguously. If there is an error message, please include an exact copy of the message in your post. If something doesn’t work as expected, describe what you’re expecting to see, and the actual behaviour. A link to the page in question is usually helpful, and sometimes a screenshot might be appropriate. Descriptions like “doesn’t work” or “it’s broken” are too vague, and unlikely to receive an answer.</li>
                        <li>Most suspected bugs aren’t really bugs, but problems caused by external factors. Unless you’ve confirmed the problem with a fresh install, or can identify the problem in the PHP source, start with a ‘Troubleshooting’ post rather than a bug report.</li>
                        <li>Troubleshooting questions will usually be answered quicker if you include a copy of your diagnostics in your post (Textpattern → Admin → Diagnostics). Don’t post the ‘High’ detail version unless someone asks.</li>
                        <li>Questions about templates and appearance will usually benefit from including a minimal copy of the code in question. Edit out as much irrelevant code as you can—a short snippet of code is much easier for people to quickly diagnose than an entire page.</li>
                        <li>If you haven’t received an answer in a day or so, there’s probably a good reason: an ambiguous or rambling question, or not enough information. Don’t ‘bump’ your thread with a single-word post, it’s more likely to annoy people than attract an answer—post some more information instead.</li>
                        <li>Don’t post a troubleshooting question as a reply to an existing thread unless your problem is exactly the same as the one described at the beginning of the thread.</li>
                        <li>If you’re requesting a feature, <a href="https://forum.textpattern.io/viewtopic.php?id=10325">this article</a> lists some of the things you can do to increase its chances.</li>
                    </ol>
                </div>
                <pun_footer>
            </div>
        </main>
    </div>
    <div class="wrapper-footer">
        <aside class="container complementary-content">
            <div class="layout-container">
                <div class="layout-3col-2span">
                    <div class="layout-container">
                        <section class="layout-2col" itemscope itemtype="https://schema.org/Organization">
                            <h4>Social channels</h4>
                            <meta itemprop="name" content="Textpattern CMS">
                            <meta itemprop="sameAs" content="https://en.wikipedia.org/wiki/Textpattern">
                            <ul class="social-channels">
                                <li><a class="twitter" rel="external" itemprop="sameAs" href="https://twitter.com/textpattern" title="Follow us on Twitter">Twitter</a></li>
                                <li><a class="googleplus" rel="external" itemprop="sameAs" href="https://plus.google.com/+textpattern" title="Follow us on Google+">Google+</a></li>
                                <li><a class="github" rel="external" itemprop="sameAs" href="https://github.com/textpattern" title="Follow us on GitHub">GitHub</a></li>
                            </ul>
                            <h4>Donate</h4>
                            <p>Your kind donations help us keep Textpattern CMS development alive!</p>
                            <p><a class="button button-primary" href="https://textpattern.com/donate"><span class="ui-icon ui-icon-heart"></span> Donate…</a></p>
                        </section>
                        <section class="layout-2col community-details">
                            <h4>External links</h4>
                            <ul class="community-links">
                                <li><a rel="external" href="https://github.com/textpattern">Textpattern on GitHub</a></li>
                                <li><a rel="external" href="https://github.com/textile">Textile on GitHub</a></li>
                                <li><a rel="external" href="http://textpattern.tips/">Textpattern Tips</a></li>
                                <li><a rel="external" href="https://github.com/drmonkeyninja/awesome-textpattern">Awesome Textpattern</a></li>
                                <li><a rel="external" href="http://txpmag.com/">TXP Magazine</a></li>
                                <li><a rel="external" href="http://textpattern.ru/">Textpattern.ru</a></li>
                            </ul>
                        </section>
                    </div>
                </div>
                <section class="layout-3col host-details">
                    <h4 class="accessibility">Host details</h4>
                    <p><span class="host-network">Textpattern Network</span></p>
                    <p>
                        <small>
                            Kindly hosted by<br>
                            <a class="joyent" rel="external" href="https://www.joyent.com/" title="Go to the Joyent website">Joyent</a>
                        </small>
                    </p>
                </section>
            </div>
        </aside>
        <footer class="site-footer">
            <p class="legal">
                Copyright 2004–@@year The Textpattern Development Team.
                <a href="https://textpattern.com/contact">Contact us</a>.
                <a href="https://textpattern.com/privacy">Privacy</a>.
                <a href="humans.txt">Colophon</a>.
                Textpattern is both free and open source. <a href="https://textpattern.com/license">GPLv2 license</a>.
            </p>
        </footer>
    </div>
    <script async data-main="style/Textpattern/js/main.@@timestamp.js" src="style/Textpattern/js/require.@@timestamp.js"></script>
    <noscript>JavaScript is currently disabled in your browser - activate it for the best experience.</noscript>
</body>
</html>
