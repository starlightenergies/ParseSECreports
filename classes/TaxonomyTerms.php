<?php
namespace JDApp;

//need to store and retrieve from database to initialize

class TaxonomyTerms {

	public array $header_terms = [];
	public array $data_type_terms = [];
	public array $entry_type_terms = [];
	public array $units_type_terms = [];
	public string $CompanyCIK = '';

	public function __construct (int $cik) {

		$this->CompanyCIK = $cik;
		//just a starter set (can read these from file)				//TODO
		array_push($this->header_terms,'cik');
		array_push($this->header_terms, 'entityName');
		array_push($this->header_terms, 'dei');										//label, description, units, USD etc
		array_push($this->header_terms, 'us-gaap');
		array_push($this->data_type_terms,'EntityCommonStockSharesOutstanding');
		array_push($this->data_type_terms,'EntityPublicFloat');
		array_push($this->data_type_terms, 'units');
		array_push($this->data_type_terms, 'shares');
		array_push($this->data_type_terms, 'USD');
		array_push($this->data_type_terms, 'label');
		array_push($this->data_type_terms, 'description');
		array_push($this->data_type_terms, 'sqft');
		array_push($this->data_type_terms, 'pure');
		array_push($this->data_type_terms, 'D');
		array_push($this->data_type_terms, 'facts');
		array_push($this->data_type_terms,'AccountsAndNotesReceivableNet');
		array_push($this->data_type_terms,'AccountsPayableCurrent');
		array_push($this->data_type_terms,'AccountsPayableCurrent');
		array_push($this->data_type_terms,'AccountsReceivableNetCurrent');		//could use identifier re taxonomy type TODO
		array_push($this->data_type_terms,'AccretionAmortizationOfDiscountsAndPremiumsInvestments');


		//need code to pull these terms in from the database
	}

	public function deleteTerm($term,$cik) {

		$old_term = $term;
		//delete term from array
	}

	public function editTerm($term,$cik) {

		$new_term = $term;
		//edit existing term in array
	}

	public function addTerm($term) {

		$this->data_type_terms[] = $term;						//need to test if updated TODO

	}

	public function getDataTerms(): array {
		return $this->data_type_terms;
	}

	public function getHeaderTerms(): array {
		return $this->header_terms;
	}



	public function updateTermsDatabase() {

		//need to update Terms database here or use method in the destruct call TODO
	}

	public function __destruct() {
		// TODO: Implement __destruct() method. or use above
	}

}