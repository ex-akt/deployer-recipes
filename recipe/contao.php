<?php

namespace Deployer;

// Beide Tasks ersetzen die gleichnamigen aus nutshell-framework/deployer-recipes.
// Dort (und in den Versionen bis 2.1 hier) fing ein leerer
// `catch (ConfigurationException $e) {}` jede ungesetzte Option ab – die Tasks
// liefen dann WIRKUNGSLOS UND LAUTLOS durch. Jetzt meldet jeder Fall, was er
// tut oder warum er nichts tut. Hooks bleiben erhalten: task() ersetzt beim
// Neudefinieren nur den Callback.

desc('Copy local .env.production (or env_filename) to shared/.env.local');
task('deploy:env.local', function () {
    $env = has('env_filename') ? get('env_filename') : null;

    // Ohne env_filename die üblichen Namen probieren (.env.prod = Altbestand).
    if (!$env) {
        foreach (['.env.production', '.env.prod'] as $candidate) {
            if (\file_exists($candidate)) {
                $env = $candidate;
                break;
            }
        }
    }

    if (!$env || !\file_exists($env)) {
        throw error(sprintf(
            'deploy:env.local: keine Env-Datei gefunden (%s) – shared/.env.local wurde NICHT geschrieben. '
            . "set('env_filename', '.env.production') setzen oder die Datei anlegen.",
            $env ?: '.env.production / .env.prod'
        ));
    }

    upload($env, '{{deploy_path}}/shared/.env.local');
    info("$env → shared/.env.local");
});

desc('Activate the production .htaccess (htaccess_filename) in the release');
task('deploy:htaccess', function () {
    $htaccess = has('htaccess_filename') ? get('htaccess_filename') : null;

    // Bewusst KEIN Default: in Altprojekten liegen teils veraltete
    // .htaccess.production-Dateien (msinga.de: von 2017, aktive .htaccess von
    // 2021). Ein Default würde sie beim nächsten Deploy still scharf schalten.
    if (!$htaccess) {
        $local = get('public_path', 'public') . '/.htaccess.production';
        if (\file_exists($local)) {
            warning("deploy:htaccess: $local liegt im Projekt, wird aber NICHT aktiviert – "
                . "set('htaccess_filename', '.htaccess.production') in der deploy.php setzen");
        }
        return;
    }

    cd('{{release_path}}/{{public_path}}');
    if (!test("[ -f ./$htaccess ]")) {
        warning("deploy:htaccess: $htaccess fehlt im Release – die Standard-.htaccess bleibt aktiv");
        return;
    }
    run("mv ./$htaccess ./.htaccess");
    run('rm -f .htaccess_*');
    info("$htaccess → .htaccess");
});


//WGET instead of CURL implementation of contao:manager:download
task('contao:manager:download', function () {
    run('cd {{release_or_current_path}} && wget https://download.contao.org/contao-manager/stable/contao-manager.phar && mv contao-manager.phar {{release_or_current_path}}/{{public_path}}/contao-manager.phar.php');
 });
