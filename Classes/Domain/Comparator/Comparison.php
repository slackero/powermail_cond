<?php
namespace In2code\PowermailCond\Domain\Comparator;

use In2code\Powermail\Domain\Model\Field;
use In2code\PowermailCond\Domain\Model\Rule;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Comparison
 */
class Comparison {

	/**
	 * @var int
	 */
	protected $operation = 0;

	/**
	 * @param int $operation
	 */
	public function __construct($operation) {
		$this->operation = $operation;
	}

	/**
	 * @param Field $leftField
	 * @param string $valueToMatch
	 * @param Field $rightField
	 * @return bool
	 */
	public function evaluate(Field $leftField, $valueToMatch = '', Field $rightField = NULL) {
		$result = FALSE;
		switch ($this->operation) {
			case Rule::OPERATOR_IS_SET:
				$result = $this->operationIsNotEmpty($leftField->getText());
				break;
			case Rule::OPERATOR_NOT_IS_SET:
				$result = !$this->operationIsNotEmpty($leftField->getText());
				break;
			case Rule::OPERATOR_CONTAINS_VALUE:
				$result = $this->operationContains($leftField->getText(), $valueToMatch);
				break;
			case Rule::OPERATOR_NOT_CONTAINS_VALUE:
				$result = !$this->operationContains($leftField->getText(), $valueToMatch);
				break;
			case Rule::OPERATOR_IS:
				$result = ($leftField->getText() === $valueToMatch);
				break;
			case Rule::OPERATOR_NOT_IS:
				$result = ($leftField->getText() !== $valueToMatch);
				break;
			case Rule::OPERATOR_GREATER_THAN:
				if ($valueToMatch !== '') {
					$result = (((int) $leftField->getText()) > ((int) $valueToMatch));
				}
				break;
			case Rule::OPERATOR_LESS_THAN:
				if ($valueToMatch !== '') {
					$result = (((int) $leftField->getText()) < ((int) $valueToMatch));
				}
				break;
			case Rule::OPERATOR_CONTAINS_VALUE_FROM_FIELD:
				if ($rightField instanceof Field) {
					$result = $this->operationContains($rightField->getText(), $leftField->getText());
				}
				break;
			case Rule::OPERATOR_NOT_CONTAINS_VALUE_FROM_FIELD:
				if ($rightField instanceof Field) {
					$result = !$this->operationContains($rightField->getText(), $leftField->getText());
				}
				break;
		}
		return $result;
	}

	/**
	 * @param $value
	 * @return bool
	 */
	protected function operationIsNotEmpty($value) {
		return !empty($value);
	}

	/**
	 * @param string|array $haystack
	 * @param string|array $needle If array, all elements must be contained in $haystack (OR)
	 * @return bool
	 */
	protected function operationContains($haystack, $needle) {
		if (!$this->operationIsNotEmpty($needle) || !$this->operationIsNotEmpty($haystack)) {
			return FALSE;
		}
		if (strpos($needle, PHP_EOL)) {
			$needle = GeneralUtility::trimExplode(PHP_EOL, $needle);
		}
		if (is_array($needle)) {
			foreach ($needle as $needleString) {
				if ($this->operationIsNotEmpty($needleString)) {
					if (strpos($haystack, $needleString) !== FALSE) {
						return TRUE;
					}
				}
			}
			return FALSE;
		}
		if (is_array($haystack)) {
			return in_array($needle, $haystack);
		}
		return (strpos($haystack, $needle) !== FALSE);
	}
}