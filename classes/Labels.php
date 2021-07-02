<?php
namespace JDApp;
use JDApp\FinancialRecord as Record;
use JDApp\StateMachine as State;
require_once "includes/programDefines.inc";
/* MIT LICENSE
Copyright 2021 StarlightEnergies.com
Permission is hereby granted, free of charge, to any person obtaining a copy of this software
and associated documentation files (the "Software"), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish, distribute,
sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	Labels.php
 * @version:    	1.0
 * @lastUpdate:  	2021-06-29
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:    		2021-06-15
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:
 */


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