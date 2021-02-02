<?php

/**
 * TechDivision\Import\Converter\Product\Category\Observers\Filters\CategoryUpgradeFilter
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
 * Observer that extracts the categories from a product CSV.
 *
 * @author     Tim Wagner <t.wagner@techdivision.com>
 * @copyright  2020 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/import-converter-product-category
 * @link       http://www.techdivision.com
 * @link       http://www.techdivision.com
 * @deprecated Since 9.0.1
 * @see        \TechDivision\Import\Category\Filters\CategoryUpgradeFilter
 */
class CategoryUpgradeFilter implements FilterInterface
{

    /**
     * This method quotes the passed category elements for export usage.
     *
     * The following cases can be handled:
     *
     * - Default Category  > Default Category
     * - Deine/Meine       > "Deine/Meine"
     * - "Unsere"          > """Unsere"""
     * - "Meine/Eure"      > """Meine/Euere"""
     *
     * if (") then + double all (")
     *     - Default Category  > Default Category
     *     - Deine/Meine       > Deine/Meine
     *     - "Unsere"          > ""Unsere""
     *     - "Meine/Eure"      > ""Mein/Eure""
     * if (") || (/) then + surround values with (")
     *     - Default Category  > Default Category
     *     - Deine/Meine       > "Deine/Meine"
     *     - ""Unsere""        > """Unsere"""
     *     - ""Meine/Eure""    > """Meine/Eure"""
     *
     * @param \TechDivision\Import\Observers\ObserverInterface $observer  The subject instance
     * @param array                                            $elements  The array with the elements that has to be filtered
     * @param string                                           $delimiter The delimiter used to explode/implode the elements
     *
     * @return array The filtered elements
     */
    public function filter(ObserverInterface $observer, array $elements, string $delimiter = '/') : array
    {

        // load the character used to enclose columns
        $enclosure = $observer->getSubject()->getConfiguration()->getEnclosure();

        // filter the category elements and upgrade them for expoort purposes
        array_walk($elements, function (&$element) use ($enclosure, $delimiter) {
            // add one quote to each quote
            $element = str_replace($enclosure, str_pad($enclosure, 2, $enclosure), $element);
            // if the element contains the delimiter char OR the enclosur char, surround it with additional quotes
            if (strpos($element, $delimiter) !== false || strpos($element, $enclosure) !== false) {
                $element = $enclosure . $element . $enclosure;
            }
        });

        // return the filtered elements
        return $elements;
    }
}
