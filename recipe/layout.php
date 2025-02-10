<?php

namespace Deployer;

use Deployer\Exception\ConfigurationException;

// Upload layout to remote (rsync)
desc('Uploads layout to remote.');
task('files:release_layout', static function () {
    upload('layout/',"{{release_or_current_path}}/layout/");
    info('Upload layout/ directory completed');
});
before('files:release_layout', 'ask_deploy_folder');

// Upload layout from remote (rsync)
desc('Downloads layout from remote.');
task('files:retrieve_layout', static function () {
    download("{{release_or_current_path}}/layout/",'layout/');
    info('Download layout/ directory completed');
});