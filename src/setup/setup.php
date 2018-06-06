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

/**
 * This does the initial project setup.
 */

$fluxbb_download = 'http://fluxbb.org/download/releases/1.5.10/fluxbb-1.5.10.tar.gz';
$fluxbb_dir = basename($fluxbb_download, '.tar.gz');

`mkdir -pv tmp`;
chdir('tmp');
`rm -rf fluxbb`;
`mkdir -pv download`;
chdir('download');

if (!file_exists("$fluxbb_dir.tar.gz")) {
    echo "Downloading FluxBB...\n";
    `curl -OL $fluxbb_download`;
}

echo "Extracting download...\n";
`tar -xvf $fluxbb_dir.tar.gz`;

$file = false;

foreach (glob('*') as $file) {
    if (is_dir($file) && preg_match('/[a-z0-9\-._]/i', $file)) {
        $dir = basename($file);
        break;
    }
}

if (!$dir) {
    echo 'Unable to find extracted directory.';
}

`mv $dir ../fluxbb`;
chdir('../fluxbb');

echo "Applying patches...\n";

foreach (glob('../../src/setup/patches/*.patch') as $file) {
    echo "Applying ".basename($file)."...\n";
    `patch -p1 < $file`;
}

echo "Cleaning up the downloaded package...\n";

foreach (array('.git', '.gitattributes', '.gitignore', 'COPYING', 'readme.md', 'img') as $file) {
    echo "Remove {$file}...\n";
    `rm -Rf '{$file}'`;
}

foreach (glob('style/*') as $file) {
    if (basename($file) !== 'imports') {
        echo "Remove {$file}...\n";
        `rm -Rf '{$file}'`;
    }
}

chdir('../../');

// Keep existing configuration.

if (file_exists('public/config.php')) {
    echo "Keep existing config.php...\n";
    copy('public/config.php', 'tmp/fluxbb/config.php');
    echo "Remove install.php...\n";
    `rm -Rf 'tmp/fluxbb/install.php'`;
}

echo "Removing the old installation...\n";

$removeExtensions = array(
    'php',
    'md',
    'js',
);

$removeNames = array(
    'COPYING',
);

$keep = array(
    'img',
    'api',
);

foreach (glob('public/*') as $file) {
    $name = basename($file);
    $ext = pathinfo($name, PATHINFO_EXTENSION);

    if (in_array($name, $keep)) {
        echo "Keep {$file}...\n";
        continue;
    }

    if (is_dir($file) || in_array($name, $removeNames) || in_array($ext, $removeExtensions)) {
        echo "Remove {$file}...\n";
        `rm -Rf '{$file}'`;
    }
}

echo "Moving in the new installation...\n";
`cp -rf tmp/fluxbb/* public/`;

if (!file_exists('public/img/avatars')) {
    echo "Creating img/avatars...\n";
    `mkdir -pv public/img/avatars`;
}

echo "Setting file permissions...\n";
`chmod 755 public/img/avatars`;
`chmod 755 public/cache`;

echo "Removing trash...\n";
`rm -Rf tmp`;
