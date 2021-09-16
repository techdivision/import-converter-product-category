<?php

/**
 * TechDivision\Import\Converter\Product\Category\Listeners\ReduceCategoryListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Converter\Product\Category\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Converter\Product\Category\Observers\ProductToCategoryConverterObserver;

/**
 * An listener implementation that reduces and sorts the array with the exported categories.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */
class ReduceCategoryListener extends AbstractListener
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the listener with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // try to load the availalbe artefacts from the registry processor
        if ($artefacts = $this->registryProcessor->getAttribute(CacheKeys::ARTEFACTS)) {
            // query whether or not categories are available
            if (isset($artefacts[ProductToCategoryConverterObserver::ARTEFACT_TYPE])) {
                // initialize the array for the sorted und merged categories
                $toExport = array();

                // load the categories from the artefacts
                $arts = $artefacts[ProductToCategoryConverterObserver::ARTEFACT_TYPE];
                // iterate over the categories
                foreach ($arts as $categories) {
                    foreach ($categories as $category) {
                        // load the category's path
                        $path = $category[ColumnKeys::PATH];
                        // query whether or not the category has already been processed
                        if (isset($toExport[$path])) {
                            continue;
                        }

                        // if not, add it to the array
                        $toExport[$path] = $category;
                    }
                }

                // sort the categories
                usort($toExport, function ($a, $b) {
                    return strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]);
                });

                // replace them in the array with the artefacts
                $artefacts[ProductToCategoryConverterObserver::ARTEFACT_TYPE] = array($toExport);
                // override the old artefacts
                $this->registryProcessor->setAttribute(CacheKeys::ARTEFACTS, $artefacts, array(), array(), true);
            }
        }
    }
}
