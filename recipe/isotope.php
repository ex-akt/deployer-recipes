<?php

namespace Deployer;

use Deployer\Exception\ConfigurationException;

// Upload isotope-folder to remote (rsync)
desc('Upload isotope to remote.');
task('isotope:release', static function () {
    upload('isotope/',"{{release_or_current_path}}/isotope/");
    info('Upload isotope/ directory completed');
});
before('files:release_isotope', 'ask_deploy_folder');

// Download isotope-folder from remote (rsync)
desc('Download isotope-folder from remote.');
task('files:retrieve_isotope', static function () {
    download("{{release_or_current_path}}/isotope/",'isotope/');
    info('Upload isotope/ directory completed');
});