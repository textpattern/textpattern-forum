<?php

`mkdir -pv tmp`;

chdir('tmp');
echo "Downloading FluxBB...\n";

`rm -rf fluxbb`;
`git clone --branch feature-textpattern-forum --depth 0 https://github.com/textpattern/fluxbb.git fluxbb`;

echo "Cleaning up the downloaded package...\n";

chdir('fluxbb');

foreach (array('.git', '.gitattributes', '.gitignore', 'style', 'COPYING', 'readme.md') as $file)
{
    echo "Remove {$file}...\n";
    `rm -Rf '{$file}'`;
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
    if (basename($file) === 'COPYING' || in_array(pathinfo($file, PATHINFO_EXTENSION), array('php', 'md')) || is_dir($file))
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
