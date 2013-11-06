<?php

$env->repository('git@github.com:textpattern/textpattern-forum.git')
    ->deploy_to('../test-deploy');

group('dependencies', function()
{
    desc('Tries to install base dependencies.');

    task('install', function($app)
    {
    });

    desc('Validates the system.');

    task('validate', function ($app)
    {
        $paths = array();

        foreach (array('ruby', 'gem', 'npm', 'bundle', 'grunt', 'bower', 'composer') as $bin)
        {
            if (($status = run('which '.$bin, true)) && !($path = end($status)))
            {
                abort('dependencies:validate', $bin.' is not installed to $PATH.');
            }

            $paths[] = '   '.str_pad($bin, 8, ' ').' '.$path;
        }

        info('dependencies:validate', "Validated! Dependencies found: \n".implode("\n", $paths));
    });
});

after('deploy', function($app)
{
    run(array(
        'npm install',
        'bundle install',
        'bower install',
        'composer install',
        'grunt setup',
        'grunt postsetup',
        'grunt build',
    ));
});
