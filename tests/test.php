<?php

// Debug from console
// set XDEBUG_CONFIG="idekey=xdebug"
// php test.php

require_once __DIR__ . '/../vendor/autoload.php';

ob_start();

$phpunit = new \PHPUnit\TextUI\TestRunner();

try {
    if ($tests = $phpunit->getTest(__DIR__, '', 'Test.php')) {
        $testResults = $phpunit->doRun($tests, [], false);
    }
} catch (\PHPUnit\Framework\Exception $e) {
    echo $e->getMessage() . "\n";
    echo 'Unit tests failed.';
}
