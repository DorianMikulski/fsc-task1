<?php

class Data {
	
	/**
	 * 
	 * @var DOMDocument $_DOMDocument
	 */
	public $_DOMDocument;

	/**
	 * Przetworzone dane w postaci string rozdzielonego przecinkami do pliku CSV
	 * 
	 * @var array $_csvData
	 */
	protected $_csvData;



	/**
	 * Konstruktor klasy 
	 * 
	 * @param DOMDocument $document
	 */
	function __construct(DOMDocument $document) {
		$this->_DOMDocument = $document;
	}

	/**
	 * Pobranie danych z dokumentu HTML
	 */
	function getData() {
		$this->_csvData = [];

		// Tracking Number
		$this->_csvData[] = $this->_DOMDocument->getElementById('wo_number')->nodeValue;
		// PO Number
		$this->_csvData[] = $this->_DOMDocument->getElementById('po_number')->nodeValue;
		// Scheduled
		$this->_csvData[] = $this->getScheduled();
		// Customer
		$this->_csvData[] = $this->_DOMDocument->getElementById('customer')->nodeValue;
		// Trade
		$this->_csvData[] = $this->_DOMDocument->getElementById('trade')->nodeValue;
		// NTE jako float
		$this->_csvData[] = $this->getNTE();
		// Store ID
		$this->_csvData[] = $this->_DOMDocument->getElementById('location_name')->nodeValue;


		$address = explode(' ', $this->trim('location_address'));


		// Address
		$this->_csvData[] = $address[0] . ' ' . $address[1] . ' ' . $address[2];
		// Address - city
		$this->_csvData[] = $address[3];
		// Address - state
		$this->_csvData[] = substr($address[4], 0, 2);
		// Address - zip code
		$this->_csvData[] = $address[5];
		// Phone
		$this->_csvData[] = $this->getPhone();
	}

	/**
	 * Zapis do pliku CSV
	 * 
	 * @param string $fileName - nazwa pliku CSV do zapisu
	 */
	function toCSV(string $fileName) {
		$file = fopen($fileName, 'w');

		fputcsv($file, $this->_csvData);

		fclose($file);
	}

	/**
	 * Przetwarzanie pola 'scheduled' na datę
	 */
	protected function getScheduled() {
		$date = $this->trim('scheduled_date');

		return (new DateTime($date))
				->format('Y-m-d H:i');
	}

	/**
	 * Przetwarzanie pola 'NTE' na float
	 */
	protected function getNTE() {
		$nte = str_replace(['$', ','], '', $this->_DOMDocument->getElementById('nte')->nodeValue);

		return (float) $nte;
	}

	/**
	 * Przetwarzanie pola 'phone' na float
	 */
	protected function getPhone() {
		$phone = str_replace('-', '', trim($this->_DOMDocument->getElementById('location_phone')->nodeValue));

		return (float) $phone;
	}

	/**
	 * Usuwanie białych znaków
	 */
	private function trim(string $id) {
		return preg_replace('/\s\s+/', ' ', trim($this->_DOMDocument->getElementById($id)->nodeValue));
	}
}