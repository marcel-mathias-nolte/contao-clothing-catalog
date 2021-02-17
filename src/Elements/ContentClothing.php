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
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingPropertyModel;

abstract class ContentClothing extends \ContentElement {

    protected static $autoItemParsed = false;
    public static $strCategory;
    public static $intCategory;
    public static $strColor;
    public static $intColor;
    public static $strMaterial;
    public static $intMaterial;
    public static $arrMatchedItems;
    public static $arrProperties = array();

    /**
     * Compile the content element
     */
    public function generate(): string
    {
        if (!self::$autoItemParsed) {
            foreach($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties'] as $label) {
                \Input::setGet($label, \Input::get($label));
            }

            self::$strCategory = \Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['category']);
            self::$strColor = \Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['color']);
            self::$strMaterial = \Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['material']);

            $objCategory = ClothingCategoryModel::findByAlias(self::$strCategory);
            if ($objCategory != null) {
                self::$intCategory = (int)$objCategory->id;
            }

            $objColor = ClothingColorModel::findByAlias(self::$strColor);
            if ($objColor != null) {
                self::$intColor = (int)$objColor->id;
            }

            $objMaterial = ClothingMaterialModel::findByAlias(self::$strMaterial);
            if ($objMaterial != null) {
                self::$intMaterial = (int)$objMaterial->id;
            }

            $objProperties = ClothingPropertyModel::findAll();
            if ($objProperties != null) {
                foreach ($objProperties as $objProperty) {
                    if ($objProperty->type == 'checkbox') {
                        \Input::setGet($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][0] . $objProperty->alias, \Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][0] . $objProperty->alias));
                        if (\Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][0] . $objProperty->alias) == $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][1]) {
                            self::$arrProperties[$objProperty->alias] = true;
                        }
                    } else if ($objProperty->type == 'select') {
                        \Input::setGet($objProperty->alias, \Input::get($objProperty->alias));
                        $strValue = \Input::get($objProperty->alias);
                        if (ClothingPropertyModel::isValidValue($objProperty->alias, $strValue)) {
                            self::$arrProperties[$objProperty->alias] = $strValue;
                        }
                    }
                }
            }

            self::$arrMatchedItems = ClothingItemModel::findPublishedByCategoryAndMaterialAndColor(self::$intCategory, self::$intMaterial, self::$intColor, self::$arrProperties);
            self::$strCategory = \Input::get($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['category']);
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

    /**
     * Return the href and item count to a given changed property
     * @param string $strProperty;
     * @param string|integer|bool $value;
     * @param integer $id;
     * @return array
     */
    public static function getHrefAndCount(string $strProperty, $value, int $id = 0) : array {
        global $objPage;
        $arrUrlParts = array('');
        if ($strProperty == 'category') {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['category'] . '/' . $value;
        } else if (static::$intCategory > 0) {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['category'] . '/' . static::$strCategory;
        }
        if ($strProperty == 'color') {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['color'] . '/' . $value;
        } else if (static::$intColor > 0) {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['color'] . '/' . static::$strColor;
        }
        if ($strProperty == 'material') {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['material'] . '/' . $value;
        } else if (static::$intMaterial > 0) {
            $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['clothing_properties']['material'] . '/' . static::$strMaterial;
        }
        $properties = static::$arrProperties;
        if ($strProperty != 'category' && $strProperty != 'color' && $strProperty != 'material') {
            $objProperty = ClothingPropertyModel::findByAlias($strProperty);
            if ($objProperty != null) {
                if ($objProperty->type == 'checkbox') {
                    if (!$value) {
                        unset($properties[$strProperty]);
                    } else {
                        $properties[$strProperty] = true;
                    }
                } else if ($objProperty->type == 'select') {
                    if (!$value) {
                        unset($properties[$strProperty]);
                    } else {
                        $properties[$strProperty] = $value;
                    }
                }
            }
        }
        if (count($properties) > 0) {
            foreach ($properties as $prop => $value) {
                if ($value === true) {
                    $arrUrlParts[] = $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][0] . $prop . '/' . $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['with_property'][1];
                } else {
                    $arrUrlParts[] = $prop . '/' . $value;
                }
            }
        }
        $arrResult = array(
            'href' => static::generateFrontendUrl($objPage->row(), implode('/', $arrUrlParts)),
            'resultCount' => count(ClothingItemModel::findPublishedByCategoryAndMaterialAndColor($strProperty == 'category' ? $id : parent::$intCategory, $strProperty == 'color' ? $id : parent::$intColor, $strProperty == 'material' ? $id : static::$intMaterial, $properties))
        );
        return $arrResult;
    }
}