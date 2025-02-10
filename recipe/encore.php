<?php

namespace Deployer;

task('deploy:encore:compile', function () {
    runLocally('npm --prefix vendor/ex-akt/contao-webpack-config-bundle run prod');
})->desc('Compile assets');


//before('deploy', 'deploy:encore:compile');

