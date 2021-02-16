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

use Contao\ContentElement;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingCategoryModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingColorModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingMaterialModel;

class ContentClothingFilter extends ContentClothing {

    protected $strTemplate = 'ce_clothing_item_filter';

    public function generate() {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['clothing_catalog_filter'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Compile the content element
     */
    protected function compile()
    {
        global $objPage;

        $arrColors = array();
        $objColors = ClothingColorModel::findAll(['order' => 'title ASC']);
        if ($objColors != null) {
            foreach ($objColors as $objColor) {
                $color = deserialize($objColor->color);
                $arrData = $objColor->row();
                $arrData['color'] = $color;
                $arrData['fgcolor'] = '#' . $color[0];
                $arrData['selected'] = $objColor->id == parent::$intColor;
                $arrData['href'] = $this->generateFrontendUrl($objPage->row(), (parent::$intCategory > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'] . '/' . parent::$strCategory : '') . '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'] . '/' . $objColor->alias . (parent::$intMaterial > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'] . '/' . parent::$strMaterial : ''));
                $arrData['bgcolor'] = parent::getContrastColor($arrData['fgcolor']);
                $arrColors[] = (object)$arrData;
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
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $arrData['selected'] = $objMaterial->id == parent::$intMaterial;
                $arrData['href'] = $this->generateFrontendUrl($objPage->row(), (parent::$intCategory > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'] . '/' . parent::$strCategory : '') . (parent::$intColor > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'] . '/' . parent::$strColor : '') . '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'] . '/' . $objMaterial->alias);
                $arrMaterials[] = (object)$arrData;
            }
        }
        $this->Template->materials = $arrMaterials;

        $arrCategories = array();
        $this->renderCategoryTree(0, $arrCategories);
        $this->Template->categories = $arrCategories;

        $arrChildCategories = array();
        if (parent::$intCategory) {
            $objCategories = ClothingCategoryModel::findAllByPid(parent::$intCategory, ['order' => 'sorting ASC']);
            if ($objCategories != null) {
                foreach ($objCategories as $objCategory) {
                    $color = deserialize($objCategory->color);
                    $arrData = $objCategory->row();
                    $arrData['color'] = $color;
                    $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                    $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                    $arrData['href'] = $this->generateFrontendUrl($objPage->row(), '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'] . '/' . $objCategory->alias . (parent::$intColor > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'] . '/' . parent::$strColor : '') . (parent::$intMaterial > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'] . '/' . parent::$strMaterial : ''));
                    $arrChildCategories[] = (object)$arrData;
                }
            }
        }
        $this->Template->childCategories = $arrChildCategories;

        $arrCategoryBreadCrumb = array();
        if (parent::$intCategory) {
            $intId = parent::$intCategory;
            while ($intId && $objCategory = ClothingCategoryModel::findByPk($intId)) {
                $color = deserialize($objCategory->color);
                $arrData = $objCategory->row();
                $arrData['color'] = $color;
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $arrData['href'] = $this->generateFrontendUrl($objPage->row(), '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'] . '/' . $objCategory->alias . (parent::$intColor > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'] . '/' . parent::$strColor : '') . (parent::$intMaterial > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'] . '/' . parent::$strMaterial : ''));
                $arrCategoryBreadCrumb[] = (object)$arrData;
                $intId = $objCategory->pid;
            }
        }
        array_reverse($arrCategoryBreadCrumb);
        $this->Template->categoryBreadcrumb = $arrCategoryBreadCrumb;

        $this->Template->resultCount = count(parent::$arrMatchedItems);

        $size = deserialize($this->size);
        $imgParams = array();
        if (is_array($size)) {
            if ($size[0]) {
                $imgParams[] = 'width=' . $size[0];
            }
            if ($size[1]) {
                $imgParams[] = 'height=' . $size[1];
            }
            if ($size[2]) {
                $imgParams[] = 'mode=' . $size[2];
            }
        }
        if (count($imgParams) > 0) {
            $imgParams = '?' . implode('&', $imgParams);
        } else {
            $imgParams = '';
        }
        $this->Template->imgParams = $imgParams;
    }

    /**
     * Render a category level
     * @param int $intPid
     * @param array $arrItems
     * @param int $intLevel
     */
    protected function renderCategoryTree(int $intPid, array &$arrItems, int $intLevel = 0) {
        global $objPage;
        $objCategories = ClothingCategoryModel::findAllByPid($intPid, ['order' => 'sorting ASC']);
        if ($objCategories != null) {
            foreach ($objCategories as $objCategory) {
                $color = deserialize($objCategory->color);
                $arrData = $objCategory->row();
                $arrData['color'] = $color;
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $arrData['selected'] = $objCategory->id == parent::$intCategory;
                $arrData['level'] = $intLevel;
                $arrData['href'] = $this->generateFrontendUrl($objPage->row(), '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['category'] . '/' . $objCategory->alias . (parent::$intColor > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['color'] . '/' . parent::$strColor : '') . (parent::$intMaterial > 0 ? '/' . $GLOBALS['TL_LANG']['MSC']['clothing_properties']['material'] . '/' . parent::$strMaterial : ''));
                $arrItems[] = (object)$arrData;
                $this->renderCategoryTree($objCategory->id, $arrItems, $intLevel + 1);
            }
        }
    }
}