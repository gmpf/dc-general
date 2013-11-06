<?php
/**
 * PHP version 5
 * @package    generalDriver
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace DcGeneral\DataDefinition\Section\View;

use DcGeneral\DataDefinition\Section\View\Panel\ElementInformationInterface;

interface PanelRowInterface extends \IteratorAggregate
{
	/**
	 * Return the names of the contained panel elements.
	 *
	 * This will return the following for example:
	 * array('filter[prop1]', 'filter[prop2]', 'search', 'limit')
	 *
	 * @return array
	 */
	public function getElements();

	/**
	 * Add an element at the end of the row or - optionally - at the given position.
	 *
	 * If the given position is zero or any other positive value, the element will get placed at the given position.
	 * If the index is negative or greater than the total amount of rows present, the new element will get placed at the
	 * end of the list.
	 *
	 * @param ElementInformationInterface $element
	 *
	 * @param int                         $index Target position for the element.
	 *
	 * @return PanelRowInterface
	 */
	public function addElement(ElementInformationInterface $element, $index = -1);

	/**
	 * Remove the element with the given index (if numeric) or name (if string).
	 *
	 * @param int|string|ElementInformationInterface $indexOrNameOrInstance Element name or numeric index in the row.
	 *
	 * @return PanelRowInterface
	 */
	public function deleteElement($indexOrNameOrInstance);

	/**
	 * Check if the given element instance or an element with the given name is in the row.
	 *
	 * Throws an exception when an invalid value has been passed.
	 *
	 * @param ElementInformationInterface|string $instanceOrName
	 *
	 * @return bool
	 *
	 * @throws \DcGeneral\Exception\DcGeneralInvalidArgumentException
	 */
	public function hasElement($instanceOrName);

	/**
	 * Retrieve the amount of elements.
	 *
	 * @return int
	 */
	public function getCount();

	/**
	 * Retrieve the element with the given index (if numeric) or name (if string).
	 *
	 * @param int|string $indexOrName Element name or numeric index in the row.
	 *
	 * @return ElementInformationInterface
	 */
	public function getElement($indexOrName);

	/**
	 * @return ElementInformationInterface[]
	 */
	public function getIterator();
}