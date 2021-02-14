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

use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingCategoryModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingColorModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingItemModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingMaterialModel;

abstract class ContentClothingItem extends \ContentElement {

    protected static $autoItemParsed = false;
    public static $strCategory;
    public static $intCategory;
    public static $strColor;
    public static $intColor;
    public static $strMaterial;
    public static $intMaterial;
    public static $arrMatchedItems;

    public function generate()
    {
        if (!self::$autoItemParsed) {
            // @todo: In Sprachdatei
            $urlItemBase = array(
                'category' => 'unter',
                'color' => 'in',
                'material' => 'aus'
            );

            \Input::setGet($urlItemBase['category'], \Input::get($urlItemBase['category']));
            \Input::setGet($urlItemBase['color'], \Input::get($urlItemBase['color']));
            \Input::setGet($urlItemBase['material'], \Input::get($urlItemBase['material']));

            self::$strCategory = \Input::get($urlItemBase['category']);
            self::$strColor = \Input::get($urlItemBase['color']);
            self::$strMaterial = \Input::get($urlItemBase['material']);

            $objCategory = ClothingCategoryModel::findByAlias(self::$strCategory);
            if ($objCategory != null) {
                self::$intCategory = $objCategory->id;
            }

            $objColor = ClothingColorModel::findByAlias(self::$strCategory);
            if ($objColor != null) {
                self::$intColor = $objColor->id;
            }

            $objMaterial = ClothingMaterialModel::findByAlias(self::$strCategory);
            if ($objMaterial != null) {
                self::$intMaterial = $objMaterial->id;
            }

            self::$arrMatchedItems = ClothingItemModel::findPublishedByCategoryAndMaterialAndColor(self::$intCategory, self::$intMaterial, self::$intColor);
            self::$autoItemParsed = true;
        }
        return parent::generate();
    }

    /**
     * Compile the content element
     */
    abstract protected function compile();
}