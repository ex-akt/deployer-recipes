<?php

namespace Deployer;

use Deployer\Exception\ConfigurationException;

desc('Copy local .env.prod file to shared path and rename to .env.local');
task('deploy:env.local', function () {
    try {
        if ($env = get('env_filename')) {
            upload($env,'{{deploy_path}}/shared/.env.local',);
            info($env . 'copied to shared folder and renamed to .env.local');
        }
        elseif(\file_exists('.env.prod')){
            upload('.env.prod','{{deploy_path}}/shared/.env.local',);
            info('.env.prod copied to shared folder and renamed to .env.local');
        }
        else{
            warning('No .env-file specified. Please add .env.prod in root or set env_filename option');
        }

    } catch (ConfigurationException $e) {}
});


//WGET instead of CURL implementation of contao:manager:download
task('contao:manager:download', function () {
    run('cd {{release_or_current_path}} && wget https://download.contao.org/contao-manager/stable/contao-manager.phar && mv contao-manager.phar {{release_or_current_path}}/{{public_path}}/contao-manager.phar.php');
 });
 