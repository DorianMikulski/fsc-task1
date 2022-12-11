<?php

$options = getopt('', ['html:', 'csv:']);

// walidacja danych wejściowych skryptu
validation($options);

list($htmlFileName, $csvFileName) = [ $options['html'], $options['csv'] ];

echo "Rozpoczynam przetwarzanie pliku '$htmlFileName'...\n";

try {
	require_once __DIR__ . "\Data.php";

	// załadowanie dokumentu HTML do parsowania
	$document = new DOMDocument();
	$document->loadHTMLFile($htmlFileName);

	// przetwarzanie danych z dokumentu HTML
	$modelData = new Data($document);
	$modelData->getData();

	// zapis do pliku CSV
	$modelData->toCSV($csvFileName);

} catch (Exception $e) {
	echo 'Wystąpił błąd: ', $e->getMessage(), "\n";
}

$csvFileSize = filesize($csvFileName);

echo "Wygenerowano plik CSV o nazwie '$csvFileName' ($csvFileSize B). \n Możesz go zaimportować do programu Excel\n";


/**
 * Walidacja danych wejściowych skryptu:
 * - czy podano argument --html z plikiem do parsowania
 * - czy podano argument --csv - nazwę pliku wyjściowego w formacie CSV 
 * - czy plik HTML z argumentu istnieje w projekcie
 * 
 * @param array $options - argumentu wywołania skryptu
 */
function validation (array $options) {
	if (empty($options['html'])) {
		throw new Exception("Argument '--html' jest wymagany", 1);
	}
	if (empty($options['html'])) {
		throw new Exception("Argument '--csv' jest wymagany", 1);
	}
	if (!file_exists($options['html'])) {
		throw new Exception("Podany plik '{$options['html']}' nie istnieje", 1);
	}
}