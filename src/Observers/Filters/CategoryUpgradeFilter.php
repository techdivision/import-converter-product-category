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

/**
 * Observer that extracts the categories from a product CSV.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-converter-product-category
 * @link      http://www.techdivision.com
 */
class CategoryUpgradeFilter
{

    /**
     * This method implodes the passed category elements and quotes it for export usage.
     *
     * The following cases can be handled:
     *
     * - Default Category  > Default Category
     * - Deine/Meine       > "Deine/Meine"
     * - "Unsere"          > """Unsere"""
     * - "Meine/Eure"      > """Meine/Euere"""
     *
     * if (") then + (")
     *     - Default Category  > Default Category
     *     - Deine/Meine       > Deine/Meine
     *     - "Unsere"          > ""Unsere""
     *     - "Meine/Eure"      > ""Mein/Eure""
     * if (") || (/) then + (")
     *     - Default Category  > Default Category
     *     - Deine/Meine       > "Deine/Meine"
     *     - ""Unsere""        > """Unsere"""
     *     - ""Meine/Eure""    > """Meine/Eure"""
     *
     * @param array $elements The array with the elements that has to be imploded
     *
     * @return string The imploded
     */
    public function filter(array $elements) : string
    {

        // load the character used to enclose columns
        $enclosure = $this->getSubject()->getConfiguration()->getEnclosure();

        array_walk($elements, function (&$element) use ($enclosure) {

            $element = str_replace($enclosure, str_pad($enclosure, 2, $enclosure), $element);

            if (strpos($element, '/') !== false || strpos($element, $enclosure) !== false) {
                $element = $enclosure . $element . $enclosure;
            }
        });

        return implode('/', $elements);
    }
}
