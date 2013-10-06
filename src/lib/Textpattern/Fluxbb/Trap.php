<?php

namespace Textpattern\Fluxbb;

/**
 * Hidden spam trap field.
 *
 * @example
 * use Textpattern\Fluxbb\Trap;
 * new Trap('http://example.com/');
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

    public function __construct($url)
    {
        $this->url = (string) $url;

        foreach (get_class_methods($this) as $method)
        {
            if (strpos($method, 'trapForm') === 0)
            {
                $this->$method();

                if ($this->trap)
                {
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
        foreach ($_POST as $name => $value)
        {
            if (strpos($name, 'textpattern_fluxbb_t_') === 0 && $value)
            {
                header('Location: '.$this->url);
                die;
            }
        }
    }

    /**
     * Renders a spam trap field.
     *
     * @param  string $type  The field type
     * @param  string $name  The field name
     * @param  string $value The field value
     * @param  string $label The field label
     * @return string HTML
     * @example
     * $this->formInput('text', 'displayname', '', 'Display name');
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
        if (strpos($_SERVER['REQUEST_URI'], 'register.php') !== false)
        {
            $this->search = '<input type="hidden" name="form_sent" value="1" />';
            $this->trap = $this->formInput('text', 'displayname', '', 'Display name');
        }
    }

    /**
     * Spam trap field added to the login form.
     *
     * @return string
     */

    protected function trapFormLogin()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'login.php') !== false)
        {
            $this->search = '<label><input type="checkbox" name="save_pass"';
            $this->trap = $this->formInput('checkbox', 'anonymous', 1, 'Log in anonymously?');
        }
    }
}
