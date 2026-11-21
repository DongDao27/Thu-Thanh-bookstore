<?php

// Clover counts only backend/app, as configured by phpunit.xml.
$path = $argv[1] ?? dirname(__DIR__).'/backend/storage/logs/coverage.xml';
if (! is_file($path)) {
    fwrite(STDERR, "Missing Clover report. Run PHPUnit with coverage first.\n");
    exit(1);
}
$xml = simplexml_load_file($path);
$metrics = $xml?->project?->metrics;
$total = (int) ($metrics['statements'] ?? 0);
$covered = (int) ($metrics['coveredstatements'] ?? 0);
if ($total <= 0 || $covered < 0 || $covered > $total) {
    fwrite(STDERR, "Invalid or empty coverage metrics.\n");
    exit(1);
}
$percent = 100 * $covered / $total;
printf("Application statement coverage: %.2f%% (%d/%d); required >70%%.\n", $percent, $covered, $total);
exit($percent > 70 ? 0 : 1);
