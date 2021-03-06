<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

use MageDeveloper\Dataviewer\Domain\Model\Datatype;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class RecordRepository extends AbstractRepository
{
	/**
	 * Find records by given uids
	 *
	 * @param array $uids
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUids(array $uids, array $storagePids = array())
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();
		
		if(!empty($storagePids))
		{
			$querySettings->setStoragePageIds($storagePids);
			$querySettings->setRespectStoragePage(true);
		}

		return $query->matching(
			$query->in("uid", $uids)
		)->execute();
	}
	
	
	/**
	 * Find records by datatype
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function findByDatatype(Datatype $datatype)
	{
		$query = $this->createQueryWithSettings();

		return $query->matching(
			$query->equals("datatype", $datatype->getUid())
		)->execute();
	}

	/**
	 * Find record by a list of datatype ids
	 * 
	 * @param array $datatypeIds
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByDatatypeIds(array $datatypeIds, array $storagePids)
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);

		return $query->matching(
			$query->in("datatype", $datatypeIds)
		)->execute();
	}


	/**
	 * Find records by a list of record ids
	 * 
	 * @param array $recordIds
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByRecordIds(array $recordIds, array $storagePids)
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);
		
		$orderings = array();
		foreach ($recordIds as $_id)
			$orderings["uid={$_id}"] = QueryInterface::ORDER_DESCENDING;
		
		$query->setOrderings($orderings);

		return $query->matching(
			$query->in("uid", $recordIds)
		)->execute();
	}

	/**
	 * Find records by a creation date range
	 *
	 * @param \Datetime $dateFrom
	 * @param \Datetime $dateTo
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByCreationDateRange(\Datetime $dateFrom, \Datetime $dateTo, array $storagePids)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);

		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("crdate", $dateFrom->getTimestamp()),
				$query->lessThanOrEqual("crdate", $dateTo->getTimestamp())
			)
		)->execute();
	}

	/**
	 * Find records by a record change time range
	 *
	 * @param \Datetime $dateFrom
	 * @param \Datetime $dateTo
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByChangeDateRange(\Datetime $dateFrom, \Datetime $dateTo, array $storagePids)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);

		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("tstamp", $dateFrom->getTimestamp()),
				$query->lessThanOrEqual("tstamp", $dateTo->getTimestamp())
			)
		)->execute();
	}

	/**
	 * Find all records on a set of storage pids
	 * 
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAll($storagePids = array(), $includeHidden = false)
	{
		if (!empty($storagePids))
		{
			$query = $this->createQuery();
			$querySettings = $query->getQuerySettings();
			$querySettings->setStoragePageIds($storagePids);
			$querySettings->setRespectStoragePage(true);
			$querySettings->setIgnoreEnableFields(!$includeHidden);

			$this->setDefaultQuerySettings($querySettings);
			
			return $this->findAll();
		}
	
		return parent::findAll();
	}

	/**
	 * Find records by advanced conditions like
	 * filters, limit
	 * 
	 * @param array $filters
	 * @param int|string $sortField
	 * @param string $sortOrder
	 * @param null|int $limit
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByAdvancedConditions(array $filters = array(), $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING, $limit = null, array $storagePids = array())
	{	
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$query->setQuerySettings($querySettings);
		
		$statement = $this->getStatementByAdvancedConditions($filters, $sortField, $sortOrder, $limit, $storagePids);
		$query->statement($statement);
		$result = $query->execute(true);
		
		return $result;
	}

	/**
	 * Find records by advanced conditions like
	 * filters, limit
	 *
	 * @param array $filters
	 * @param int|string $sortField
	 * @param string $sortOrder
	 * @param null|int $limit
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function getStatementByAdvancedConditions(array $filters = array(), $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING, $limit = null, array $storagePids = array())
	{
		$subSelectOrdering = "";
		if(is_numeric($sortField))
		{
			$fieldId = (int)$sortField;
			$subSelectOrdering = ",";
			$subSelectOrdering .= "(SELECT search FROM tx_dataviewer_domain_model_recordvalue AS SSRV WHERE field = {$fieldId} AND SSRV.record = RECORD.uid) AS sort";
			$sortField = "sort";
		}

		$statement = "";
		$statement .= "SELECT            RECORD.uid,RECORD.title{$subSelectOrdering}"."\r\n";
		$statement .= "FROM              tx_dataviewer_domain_model_record AS RECORD"."\r\n";
		$statement .= "LEFT JOIN         tx_dataviewer_domain_model_recordvalue AS RECORDVALUE"."\r\n";
		$statement .= "ON                RECORDVALUE.record = RECORD.uid"."\r\n";
		$statement .= "WHERE             RECORD.deleted = '0'"."\r\n";
		$statement .= "AND               RECORD.hidden = '0'"."\r\n";
		$statement .= "AND               RECORDVALUE.deleted = '0'"."\r\n";
		$statement .= "AND               RECORDVALUE.hidden = '0'"."\r\n";
		
		
		if(!empty($storagePids))
		{
			$storagePids = implode(",", $storagePids);
			$statement .= "AND               RECORD.pid IN ({$storagePids})"."\r\n";
			$statement .= "AND               RECORDVALUE.pid IN ({$storagePids})"."\r\n";
		}
		

		if(!empty($filters))
			$statement .= $this->_createAdditionalWhereClauseByFilters($filters)."";

		$statement .= "GROUP BY          RECORD.uid"."\r\n";
		$statement .= "ORDER BY          {$sortField} {$sortOrder}"."\r\n";

		if (!is_null($limit) && is_numeric($limit) && $limit > 0)
			$statement .= "LIMIT             {$limit}"."\r\n";
			
		return $statement;	
	}
	
	/**
	 * Creates an additional where clause string by
	 * given filters
	 * 
	 * @param array $filters
	 * @return string
	 */
	protected function _createAdditionalWhereClauseByFilters(array $filters)
	{
		$additionalWhereClause = "";

		foreach($filters as $_filter)
		{
			$fieldId 			= $_filter["field_id"];
			$filterCondition 	= $_filter["filter_condition"];
			$filterValue 		= $_filter["field_value"];
			$filterCombination  = $_filter["filter_combination"];
		
			$additionalWhereClause .= $this->_getSqlCondition($fieldId, $filterCondition, $filterValue, $filterCombination)."\r\n";
		}
		
		return $additionalWhereClause;
	}

	/**
	 * @param int $fieldId
	 * @param string $filterCondition
	 * @param mixed $filterValue
	 * @return string
	 */
	protected function _getSqlCondition($fieldId, $filterCondition, $filterValue, $filterCombination = "AND")
	{
		$filterCondition = strtolower($filterCondition);
	
		$searchField = "search";
		$sql = "";
		
		// A selected dataviewer field
		if(is_numeric($fieldId)) 
		{
			$filterCombination = str_pad($filterCombination, 10, " ");
			$sql .= "{$filterCombination}        RECORD.uid IN (SELECT record FROM tx_dataviewer_domain_model_recordvalue WHERE field = {$fieldId} AND ";
		}
		else
		{
			$searchField = "{$filterCombination}               ({$fieldId}";
		}
		/*
		eq			=			'{$var}'					->equals
		neq			!=			'{$var}'					->logicalNot->equals
		like		LIKE		'%{$var}%'					->like
		nlike		NOT LIKE	'%{$var}%'					->logicalNot->like
		in			IN			([trimExplode]{$var})		->in
		nin			NOT IN		([trimExplode]{$var})		->logicalNot->in
		gt			>			{(int)$var}					->greaterThan
		lt			<			{(int)$var}					->lessThan
		gte			>=			{(int)$var}					->greaterThanOrEqual
		lte			<=			{(int)$var}					->lessThanOrEqual
		*/

		//$filterValue =  $GLOBALS['TYPO3_DB']->quoteStr($filterValue, 'tx_dataviewer_domain_model_recordvalue');

		switch($filterCondition)
		{
			case "eq":
				$sql .= "{$searchField} = '{$filterValue}'".")";
				break;
			case "neq":
				$sql .= "{$searchField} != '{$filterValue}'".")";
				break;
			case "like":
				if(strpos($filterValue, "%") === false) $filterValue = "%{$filterValue}%";
				$sql .= "{$searchField} LIKE '{$filterValue}'".")";
				break;
			case "nlike":
				if(strpos($filterValue, "%") === false) $filterValue = "%{$filterValue}%";
				$sql .= "{$searchField} NOT LIKE '{$filterValue}'".")";
				break;
			case "in":
				$sql .= "{$searchField} IN ({$filterValue})".")";
				break;
			case "nin":
				$sql .= "{$searchField} NOT IN ({$filterValue})".")";
				break;
			case "gt":
				$filterValue = (int)$filterValue;
				$sql .= "{$searchField} > {$filterValue}".")";
				break;
			case "lt":
				$filterValue = (int)$filterValue;
				$sql .= "{$searchField} < {$filterValue}".")";
				break;
			case "gte":
				$filterValue = (int)$filterValue;
				$sql .= "{$searchField} >= {$filterValue}".")";
				break;
			case "lte":
				$filterValue = (int)$filterValue;
				$sql .= "{$searchField} <= {$filterValue}".")";
				break;
		}
		
		return $sql;
		
	}

}
