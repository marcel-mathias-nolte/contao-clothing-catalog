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
        $strCssFile = 'bundles/contaoclothingcatalog/css/frontend.scss|static';
        if (!in_array($strCssFile, $GLOBALS['TL_CSS'])) {
            $GLOBALS['TL_CSS'][] = $strCssFile;
        }

        // TODO: Implement compile() method.
    }
}