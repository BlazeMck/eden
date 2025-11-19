<?php
namespace Deployer;

require 'recipe/composer.php';

// Config

set('repository', 'https://github.com/BlazeMck/eden.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts


// Hooks

after('deploy:failed', 'deploy:unlock');
