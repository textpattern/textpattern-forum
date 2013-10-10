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

/**
 * Runs maintenance tasks when requested.
 *
 * @example
 * use Textpattern\Fluxbb\Tasks;
 * new Tasks('aZdbY!daIoB');
 */

class Tasks
{
    /**
     * Listened HTTP GET parameter.
     *
     * @var string
     */

    protected $parameter = 'textpattern_fluxbb_tasks';

    /**
     * Constructor.
     *
     * @param string $key The key
     */

    public function __construct($key)
    {
        if (isset($_GET[$this->parameter]) && $_GET[$this->parameter] === $key)
        {
            $out = array();

            foreach (get_class_methods($this) as $method)
            {
                if (strpos($method, 'task') === 0)
                {
                    $out[$method] = $this->$method();
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($out);
            die();
        }
    }

    /**
     * Removes users older than a month and have never logged in.
     */

    public function taskRemoveUnverifiedAccounts()
    {
        $time = strtotime('-1 month');
        return (int) Db::pdo()->exec("DELETE FROM users WHERE group_id = 0 and registered < {$time}");
    }
}
