<?php
use Symfony\Component\Console\Application;

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/commands/XiagTestCommand.php');

error_reporting(0);
ini_set('display_errors', 0);

$application = new Application();
$application->add(new XiagTestCommand());
$application->run();
