<?php
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/services/FakeStoreImporter.php';

use App\services\FakeStoreImporter;

$importer = new FakeStoreImporter();
$importer->importAll();

echo "Импорт завершен\n";