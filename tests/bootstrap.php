<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

/*
 * CI provides an empty Postgres database at DATABASE_URL, so the suite
 * prepares its own schema before the first test runs. Doing it here means
 * every way of starting the suite (`composer test`, `composer test --
 * --filter …`, `php bin/phpunit`) works against a freshly provisioned
 * database, and the console commands resolve the test database exactly
 * the way the tests themselves do.
 */
$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'test', true);
$console = new Application($kernel);
$console->setAutoExit(false);
$output = new ConsoleOutput();

$prepareDatabase = [
    ['command' => 'doctrine:database:create', '--if-not-exists' => true],
    ['command' => 'doctrine:migrations:migrate', '--allow-no-migration' => true],
];

foreach ($prepareDatabase as $parameters) {
    $exitCode = $console->run(new ArrayInput($parameters + ['--no-interaction' => true]), $output);

    if (0 !== $exitCode) {
        throw new RuntimeException(sprintf('Could not prepare the test database: "%s" failed.', $parameters['command']));
    }
}

$kernel->shutdown();
