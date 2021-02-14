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

use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingColorModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingMaterialModel;

class ContentClothingItemFilter extends ContentClothingItem {

    protected $strTemplate = 'ce_clothing_item_filter';

    /**
     * @inheritDoc
     */
    protected function compile()
    {
        $arrColors = array();
        $objColors = ClothingColorModel::findAll(['order' => 'title ASC']);
        if ($objColors != null) {
            foreach ($objColors as $objColor) {
                $color = deserialize($objColor->color);
                $arrData = $objColor->row();
                $arrData['color'] = $color;
                $arrData['selected'] = $objColor->id == parent::$intColor;
                $arrColors[] = $arrData;
            }
        }
        $this->Template->colors = $arrColors;

        $arrMaterials = array();
        $objMaterials = ClothingMaterialModel::findAll(['order' => 'title ASC']);
        if ($objMaterials != null) {
            foreach ($objMaterials as $objMaterial) {
                $color = deserialize($objMaterial->color);
                $arrData = $objMaterial->row();
                $arrData['color'] = $color;
                $arrData['selected'] = $objMaterial->id == parent::$intMaterial;
                $arrMaterials[] = $arrData;
            }
        }
        $this->Template->materials = $arrMaterials;

        $arrCategories = array();
        // @todo rekusriv
        $objCategories = ClothingMaterialModel::findAllByPid(0, ['order' => 'sorting ASC']);
        if ($objCategories != null) {
            foreach ($objCategories as $objCategory) {
                $arrData = $objCategory->row();
                $arrData['selected'] = $objCategory->id == parent::$intCategory;
                $arrCategories[] = $arrData;
            }
        }
        $this->Template->categories = $arrCategories;

        $arrChildCategories = array();
        // @todo childCategories
        $objCategories = ClothingMaterialModel::findAllByPid(0, ['order' => 'sorting ASC']);
        if ($objCategories != null) {
            foreach ($objCategories as $objCategory) {
                $arrData = $objCategory->row();
                $arrData['selected'] = $objCategory->id == parent::$intCategory;
                $arrCategories[] = $arrData;
            }
        }
        $this->Template->categories = $arrCategories;
        // TODO: Implement compile() method.
    }
}