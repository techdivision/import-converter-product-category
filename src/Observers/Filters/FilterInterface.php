<?php

/**
 * TechDivision\Import\Converter\Product\Category\Observers\Filters\FilterInterface
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Converter\Product\Category\Observers\Filters;

use TechDivision\Import\Observers\ObserverInterface;

/**
 * Interface for filter implementations.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2020 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import-converter-product-category
 * @link       http://www.techdivision.com
 * @deprecated Since 9.0.1
 * @see        \TechDivision\Import\Category\Filters\FilterInterface
 */
interface FilterInterface
{

    /**
     * This method filters the passed elements.
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer The subject instance
     * @param array                                            $elements The array with the elements that has to be filtered
     *
     * @return array The filtered elements
     */
    public function filter(ObserverInterface $observer, array $elements) : array;
}
