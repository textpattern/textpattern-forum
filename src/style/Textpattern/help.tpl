<!DOCTYPE html>
<html lang="<pun_language>" dir="<pun_content_direction>" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="Textpattern Forum">
    <link rel="stylesheet" href="style/Textpattern/css/main.@@timestamp.css">
    <pun_head>
    <script src="style/Textpattern/js/modernizr.@@timestamp.js"></script>
</head>
<body id="page-help">
    <div class="wrapper">

<!-- Old IE browsers -->
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" rel="external">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

<!-- Header -->
        <header role="banner" itemscope itemtype="http://schema.org/Organization">
            <h1 itemprop="name" class="masthead"><a rel="home" itemprop="url" href="/" title="Go to the Textpattern homepage">Textpattern CMS</a></h1>

            <form id="search-form" role="search" action="http://www.google.com/cse">
                <a href="search.php">Advanced</a>
                <input type="search" name="q" size="32" id="search-query">
                <input type="submit" name="sa" value="Search">
                <input type="hidden" name="cx" value="013284010981475036739:4p3oc9ihitk">
                <input type="hidden" name="ie" value="UTF-8">
            </form>
        </header>

<!-- Navigation -->
        <div class="nav-container">
            <nav role="navigation" class="nav-collapse" aria-label="Site navigation">
                <ul>
                    <li><a href="#">Get started</a></li>
                    <li><a href="http://textpattern.net/">Documentation</a></li>
                    <li><a href="#">Themes</a></li>
                    <li><a href="http://textpattern.org/">Plugins</a></li>
                    <li><a href="http://forum.textpattern.com/">Forum</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">About</a></li>
                </ul>
            </nav>
        </div>

<!-- Primary Content -->
        <div class="container">
            <main role="main" aria-label="Main content">
                <h1>Forum help</h1>

                <h2>Why can’t I create a forum account or receive forum emails?</h2>
                <p>The most common cause of problems creating forum accounts are email spam filters and blacklists. You must supply a working email address in order to create an account: when you sign-up, the forum software will send you the password you may use to login, to verify that your email address is correct and working properly. Some email servers refuse to accept these emails, or incorrectly intercept them as spam. This is particularly common with free web mail services.</p>
                <p>The following email providers seem to have problems accepting email from the forum, for one reason or another:</p>
                <ul>
                    <li>bk.ru</li>
                    <li>comcast.net</li>
                    <li>hotmail.com</li>
                    <li>inbox.ru</li>
                    <li>list.ru</li>
                    <li>mail.ru</li>
                </ul>
                <p>It is therefore recommended that you use an address from another provider.</p>
                <h2>How should I ask for help on the forum?</h2>
                <p>The Textpattern CMY Support Forum is for discussion and support of all things Textpattern. Please bear in mind that it is a community forum—when you ask for assistance, you are asking volunteers and fellow users, not paid technical support staff. The usual common-sense conventions of etiquette and politeness apply.</p>
                <p>A few rules of thumb specific to the Textpattern forum:</p>
                <ol>
                    <li>Please make an effort to check the <a href="http://textpattern.com/support" rel="external">Textpattern CMS User Documentation</a> before posting, and check for duplicate threads on the forum.</li>
                    <li>Try to choose the most appropriate forum for your question. ‘Troubleshooting’ is for when you’ve tried something and it doesn’t work as expected. ‘How do I…?’ is self explanatory. Questions about plugins should go to the ‘Plugins’ forum, not ‘How do I…?’ or ‘Troubleshooting’.</li>
                    <li>If you’re asking for help with a problem or error, please describe the problem clearly and unambiguously. If there is an error message, please include an exact copy of the message in your post. If something doesn’t work as expected, describe what you’re expecting to see, and the actual behaviour. A link to the page in question is usually helpful, and sometimes a screenshot might be appropriate. Descriptions like “doesn’t work” or “it’s broken” are too vague, and unlikely to receive an answer.</li>
                    <li>Most suspected bugs aren’t really bugs, but problems caused by external factors. Unless you’ve confirmed the problem with a fresh install, or can identify the problem in the PHP source, start with a Troubleshooting post rather than a bug report.</li>
                    <li>Troubleshooting questions will usually be answered quicker if you include a copy of your diagnostics in your post (textpattern > admin > diagnostics). Don’t post the ‘High’ detail version unless someone asks.</li>
                    <li>Questions about templates and appearance will usually benefit from including a minimal copy of the code in question. Edit out as much irrelevant code as you can—a short snippet of code is much easier for people to quickly diagnose than an entire page.</li>
                    <li>Surround HTML or template code with <code>&lt;code&gt; &lt;/code&gt;</code> tags when posting.</li>
                    <li>Surround Textile code with <code>&lt;notextile&gt; &lt;/notextile&gt;</code> tags when posting.</li>
                    <li>More detailed examples of how to post HTML, template and Textile code are provided below.</li>
                    <li>If you haven’t received an answer in a day or so, there’s probably a good reason: an ambiguous or rambling question, or not enough information. Don’t ‘bump’ your thread with a single-word post, it’s more likely to annoy people than attract an answer. Post some more information instead.</li>
                    <li>Don’t post a troubleshooting question as a reply to an existing thread unless your problem is exactly the same as the one described at the beginning of the thread.</li>
                    <li>If you’re requesting a feature, <a href="http://forum.textpattern.com/viewtopic.php?id=10325">this article</a> lists some of the things you can do to increase its chances.</li>
                </ol>
                <h2>How do I post tags and code on the forum?</h2>
                <p>For inline code:</p>
                <pre><code>@&lt;txp:permlink&gt;&lt;txp:title /&gt;&lt;/txp:permlink&gt;@</code></pre>
                <p>For short snippets:</p>
                <pre><code>bc. &lt;h3&gt;&lt;txp:permlink&gt;&lt;txp:title /&gt;&lt;/txp:permlink&gt;&lt;/h3&gt;
&lt;p&gt;&lt;txp:posted /&gt;&lt;/p&gt;</code></pre>
                <p>For longer snippets:</p>
                <pre><code>bc.. &lt;txp:permlink&gt;&lt;txp:title /&gt;&lt;/txp:permlink&gt;
&lt;p&gt;&lt;txp:posted /&gt;&lt;/p&gt;
	
&lt;txp:body /&gt;</code></pre>
                <p>To post short Textile examples:</p>
                <pre><code>==*Bold*==</code></pre>
                <p>or</p>
                <pre><code>notextile. *Bold*</code></pre>
                <p>Or, for longer Textile examples:</p>
                <pre><code>notextile.. h1. My Title

*Bold*</code></pre>
                <p>If you’re still having trouble, or posting something long or complex, consider posting your code on pastebin.com and linking to that content from the forum.</p>

            </main>
        </div><!-- /.container -->

    </div><!-- /.wrapper -->


<!-- Secondary Content -->
    <div role="complementary" class="container">


        <div class="layout--span-1-8">
            <section role="region" class="layout--span-1-6 at-break2">
                <h4>Placeholder text</h4>
            </section>
            <section role="region" class="layout--span-7-12 at-break2">
                <h4>Placeholder text</h4>
            </section>
        </div><!-- /.layout--span-1-8 -->


        <section role="region" class="layout--span-9-12 host-details">
            <h4 class="accessibility">Host details</h4>
            <p><img class="svg" src="style/Textpattern/img/branding/textpattern-network/textpattern-network-white.png" alt="Textpattern Network"></p>
            <p>
                <small>
                    Kindly hosted by<br>
                    <a rel="external" href="http://joyent.com/" title="Go to the Joyent website"><img class="svg" src="style/Textpattern/img/branding/misc/joyent-inverse.png" alt="Joyent"></a>
                </small>
            </p>
        </section>

    </div><!-- /role="complementary" -->

    
<!-- Footer -->
    <footer role="contentinfo">
        <p class="legal">
            <a href="#">Copyright</a> 2004–2013 The Textpattern Development Team.
            <a href="#">Contact us</a>.
            <a href="#">Privacy</a>.
            <a href="humans.txt">Colophon</a>.
            Textpattern is both free and open source. <a href="#">GPLv2 license</a>.
        </p>
        <p class="html5"><a rel="external" href="http://www.w3.org/html/logo/" title="HTML5 powered">HTML5 powered</a></p>
    </footer>


<!-- JavaScript -->
    <script data-main="style/Textpattern/js/main.@@timestamp.js" src="style/Textpattern/js/require.@@timestamp.js"></script>
</body>
</html>
