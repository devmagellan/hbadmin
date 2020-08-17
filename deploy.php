<?php

/**
 * Deploy via user "ubuntu"
 * First you need add you ssh public key to server authorized_keys
 *
 * Now work deploy DEV env: "dep deploy dev"
 */
namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('app_prefix', '/var/www');
set('path_prod', '{{app_prefix}}/merchants.heartbeat.education');
set('path_dev', '{{app_prefix}}/hbadmin_test');


// Project repository
set('repository', 'git@bitbucket.org:heartbeat_education/hbadmin.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
/** By default for symfony see symfony.php */
//add('shared_files', []);
//add('shared_dirs', []);  can be var/sessions

set('bin_dir', 'bin');
set('var_dir', 'var');
set('shared_dirs', ['var/logs']);
set('writable_dirs', ['var/cache', 'var/logs']);

// Hosts
host('prod')
    ->hostname('ubuntu@18.218.15.255')
    ->set('user', 'ubuntu')
    ->set('username', 'ubuntu')
    ->set('deploy_path', '{{path_prod}}')
    ->set('branch', 'master') // branch
    ->set('symfony_env', 'prod') //for prod env
    ->set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader') /** composer has no key --no-suggest */
    ->set('keep_releases', 5);

host('dev')
    ->hostname('ubuntu@18.218.15.255')
    ->set('user', 'ubuntu')
    ->set('username', 'ubuntu')
    ->set('deploy_path', '{{path_dev}}')
    ->set('branch', 'develop') // branch
    ->set('symfony_env', 'dev') //for dev env
    ->set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --dev --optimize-autoloader') /** composer has no key --no-suggest */
    ->set('clear_paths', []) /** allow use dev environment. app_dev use in nginx conf */
    ->set('keep_releases', 5)
;


// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('deploy:ckeditor', function() {
    run('cd {{release_path}} && php bin/console ckeditor:install --clear=drop');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:vendors', 'deploy:ckeditor');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');


