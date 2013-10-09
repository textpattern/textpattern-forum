<?php

namespace Textpattern\Fluxbb;

/**
 * Checks StopForumSpam database against user-data.
 *
 * @example
 * use Textpattern\Fluxbb\Records;
 * $records = new Records('127.0.0.0', 'john.doe@example.com');
 * print_r($records->getRecord());
 */

class Records extends Sfs
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $this->ip = array();
        $this->email = array();
    }

    /**
     * Adds an email.
     */

    public function addEmail($email)
    {
        $this->email[] = (string) $email;
        return $this;
    }

    /**
     * Adds an IP.
     */

    public function addIp($ip)
    {
        $this->ip[] = (string) $ip;
        return $this;
    }
}
