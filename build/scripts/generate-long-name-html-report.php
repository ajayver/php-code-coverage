<?php declare(strict_types=1);

/*
 * Reproduce the php-code-coverage 9.2.32 dashboard with a LearnDash-length class name:
 *
 * COMPOSER_ROOT_VERSION=9.2.32 composer install
 * php build/scripts/generate-long-name-html-report.php /tmp/report-before before
 * php build/scripts/generate-long-name-html-report.php /tmp/report-after after
 *
 * The before mode restores the pre-fix scrollbox and table layout through custom.css.
 */

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

require __DIR__ . '/../../vendor/autoload.php';

define('TEST_FILES_PATH', __DIR__ . '/../../tests/_files/');

$target = $argv[1] ?? sys_get_temp_dir() . '/php-code-coverage-9.2.32-report';
$mode   = $argv[2] ?? 'after';

if ($mode !== 'before' && $mode !== 'after') {
    fwrite(STDERR, "Usage: php generate-long-name-html-report.php [target] [before|after]\n");
    exit(1);
}

$fixture = TEST_FILES_PATH . 'LearnDashLongClassName.php';
$lines   = array_fill(1, count(file($fixture)), Driver::LINE_NOT_EXECUTED);

$driver = new class extends Driver
{
    public function nameAndVersion(): string
    {
        return 'HTML report fixture';
    }

    public function start(): void
    {
    }

    public function stop(): RawCodeCoverageData
    {
        return RawCodeCoverageData::fromXdebugWithoutPathCoverage([]);
    }
};

$filter = new Filter;
$filter->includeFile($fixture);

$coverage = new CodeCoverage($driver, $filter);
$coverage->append(
    RawCodeCoverageData::fromXdebugWithoutPathCoverage([
        $fixture => $lines,
    ]),
    'Long class name layout fixture'
);

(new Facade)->process($coverage, $target);

if ($mode === 'before') {
    file_put_contents(
        $target . '/_css/custom.css',
        ".scrollbox { overflow-x: scroll; }\n" .
        ".scrollbox table { table-layout: auto; }\n" .
        ".scrollbox table th:last-child, .scrollbox table td:last-child { width: auto; }\n" .
        ".scrollbox table td:first-child { overflow-wrap: normal; }\n"
    );
}

fwrite(STDOUT, sprintf("Generated %s report in %s\n", $mode, $target));
