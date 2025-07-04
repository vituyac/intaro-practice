<?php
require_once __DIR__ . '/../core/FakeStoreImporter.php';
require_once __DIR__ . '/../core/Database.php';

use App\core\FakeStoreImporter;

$importer = new FakeStoreImporter();
$importer->importAll();
echo "Импорт завершен\n";