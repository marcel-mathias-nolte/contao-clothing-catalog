<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

namespace MarcelMathiasNolte\ContaoClothingCatalogBundle\Elements;

class ContentClothingList extends ContentClothing {

    protected $strTemplate = 'ce_clothing_item_list';
    protected $strListTemplate = 'ce_clothing_item_list_item';

    /**
     * @inheritDoc
     */
    protected function compile()
    {
        // TODO: Implement compile() method.
    }
}