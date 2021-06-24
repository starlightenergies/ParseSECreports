<?php
namespace JDApp;
use JDApp\FinancialRecord as Record;
use JDApp\StateMachine as State;
require_once "includes/programDefines.inc";


class Labels {

	public static int $record_id = 1;
	public int $id;

	public string $currentLabel;
	public string $pastLabel;
	public string $loop;
	public object $RecordClass;
	public object $StateClass;

	public function __construct ($header,Record $R, State $S) {
		$this->id = Labels::$record_id++;
		$this->currentLabel = $R->key_name;
		$this->loop = $header;
		$this->RecordClass = $R;
		$this->StateClass = $S;

	}

	public function classifyLabel(): string {

		$result = '';
		$context = $this->loop;
		$current = $this->currentLabel;
		$Record = $this->RecordClass;
		$State = $this->StateClass;
		if ($context == HEADER) {
			switch ($current) {

				case COMPANY_NAME:
					$Record->company_name = $Record->key_value;
					$header[$Record->key_name] = $Record->key_value;
					$Record->header[] = $header;
					$Record->key_name = '';
					$Record->key_value = '';
					$result = COMPANY_NAME;
					break;
				case DOC_ENTITY_TYPE:
					$result = DOC_ENTITY_TYPE;
					break;
				case DATALABEL:
					break;
				case DESCRIPLABEL:
					break;
				case FACTS:
					$State->braces -= 1;            //put in to address diff style in intuit report TODO
					$Record->key_name = '';
					$result = FACTS;
					break;
				case XBRLLABEL:
					break;
				case SEC_ID;
					$Record->cik = $Record->key_value;
					$header[$Record->key_name] = $Record->key_value;
					$Record->header[] = $header;
					$Record->key_name = '';
					$Record->key_value = '';
					$result = SEC_ID;
					break;
				default:

			}
			return $result;
		} elseif ($context == DATA) {

			switch ($current) {
				case DATALABEL:
				case UNITS:
				case DESCRIPLABEL:

			}


		}
	}

}