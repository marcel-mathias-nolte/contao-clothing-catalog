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

class ContentClothingItemDetails extends ContentClothingItem {

    protected $strTemplate = 'ce_clothing_item_details';

    public function generate()
    {

        $urlItemBase = array(
            'category' => 'unter',
            'color' => 'in',
            'material' => 'aus'
        );

        \Input::setGet($urlItemBase['category'], \Input::get($urlItemBase['category']));
        \Input::setGet($urlItemBase['color'], \Input::get($urlItemBase['color']));
        \Input::setGet($urlItemBase['material'], \Input::get($urlItemBase['material']));

        $categoryAlias = \Input::get($urlItemBase['category']);
        $tagAlias = \Input::get($urlItemBase['color']);
        $productAlias = \Input::get($urlItemBase['material']);
        return parent::generate();
    }

    /**
     * @inheritDoc
     */
    protected function compile()
    {
        // TODO: Implement compile() method.
    }
}