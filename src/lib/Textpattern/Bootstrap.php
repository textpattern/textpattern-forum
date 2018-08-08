<?php

/**
 * Textpattern Support Forum.
 *
 * @link    https://github.com/textpattern/textpattern-forum
 * @license MIT
 */

/*
 * Copyright (C) 2018 Team Textpattern
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

namespace Textpattern;

use Composer\Autoload\ClassLoader;
use Textpattern\Fluxbb\Document;
use Textpattern\Fluxbb\Filter;
use Textpattern\Fluxbb\Sfs;
use Textpattern\Fluxbb\Trap;
use Textpattern\Fluxbb\Tasks;

include PUN_ROOT.'/vendor/autoload.php';
$loader = new ClassLoader();
$loader->add('Textpattern\\Fluxbb', dirname(__DIR__));
$loader->register();

new Filter();
new Sfs();
new Document();

if (defined('\TEXTPATTERN_FORUM_BASE_URL')) {
    new Trap(\TEXTPATTERN_FORUM_BASE_URL.'/index.php');
}

if (defined('\TEXTPATTERN_TASKS_KEY')) {
    new Tasks(\TEXTPATTERN_TASKS_KEY);
}
