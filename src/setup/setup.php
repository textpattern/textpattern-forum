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

/**
 * This does the initial project setup.
 */

$fluxbb_download = 'http://fluxbb.org/download/releases/1.5.5/fluxbb-1.5.5.tar.gz';
$fluxbb_dir = basename($fluxbb_download, '.tar.gz');

`mkdir -pv tmp`;

chdir('tmp');

`rm -rf $fluxbb_dir`;
`rm -rf fluxbb`;

if (!file_exists("$fluxbb_dir.tar.gz"))
{
    echo "Downloading FluxBB...\n";
    `curl -O $fluxbb_download`;
}

echo "Extracting download...\n";
`tar -zxvf $fluxbb_dir.tar.gz`;
`mv $fluxbb_dir fluxbb`;

chdir('fluxbb');

echo "Applying patches...\n";

foreach (glob('../../src/setup/patches/*.patch') as $file)
{
    echo "Applying ".basename($file)."...\n";
    `patch -p1 < $file`;
}

echo "Cleaning up the downloaded package...\n";

foreach (array('.git', '.gitattributes', '.gitignore', 'COPYING', 'readme.md', 'img') as $file)
{
    echo "Remove {$file}...\n";
    `rm -Rf '{$file}'`;
}

foreach (glob('style/*') as $file)
{
    if (basename($file) !== 'imports')
    {
        echo "Remove {$file}...\n";
        `rm -Rf '{$file}'`;
    }
}

chdir('../../');

// Keep existing configuration.

if (file_exists('public/config.php'))
{
    echo "Keep existing config.php...\n";
    copy('public/config.php', 'tmp/fluxbb/config.php');
    echo "Remove install.php...\n";
    `rm -Rf 'tmp/fluxbb/install.php'`;
}

echo "Moving in the new installation...\n";

foreach (glob('public/*') as $file)
{
    $name = basename($file);

    if (!in_array($name, array('img', 'api')) && ($name === 'COPYING' || in_array(pathinfo($file, PATHINFO_EXTENSION), array('php', 'md', 'js')) || is_dir($file)))
    {
        echo "Remove {$file}...\n";
        `rm -Rf '{$file}'`;
    }
}

echo "Moving in the new installation...\n";
`cp -rf tmp/fluxbb/ public/`;
`chmod 755 public/img/avatars`;
`chmod 755 public/cache`;

echo "Removing trash...\n";
`rm -Rf tmp/fluxbb`;
