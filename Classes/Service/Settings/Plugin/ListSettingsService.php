<?php
namespace MageDeveloper\Dataviewer\Service\Settings\Plugin;

use \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ListSettingsService extends PluginSettingsService
{
	/**
	 * Selection Types
	 * @var string
	 */
	const SELECTION_TYPE_DATATYPES 			= "SELECTION_DATATYPE";
	const SELECTION_TYPE_RECORDS			= "SELECTION_RECORDS";
	const SELECTION_TYPE_CREATION_DATE		= "SELECTION_CREATION_DATE";
	const SELECTION_TYPE_CHANGE_DATE		= "SELECTION_CHANGE_DATE";
	const SELECTION_TYPE_FIELD_VALUE_FILTER	= "SELECTION_FIELD_VALUE_FILTER";
	const SELECTION_TYPE_ALL_RECORDS		= "SELECTION_ALL";

	/**
	 * Divide Records Types
	 * @var string
	 */
	const DIVIDE_TYPE_FLUENT				= "FLUENT";
	const DIVIDE_TYPE_DATATYPES				= "DIVIDE_DATATYPES";
	const DIVIDE_TYPE_FIELDS				= "DIVIDE_FIELDS";

	/**
	 * Gets the record selection type
	 *
	 * @return null|string
	 */
	public function getRecordSelectionType()
	{
		return $this->getSettingByCode("record_selection_type");
	}

	/**
	 * Gets the selected datatype ids
	 *
	 * @return string
	 */
	public function getSelectedDatatypeIds()
	{
		return $this->getSettingByCode("datatype_selection");
	}

	/**
	 * Gets the selected record ids
	 *
	 * @return string
	 */
	public function getSelectedRecordIds()
	{
		return $this->getSettingByCode("record_selection");
	}

	/**
	 * Gets the selected record id from the
	 * single record selection field
	 *
	 * @return int
	 */
	public function getSelectedRecordId()
	{
		return (int)$this->getSettingByCode("single_record_selection");
	}

	/**
	 * Gets the selected field id from the
	 * settings
	 *
	 * @return int
	 */
	public function getSelectedFieldId()
	{
		return (int)$this->getSettingByCode("field_selection");
	}

	/**
	 * Gets the template override setting
	 *
	 * @return null|string
	 */
	public function getTemplateOverride()
	{
		return $this->getSettingByCode("template_override");
	}

	/**
	 * Checks if the plugin setting has a template
	 * override
	 *
	 * @return bool
	 */
	public function hasTemplateOverride()
	{
		return ($this->getTemplateOverride() != "");
	}

	/**
	 * Gets the setting for the divide type
	 *
	 * @return null|string
	 */
	public function getDivideType()
	{
		return $this->getSettingByCode("divide_records");
	}

	/**
	 * Gets the default sort field
	 *
	 * @return string
	 */
	public function getSortField()
	{
		return ($this->getSettingByCode("sort_field"))?$this->getSettingByCode("sort_field"):"title";
	}

	/**
	 * Get the sort order
	 *
	 * @return string
	 */
	public function getSortOrder()
	{
		return ($this->getSettingByCode("sort_order"))?$this->getSettingByCode("sort_order"):\TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
	}

	/**
	 * Gets the default value for the
	 * 'Items per Page'-Selection
	 * 
	 * @return int
	 */
	public function getPerPage()
	{
		return (int)$this->getSettingByCode("per_page");
	}

	/**
	 * Gets the date from setting
	 *
	 * @return \Datetime
	 */
	public function getDateFrom()
	{
		$setting = $this->getSettingByCode("date_from_selection");

		// Now
		if (!$setting) $setting = time();

		$datetimeObj = new \Datetime();
		$datetimeObj->setTimestamp($setting);

		return $datetimeObj;
	}

	/**
	 * Gets the date to setting
	 *
	 * @return \Datetime
	 */
	public function getDateTo()
	{
		$setting = $this->getSettingByCode("date_to_selection");

		// Tomorrow
		if (!$setting) $setting = time()+86400;

		$datetimeObj = new \Datetime();
		$datetimeObj->setTimestamp($setting);

		return $datetimeObj;
	}

	/**
	 * Gets the limitation value for records to show
	 *
	 * @return int
	 */
	public function getLimitation()
	{
		return (int)$this->getSettingByCode("number_of_records");
	}

	/**
	 * Gets the attribute filters setting
	 *
	 * @return array
	 */
	public function getFieldValueFilters()
	{
		$settingId = "field_value_filter";
		$setting = $this->getSettingByCode($settingId);
		$filters = array();
		
		if (is_array($setting))
		{
			foreach($setting as $_filterSetting)
				$filters[] = $_filterSetting["filters"];
		}

		return $filters;
	}

	/**
	 * Gets the default orderings from the plugin
	 * settings
	 *
	 * @return array
	 */
	public function getDefaultOrderings()
	{
		$sortField = $this->getSortField();
		return array(
				$sortField => $this->getSortOrder(),
		);
	}

	/**
	 * Gets selected variable ids
	 *
	 * @return array
	 */
	public function getSelectedVariableIds()
	{
		$variables = $this->getSettingByCode("variable_injection");
		return GeneralUtility::trimExplode(",", $variables, true);
	}

	/**
	 * Debug Mode Enabled
	 *
	 * @return bool
	 */
	public function isDebug()
	{
		return (bool)$this->getSettingByCode("debug");
	}

	/**
	 * Sorting settings of this plugin
	 * are forced and not disturbed by 
	 * an other sorting plugin
	 * 
	 * @return bool
	 */
	public function isForcedSorting()
	{
		return (bool)$this->getSettingByCode("force_sorting");
	}
}
