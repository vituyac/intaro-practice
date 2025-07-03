<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\core\FakeStoreImporter;

$importer = new FakeStoreImporter();
$importer->importAll();
echo "Импорт завершен\n";