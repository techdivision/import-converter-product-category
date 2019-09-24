<?php

/**
 * TechDivision\Import\Converter\Product\Category\Plugins\ConverterPlugin
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

namespace TechDivision\Import\Converter\Product\Category\Plugins;

use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Plugins\SubjectPlugin;
use TechDivision\Import\Plugins\ExportableTrait;
use TechDivision\Import\Plugins\ExportablePluginInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Subjects\SubjectExecutorInterface;
use TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface;

/**
 * Plugin that updates the categories children count attribute after a successfull category import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */
class ProductCategoryConverterPlugin extends SubjectPlugin implements ExportablePluginInterface
{

    /**
     * The trait that provides export functionality.
     *
     * @var \TechDivision\Import\Plugins\ExportableTrait
     */
    use ExportableTrait;

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\ApplicationInterface                               $application         The application instance
     * @param \TechDivision\Import\Subjects\SubjectExecutorInterface                  $subjectExecutor     The subject executor instance
     * @param \TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface $fileResolverFactory The file resolver instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface                $registryProcessor   The registry processor instance
     */
    public function __construct(
        ApplicationInterface $application,
        SubjectExecutorInterface $subjectExecutor,
        FileResolverFactoryInterface $fileResolverFactory,
        RegistryProcessorInterface $registryProcessor
    ) {

        // call the parent constructor
        parent::__construct($application, $subjectExecutor, $fileResolverFactory);

        // set the subject executor and the file resolver factory
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts()
    {

        // load the artefacts
        $artefacts = $this->registryProcessor->getAttribute(CacheKeys::ARTEFACTS);

        // query whether or not artefacts are available, return an empty array if not
        return is_array($artefacts) ? $artefacts : array();
    }

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    public function getTargetDir()
    {
        return $this->getConfiguration()->getTargetDir();
    }

    /**
     * Reset the array with the artefacts to free the memory.
     *
     * @return void
     */
    public function resetArtefacts()
    {
        $this->registryProcessor->removeAttribute(CacheKeys::ARTEFACTS);
    }
}
