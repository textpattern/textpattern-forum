<?php

/**
 * Textpattern Support Forum.
 *
 * @link    https://github.com/textpattern/textpattern-forum
 * @license MIT
 */

/*
 * Copyright (C) 2013 Team Textpattern
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Textpattern\Fluxbb;

use Neutron\ReCaptcha\ReCaptcha;

/**
 * Hidden spam trap field.
 *
 * <code>
 * use Textpattern\Fluxbb\Trap;
 * new Trap('http://example.com/');
 * </code>
 */

class Trap
{
    /**
     * Stores the target location.
     *
     * @var string
     */

    protected $url;

    /**
     * Searched markup.
     *
     * @var string
     */

    protected $search;

    /**
     * Stores the trap markup.
     *
     * @var string
     */

    protected $trap;

    /**
     * Constructor.
     *
     * @param string $url The URL user is redirected when spam trap is filled
     */

    public function __construct($url = 'index.php')
    {
        $this->url = (string) $url;

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'trapForm') === 0) {
                $this->$method();

                if ($this->trap) {
                    ob_start(array($this, 'addTrap'));
                    break;
                }
            }
        }

        $this->filterRequest();
    }

    /**
     * Adds trap to form on the page.
     *
     * @return string
     */

    public function addTrap($buffer)
    {
        return str_replace($this->search, $this->trap . "\n" . $this->search, $buffer);
    }

    /**
     * Filters the request.
     *
     * Kills the process and redirects the user,
     * if a filled spam trap field is found in
     * the request.
     */

    protected function filterRequest()
    {
        foreach ($_POST as $name => $value) {
            if (strpos($name, 'textpattern_fluxbb_t_') === 0 && $value) {
                header('Location: '.$this->url);
                die;
            }
        }
    }

    /**
     * Renders a spam trap field.
     *
     * <code>
     * $this->formInput('text', 'displayname', '', 'Display name');
     * </code>
     *
     * @param  string $type  The field type
     * @param  string $name  The field name
     * @param  string $value The field value
     * @param  string $label The field label
     * @return string HTML
     */

    protected function formInput($type, $name, $value, $label)
    {
        static $instance = 0;

        $name = 'textpattern_fluxbb_t_' . htmlspecialchars($name);
        $id = $name . '-' . ($instance++);

        return
            '<p class="textpattern-fluxbb-t '.$id.'">'.
                '<label for="'.$id.'">'.htmlspecialchars($label).'</label>'.
                '<input type="'.htmlspecialchars($type).'" name="'.$name.'" value="" id="'.$id.'" />'.
            '</p>';
    }

    /**
     * Spam trap field added to the register form.
     *
     * @return string
     */

    protected function trapFormRegister()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'register.php') !== false &&
            (!empty($_GET['agree']) || !empty($_GET['action']))
        ) {
            $this->search = '<p class="buttons">';
            $this->trap = $this->formInput('text', 'displayname', '', 'Display name');

            if (defined('\TEXTPATTERN_FORUM_RECAPTCHA_PUBLIC_KEY')) {
                $recaptcha = ReCaptcha::create(
                    \TEXTPATTERN_FORUM_RECAPTCHA_PUBLIC_KEY,
                    \TEXTPATTERN_FORUM_RECAPTCHA_PRIVATE_KEY
                );

                $this->trap .= <<<EOF
                    <script>
                        var RecaptchaOptions = {
                            theme: 'custom',
                            custom_theme_widget: 'recaptcha_widget'
                        };
                    </script>

                    <fieldset class="recaptcha-widget" id="recaptcha_widget" style="display:none">
                        <legend>Answer the security question</legend>

                        <p id="recaptcha_image"></p>
                        <p class="recaptcha_only_if_incorrect_sol">Incorrect, please try again.</p>

                        <p>
                            <label for="recaptcha_response_field" class="recaptcha_only_if_image">
                                Enter the words above:
                            </label>

                            <label for="recaptcha_response_field" class="recaptcha_only_if_audio">
                                Enter the numbers you hear:
                            </label>

                            <br />

                            <input size="50" required type="text" 
                                id="recaptcha_response_field"
                                name="recaptcha_response_field">
                        </p>

                        <p>
                            <a href="#" class="recaptcha-reload">Refresh captcha</a>
                            <span class="recaptcha_only_if_image">
                                | <a href="#" class="recaptcha-switch-audio">Get an audio captcha</a>
                            </span>
                            <span class="recaptcha_only_if_audio">
                                | <a href="#" class="recaptcha-switch-image">Get an image captcha</a>
                            </span>
                            | <a href="#" class="recaptcha-show-help">Help</a><br />
                            Powered by <a href="http://www.google.com/recaptcha">reCAPTCHA</a>
                        </p>

                        <script src="//www.google.com/recaptcha/api/challenge?k={$recaptcha->getPublicKey()}"></script>

                        <noscript>
                            <div>
                                <iframe
                                    src="//www.google.com/recaptcha/api/noscript?k={$recaptcha->getPublicKey()}"
                                    height="300"
                                    width="500"
                                    frameborder="0"></iframe>
                            </div>
                            <p><textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea></p>
                            <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                        </noscript>
                </fieldset>
EOF;
            }
        }
    }

    /**
     * Spam trap field added to the login form.
     *
     * @return string
     */

    protected function trapFormLogin()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'login.php') !== false) {
            $this->search = '<label><input type="checkbox" name="save_pass"';
            $this->trap = $this->formInput('checkbox', 'anonymous', 1, 'Log in anonymously?');
        }
    }
}
