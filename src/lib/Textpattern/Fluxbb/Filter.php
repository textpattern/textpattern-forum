<?php

namespace Textpattern\Fluxbb;

/**
 * Filter incoming requests.
 */

class Filter
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->castParameters();
        $this->preventUnverified();
    }

    /**
     * Cast parameters.
     */

    protected function castParameters()
    {
        foreach (array($_GET, $_POST) as &$method)
        {
            foreach ($method as $name => $value)
            {
                $method[$name] = (string) $value;
            }
        }
    }

    /**
     * Prevent creating login cookies as guest or unverified.
     */

    protected function preventUnverified()
    {
        global $cookie_name;

        if (isset($_COOKIE[$cookie_name]))
        {
            $id = (int) join('', array_slice(explode('|', $_COOKIE[$cookie_name]), 0, 1));

            if (!$id)
            {
                unset($_COOKIE[$cookie_name]);
                return;
            }

            $sth = Db::pdo()->prepare('SELECT id FROM users WHERE id = :user and group_id = 0');
            $sth->execute(array(':user' => $id));

            if ($sth->rowCount())
            {
                unset($_COOKIE[$cookie_name]);
                return;
            }
        }
    }
}
