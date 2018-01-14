<?php

// Debug from console
// set XDEBUG_CONFIG="idekey=xdebug"
// php test.php

require_once __DIR__ . '/../vendor/autoload.php';

ob_start();

$phpunit = new \PHPUnit\TextUI\TestRunner();

try {
    $testResults = $phpunit->doRun($phpunit->getTest(__DIR__, '', 'Test.php'), array(), false);
} catch (\PHPUnit\Framework\Exception $e) {
    print $e->getMessage() . "\n";
    echo "Unit tests failed.";
}
