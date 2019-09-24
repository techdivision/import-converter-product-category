<?php

/**
 * TechDivision\Import\Converter\Product\Category\Listeners\ReduceCategoryListener
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Converter\Product\Category\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Category\Utils\ColumnKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Converter\Product\Category\Observers\ProductCategoryConverterObserver;

/**
 * An listener implementation that reduces and sorts the array with the exported categories.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */
class ReduceCategoryListener extends AbstractListener
{

    /**
     * The array with the categories that has already been processed.
     *
     * @var array
     */
    protected $processed = array();

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

        if ($artefacts = $this->registryProcessor->getAttribute(CacheKeys::ARTEFACTS)) {

            if (isset($artefacts[ProductCategoryConverterObserver::ARTEFACT_TYPE])) {

                $toExport = array();

                $arts = $artefacts[ProductCategoryConverterObserver::ARTEFACT_TYPE];

                foreach ($arts as $categories) {

                    foreach ($categories as $category) {

                        if (in_array($path = $category[ColumnKeys::PATH], $this->processed)) {
                            continue;
                        }

                        $toExport[$path] = $category;

                        $this->processed[] = $path;
                    }
                }

                usort($toExport, function ($a, $b) {
                    return strcmp($a[ColumnKeys::PATH], $b[ColumnKeys::PATH]);
                });

                $artefacts[ProductCategoryConverterObserver::ARTEFACT_TYPE] = array($toExport);

                $this->registryProcessor->setAttribute(CacheKeys::ARTEFACTS, $artefacts, array(), array(), true);
            }
        }
    }
}
