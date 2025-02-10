<?php

namespace Deployer;

use Deployer\Exception\ConfigurationException;

//Confirm Upload Folders
task('ask_deploy_folder', static function () {
    if (!askConfirmation('Remote folder will be overridden. OK?')) {
        die("Sync cancelled.\n");
    }
});

// Upload files-folder to remote (rsync)
desc('Upload files to remote.');
task('files:release', static function () {
    upload('files/',"{{release_or_current_path}}/files/");
    info('Upload files/ directory completed');
});
before('files:release', 'ask_deploy_folder');