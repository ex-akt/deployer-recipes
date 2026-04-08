<?php

namespace Deployer;

task('git:check_uncommitted', function () {
    $status = runLocally('git status --porcelain');
    if (!empty(trim($status))) {
        writeln('<comment>Uncommittete Änderungen:</comment>');
        writeln($status);
        if (!askConfirmation('Es gibt uncommittete Änderungen. Trotzdem deployen?')) {
            throw new \RuntimeException('Deploy abgebrochen. Bitte erst committen.');
        }
    }
})->desc('Prüft auf uncommittete Git-Änderungen vor dem Deploy');

before('deploy', 'git:check_uncommitted');
