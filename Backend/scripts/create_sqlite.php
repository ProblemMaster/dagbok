<?php
// Creates database/database.sqlite and imports database/dump.sql
$root = dirname(__DIR__);
$dbDir = $root . '/database';
$dbFile = $dbDir . '/database.sqlite';
$dumpFile = $dbDir . '/dump.sql';

if (!file_exists($dbDir)) mkdir($dbDir, 0755, true);

if (!file_exists($dumpFile)) {
    echo "Missing dump.sql at {$dumpFile}\n";
    exit(1);
}

if (file_exists($dbFile)) {
    echo "Database file already exists at {$dbFile}\n";
    exit(0);
}

$pdo = new PDO('sqlite:' . $dbFile);
$sql = file_get_contents($dumpFile);

// Execute multiple statements
$statements = array_filter(array_map('trim', explode(';', $sql)));
foreach ($statements as $stmt) {
    if ($stmt === '') continue;
    $pdo->exec($stmt . ';');
}

echo "Created {$dbFile} and imported schema/data.\n";
