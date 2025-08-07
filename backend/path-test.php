<?php
// Path Tester Script - Save as backend/path-test.php
header('Content-Type: application/json');

$paths_to_test = [
    '../config/db.php',
    dirname(__DIR__) . '/config/db.php',
    dirname(__FILE__) . '/../config/db.php',
    __DIR__ . '/../config/db.php',
    $_SERVER['DOCUMENT_ROOT'] . '/bowling/config/db.php',
    'C:/xampp/htdocs/bowling/config/db.php',
    '/config/db.php',
    '../../config/db.php'
];

$results = [
    'current_directory' => getcwd(),
    '__FILE__' => __FILE__,
    '__DIR__' => __DIR__,
    'dirname(__DIR__)' => dirname(__DIR__),
    'dirname(__FILE__)' => dirname(__FILE__),
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
    'tested_paths' => []
];

foreach ($paths_to_test as $path) {
    $absolute_path = realpath($path);
    $results['tested_paths'][] = [
        'path' => $path,
        'absolute_path' => $absolute_path ?: 'Could not resolve',
        'exists' => file_exists($path),
        'readable' => file_exists($path) && is_readable($path)
    ];
}

// Try to find db.php anywhere
$possible_locations = glob($_SERVER['DOCUMENT_ROOT'] . '/**/db.php', GLOB_BRACE);
if (empty($possible_locations)) {
    $possible_locations = glob($_SERVER['DOCUMENT_ROOT'] . '/*/config/db.php');
}

$results['found_db_files'] = $possible_locations ?: ['No db.php files found'];

echo json_encode($results, JSON_PRETTY_PRINT);
?>