<?php
/**
 * PHP version 5
 * @package    DcGeneral
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  The Contao Community Alliance.
 * @license    LGPL.
 * @filesource
 */

namespace DcGeneral\DataDefinition\ModelRelationship;

use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\AndFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\BaseFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\OrFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\PropertyEqualsFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\PropertyGreaterThanFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\PropertyLessThanFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\PropertyValueInFilterBuilder;
use DcGeneral\DataDefinition\ModelRelationship\FilterBuilder\PropertyValueLikeFilterBuilder;
use DcGeneral\Exception\DcGeneralInvalidArgumentException;

/**
 * Handy helper class to generate and manipulate filter arrays.
 *
 * @package DcGeneral\DataDefinition\ModelRelationship
 */
class FilterBuilder
{
	/**
	 * The current filter root (always an AND builder).
	 *
	 * @var AndFilterBuilder
	 */
	protected $filter;
	/**
	 * Flag determining if the current filter is a root filter or parent child filter.
	 *
	 * @var bool
	 */
	protected $isRootFilter;

	/**
	 * Create a new instance.
	 *
	 * @param array $filter Optional base filter array.
	 *
	 * @param bool  $isRoot Flag determining if the current filter is a root filter.
	 *
	 * @throws DcGeneralInvalidArgumentException When an invalid filter array has been passed.
	 */
	public function __construct($filter = array(), $isRoot = false)
	{
		if (!is_array($filter))
		{
			throw new DcGeneralInvalidArgumentException(
				'FilterBuilder needs a valid filter array ' . gettype($filter) . 'given'
			);
		}

		$this->filters      = $this->getBuilderFromArray(array('operation' => 'AND', 'children' => $filter), $this);
		$this->isRootFilter = $isRoot;
	}

	/**
	 * Instantiate the correct builder class from a given filter array.
	 *
	 * @param array         $filter  The filter.
	 *
	 * @param FilterBuilder $builder The builder instance.
	 *
	 * @return BaseFilterBuilder
	 *
	 * @throws DcGeneralInvalidArgumentException When an invalid operation is encountered.
	 */
	public static function getBuilderFromArray($filter, $builder)
	{
		switch ($filter['operation'])
		{
			case 'AND':
				return AndFilterBuilder::fromArray($filter, $builder);
			case 'OR':
				return OrFilterBuilder::fromArray($filter, $builder);
			case '=':
				return PropertyEqualsFilterBuilder::fromArray($filter, $builder);
			case '>':
				return PropertyGreaterThanFilterBuilder::fromArray($filter, $builder);
			case '<':
				return PropertyLessThanFilterBuilder::fromArray($filter, $builder);
			case 'IN':
				return PropertyValueInFilterBuilder::fromArray($filter, $builder);
			case 'LIKE':
				return PropertyValueLikeFilterBuilder::fromArray($filter, $builder);
			default:
		}

		throw new DcGeneralInvalidArgumentException(
			'Invalid operation ' . $filter['operation'] . ' it must be one of: AND, OR, =, >, <, IN, LIKE'
		);
	}

	/**
	 * Create a new instance from an array.
	 *
	 * @param array $filter The initial filter array (optional).
	 *
	 * @return FilterBuilder
	 */
	public static function fromArray($filter = array())
	{
		return new static($filter, false);
	}

	/**
	 * Create a new instance from an array for a root filter.
	 *
	 * @param array $filter The initial filter array (optional).
	 *
	 * @return FilterBuilder
	 */
	public static function fromArrayForRoot($filter = array())
	{
		return new static($filter, true);
	}

	/**
	 * Return the root AND condition.
	 *
	 * @return AndFilterBuilder
	 */
	public function getFilter()
	{
		return $this->filters;
	}

	/**
	 * Determine if this builder is for a root filter or not.
	 *
	 * @return bool
	 */
	public function isRootFilter()
	{
		return $this->isRootFilter;
	}

	/**
	 * Check that the builder is not for a root filter.
	 *
	 * @return FilterBuilder
	 *
	 * @throws DcGeneralInvalidArgumentException When the builder is for an root filter.
	 */
	public function checkNotRoot()
	{
		if (!$this->isRootFilter)
		{
			throw new DcGeneralInvalidArgumentException(
				'ERROR: Filter builder is for an root filter.'
			);
		}

		return $this;
	}

	/**
	 * Return the current filters.
	 *
	 * @return array
	 */
	public function getAllAsArray()
	{
		$array = $this->filters->get();

		return $array['children'];
	}

	/**
	 * Check if an given argument is a valid operation.
	 *
	 * @param string $operation The operation to check.
	 *
	 * @return bool
	 */
	public static function isValidOperation($operation)
	{
		return in_array($operation, array('AND', 'OR', '=', '>', '<', 'IN', 'LIKE'));
	}

	/**
	 * Check that an given argument is a valid operation.
	 *
	 * @param string $operation The operation to check.
	 *
	 * @return FilterBuilder
	 *
	 * @throws DcGeneralInvalidArgumentException When an invalid operation name has been passed.
	 */
	public function checkValidOperation($operation)
	{
		if (!$this->isValidOperation($operation))
		{
			throw new DcGeneralInvalidArgumentException(
				'Invalid operation ' . $operation . ' it must be one of: AND, OR, =, >, <, IN, LIKE'
			);
		}

		return $this;
	}
}
