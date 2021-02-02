<?php

/**
 * TechDivision\Import\Converter\Product\Category\Observers\ProductToCategoryConverterObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Converter\Product\Category\Observers;

use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Category\Filters\FilterInterface;
use TechDivision\Import\Converter\Observers\AbstractConverterObserver;

/**
 * Observer that extracts the categories from a product CSV.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */
class ProductToCategoryConverterObserver extends AbstractConverterObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'category-import';

    /**
     * The upgrade filter instance.
     *
     * @var \TechDivision\Import\Category\Filters\FilterInterface
     */
    private $upgradeFilter;

    /**
     * Initialize the observer with the category upgrade filter instance.
     *
     * @param \TechDivision\Import\Category\Filters\FilterInterface $upgradeFilter The upgrade filter instance
     */
    public function __construct(FilterInterface $upgradeFilter)
    {
        $this->upgradeFilter = $upgradeFilter;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // load and extract the categories from the CSV file
        if ($paths = $this->getValue(ColumnKeys::CATEGORIES, array(), array($this, 'explode'))) {
            // initialize the array for the artefacts
            $artefacts = array();

            // create a tree of categories that has to be created
            foreach ($paths as $path) {
                // explode the category elements
                $elements = $this->explode($path, '/');
                // iterate over the category elements, starting from the root one
                for ($i = 0; $i < sizeof($elements); $i++) {
                    // implode the category
                    $cat = implode('/', $cats = array_slice($elements, 0, $i + 1));
                    // and query if it already exists
                    if ($this->hasCategoryByPath($cat)) {
                        continue;
                    }

                    // if not, create a new artefact
                    $artefacts[] = $this->exportCategory($cats);
                }
            }

            // append the artefacts
            $this->addArtefacts($artefacts);
        }
    }

    /**
     * Create and return a new category from the passed path.
     *
     * @param array $elements The array with the category elements that has to be filtered
     *
     * @return array The category artefact
     */
    protected function exportCategory(array $elements) : array
    {

        // extract the category name from the array (the last element)
        $name = $elements[sizeof($elements) - 1];

        // upgrade and explode the catgory elements to load the last element which is the name
        $path = implode('/', $this->upgradeFilter->filter($this->subject, $elements));

        // create and return the category
        return  $this->newArtefact(
            array(
                ColumnKeys::ATTRIBUTE_SET_CODE => 'Default',
                ColumnKeys::STORE_VIEW_CODE    => $this->getValue(ColumnKeys::STORE_VIEW_CODE),
                ColumnKeys::PATH               => $path,
                ColumnKeys::NAME               => $name,
                ColumnKeys::URL_KEY            => null,
                ColumnKeys::IS_ACTIVE          => 1,
                ColumnKeys::IS_ANCHOR          => 1,
                ColumnKeys::INCLUDE_IN_MENU    => 1
            ),
            array(
                ColumnKeys::ATTRIBUTE_SET_CODE => null,
                ColumnKeys::STORE_VIEW_CODE    => ColumnKeys::STORE_VIEW_CODE,
                ColumnKeys::PATH               => ColumnKeys::CATEGORIES,
                ColumnKeys::NAME               => ColumnKeys::CATEGORIES,
                ColumnKeys::URL_KEY            => ColumnKeys::URL_KEY,
                ColumnKeys::IS_ACTIVE          => null,
                ColumnKeys::IS_ANCHOR          => null,
                ColumnKeys::INCLUDE_IN_MENU    => null
            )
        );
    }

    /**
     * Query's whether or not the category with the passed path is available or not.
     *
     * @param string $path The path of the category to query
     *
     * @return boolean TRUE if the category is available, else FALSE
     */
    protected function hasCategoryByPath($path)
    {
        return $this->getSubject()->hasCategoryByPath($path);
    }

    /**
     * Queries whether or not artefacts for the passed type and entity ID are available.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return boolean TRUE if artefacts are available, else FALSE
     */
    protected function hasArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->hasArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Return the artefacts for the passed type and entity ID.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return array The array with the artefacts
     * @throws \Exception Is thrown, if no artefacts are available
     */
    protected function getArtefactsByTypeAndEntityId($type, $entityId)
    {
        return $this->getSubject()->getArtefactsByTypeAndEntityId($type, $entityId);
    }

    /**
     * Create's and return's a new empty artefact entity.
     *
     * @param array $columns             The array with the column data
     * @param array $originalColumnNames The array with a mapping from the old to the new column names
     *
     * @return array The new artefact entity
     */
    protected function newArtefact(array $columns, array $originalColumnNames)
    {
        return $this->getSubject()->newArtefact($columns, $originalColumnNames);
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Media\Subjects\MediaSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(ProductToCategoryConverterObserver::ARTEFACT_TYPE, $artefacts);
    }
}
