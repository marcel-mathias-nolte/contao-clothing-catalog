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

abstract class ContentClothing extends \ContentElement {

    protected static $autoItemParsed = false;
    public static $strCategory;
    public static $intCategory;
    public static $strColor;
    public static $intColor;
    public static $strMaterial;
    public static $intMaterial;
    public static $arrMatchedItems;


    /**
     * Compile the content element
     */
    public function generate(): string
    {
        if (!self::$autoItemParsed) {
            \Input::setGet($GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'], \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['category']));
            \Input::setGet($GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'], \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['color']));
            \Input::setGet($GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'], \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['material']));

            self::$strCategory = \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['category']);
            self::$strColor = \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['color']);
            self::$strMaterial = \Input::get($GLOBALS['TL_LANG']['MSC']['clothing_properties']['material']);

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

    /**
     * Return the contrast text color to a given hex color
     * @param string $strHexColor
     * @return string
     */
    public static function getContrastColor(string $strHexColor) : string {
        if (strlen($strHexColor) > 0 && $strHexColor[0] == '#') {
            $strHexColor = substr($strHexColor, 1);
        }
        if (strlen($strHexColor) == 3) {
            $strHexColor = $strHexColor[0] . $strHexColor[0] . $strHexColor[1] . $strHexColor[1] . $strHexColor[2] . $strHexColor[2];
        }
        $R1 = hexdec(substr($strHexColor, 0, 2));
        $G1 = hexdec(substr($strHexColor, 2, 2));
        $B1 = hexdec(substr($strHexColor, 4, 2));

        $blackColor = "#000000";
        $R2BlackColor = hexdec(substr($blackColor, 0, 2));
        $G2BlackColor = hexdec(substr($blackColor, 2, 2));
        $B2BlackColor = hexdec(substr($blackColor, 4, 2));

        $L1 = 0.2126 * pow($R1 / 255, 2.2) +
            0.7152 * pow($G1 / 255, 2.2) +
            0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
            0.7152 * pow($G2BlackColor / 255, 2.2) +
            0.0722 * pow($B2BlackColor / 255, 2.2);

        if ($L1 > $L2) {
            $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
        }

        if ($contrastRatio > 5) {
            $color = '#000000';
        } else {
            $color = '#FFFFFF';
        }
        return $color;
    }
}