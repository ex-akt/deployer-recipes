<?php

namespace Deployer;

// Ersetzt database:retrieve / database:release / ask_release aus
// nutshell-framework/deployer-recipes. Zwei Gründe:
//
// 1. DDEV: Die lokale Datenbank liegt im Container, der Host `db` ist vom Mac
//    aus nicht erreichbar. `php vendor/bin/contao-console` direkt auf dem Mac
//    bricht deshalb ab – lokal muss es `ddev exec` sein.
// 2. database:release überschreibt die LIVE-Datenbank. Bisher ohne Sicherung
//    und mit einem einfachen Ja/Nein. Jetzt: Live-Backup vorher (auf dem Server
//    UND lokal, weil var/backups im Release liegt und mit alten Releases
//    verschwindet) und Bestätigung per eingetipptem Hostnamen.

set('local_console', static function () {
    return \is_dir('.ddev')
        ? 'ddev exec php vendor/bin/contao-console'
        : 'php vendor/bin/contao-console';
});

desc('Downloads a database dump from given host and overrides the local database');
task('database:retrieve', static function () {
    $dumpFilename = sprintf('deployer__%s.sql.gz', date('YmdHis'));

    cd('{{release_or_current_path}}');
    run("{{bin/console}} contao:backup:create '$dumpFilename'");
    info('Backup created on remote machine');

    runLocally('mkdir -p var/backups');
    download("{{release_or_current_path}}/var/backups/$dumpFilename", 'var/backups/', ['progress_bar' => false]);
    info('Backup archive downloaded');

    runLocally("{{local_console}} contao:backup:restore '$dumpFilename'");
    info('Local database restored');

    try {
        runLocally('{{local_console}} contao:migrate --no-interaction --no-backup');
        info('Local database migrated');
    } catch (\Exception $e) {
        warning('Local database migration skipped');
    }
});

desc('Restores the local database on the given host (backs up the live database first)');
task('database:release', static function () {
    $stamp = date('YmdHis');
    $preFilename = "deployer__vor-release__$stamp.sql.gz";
    $dumpFilename = "deployer__$stamp.sql.gz";

    // 1. Live-Datenbank sichern, bevor sie überschrieben wird.
    cd('{{release_or_current_path}}');
    run("{{bin/console}} contao:backup:create '$preFilename'");
    runLocally('mkdir -p var/backups');
    download("{{release_or_current_path}}/var/backups/$preFilename", 'var/backups/', ['progress_bar' => false]);
    info("Live database backed up: var/backups/$preFilename (remote + local)");

    // 2. Lokalen Stand hochspielen.
    runLocally("{{local_console}} contao:backup:create '$dumpFilename'");
    info('Backup created on local machine');

    upload("var/backups/$dumpFilename", "{{release_or_current_path}}/var/backups/", ['progress_bar' => false]);
    info('Backup archive uploaded');

    cd('{{release_or_current_path}}');
    run("{{bin/console}} contao:backup:restore '$dumpFilename'");
    info('Remote database restored');

    try {
        run('{{bin/console}} contao:migrate {{console_options}} --no-backup');
        info('Remote database migrated');
    } catch (\Exception $e) {
        warning('Database migration skipped');
    }

    info("Rollback: {{bin/console}} contao:backup:restore '$preFilename' (remote)");
});

task('ask_release', static function () {
    $hostname = currentHost()->getHostname();
    $answer = ask("Die LIVE-Datenbank auf $hostname wird überschrieben. Zum Bestätigen den Hostnamen eintippen:");
    if (trim((string) $answer) !== $hostname) {
        throw error('database:release abgebrochen – eingegebener Hostname stimmt nicht');
    }
});
