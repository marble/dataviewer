<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\String;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CodeFromStringViewHelper extends AbstractViewHelper
{
	/**
	 * Creates an code from a string
	 *
	 * @param string $string String to generate code of
	 * @return string
	 */
	public function render($string)
	{
		return \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($string);
	}
}