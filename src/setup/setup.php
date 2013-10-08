<?php

$fluxbb_download = 'http://fluxbb.org/download/releases/1.5.4/fluxbb-1.5.4.tar.gz';
$fluxbb_dir = basename($fluxbb_download, '.tar.gz');

`mkdir -pv tmp`;

chdir('tmp');
echo "Downloading FluxBB...\n";

`rm -rf $fluxbb_dir`;
`rm -rf fluxbb`;
`curl -O $fluxbb_download`;
`tar -zxvf $fluxbb_dir.tar.gz`;
`rm -rf $fluxbb_dir.tar.gz`;
`mv $fluxbb_dir fluxbb`;

chdir('fluxbb');

echo "Applying patches...\n";

foreach (glob('../../src/setup/patches/*.patch') as $file)
{
    echo "Applying ".basename($file)."...\n";
    `patch -p1 < $file`;
}

echo "Cleaning up the downloaded package...\n";

foreach (array('.git', '.gitattributes', '.gitignore', 'COPYING', 'readme.md') as $file)
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
    if (basename($file) === 'COPYING' || in_array(pathinfo($file, PATHINFO_EXTENSION), array('php', 'md', 'js')) || is_dir($file))
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
`rm -Rf tmp`;
