<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

namespace MarcelMathiasNolte\ContaoClothingCatalogBundle\Tests;

use PHPUnit\Framework\TestCase;

class ContaoClothingCatalogBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new \MarcelMathiasNolte\ContaoClothingCatalogBundle\ContaoClothingCatalogBundle();

        $this->assertInstanceOf('MarcelMathiasNolte\ContaoClothingCatalogBundle\ContaoClothingCatalogBundle', $bundle);
    }
}
