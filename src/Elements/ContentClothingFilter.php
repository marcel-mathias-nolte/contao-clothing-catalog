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
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingItemModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingMaterialModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingPropertyModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingPropertyValueModel;

class ContentClothingFilter extends ContentClothing {

    protected $strTemplate = 'ce_clothing_item_filter';

    protected $blnFilterEmpty = true;

    public function generate() : string {
        if (TL_MODE == 'BE')
        {
            $this->strTemplate = 'be_wildcard';
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['clothing_catalog_filter'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;

            return $objTemplate->parse();
        }

        $strJsFile = 'bundles/contaoclothingcatalog/js/frontend.js|static';
        if (!isset($GLOBALS['TL_JAVASCRIPT']) || !in_array($strJsFile, $GLOBALS['TL_JAVASCRIPT'])) {
            $GLOBALS['TL_JAVASCRIPT'][] = $strJsFile;
        }

        $strCssFile = 'bundles/contaoclothingcatalog/css/frontend.scss|static';
        if (!isset($GLOBALS['TL_CSS']) || !in_array($strCssFile, $GLOBALS['TL_CSS'])) {
            $GLOBALS['TL_CSS'][] = $strCssFile;
        }

        return parent::generate();
    }

    /**
     * Compile the content element
     */
    protected function compile()
    {
        $href = parent::getHrefAndCount('color', false);
        $arrColors = array(
            (object)array(
                'title' => $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['all_colors'],
                'color' => false,
                'selected' => !parent::$intColor,
                'fgcolor' => '#fff',
                'bgcolor' => '#000',
                'href' => $href['href'],
                'resultCount' => $href['resultCount']
           )
        );
        $objColors = ClothingColorModel::findAll(['order' => 'title ASC']);
        if ($objColors != null) {
            foreach ($objColors as $objColor) {
                $color = deserialize($objColor->color);
                $arrData = $objColor->row();
                if (is_array($color) && count($color) > 1 && $color[0] != '') {
                    $arrData['color'] = $color;
                    $arrData['fgcolor'] = '#' . $color[0];
                    $arrData['bgcolor'] = parent::getContrastColor($arrData['fgcolor']);
                }
                $arrData['colorful'] = !(is_array($color) && count($color) > 1 && $color[0] != '');
                $arrData['selected'] = $objColor->id == parent::$intColor;
                $href = parent::getHrefAndCount('color', $objColor->alias, $objColor->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                if (!$this->blnFilterEmpty || $arrData['resultCount'] > 0 || $arrData['selected'])
                    $arrColors[] = (object)$arrData;
            }
        }
        $this->Template->colors = $arrColors;

        $href = parent::getHrefAndCount('material', false);
        $arrMaterials = array(
            (object)array(
                'title' => $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['all_materials'],
                'color' => false,
                'singleSRC' => false,
                'selected' => !parent::$intMaterial,
                'href' => $href['href'],
                'resultCount' => $href['resultCount']
            )
        );
        $objMaterials = ClothingMaterialModel::findAll(['order' => 'title ASC']);
        if ($objMaterials != null) {
            foreach ($objMaterials as $objMaterial) {
                $color = deserialize($objMaterial->color);
                $arrData = $objMaterial->row();
                $arrData['color'] = $color;
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $arrData['selected'] = $objMaterial->id == parent::$intMaterial;
                $href = parent::getHrefAndCount('material', $objMaterial->alias, $objMaterial->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                if (!$this->blnFilterEmpty || $arrData['resultCount'] > 0 || $arrData['selected'])
                    $arrMaterials[] = (object)$arrData;
            }
        }
        $this->Template->materials = $arrMaterials;

        $arrCategories = array();
        $this->renderCategoryTree(0, $arrCategories);
        $this->Template->categories = $arrCategories;

        $arrChildCategories = array();
        $objCategories = ClothingCategoryModel::findByPid(parent::$intCategory ?? 0, ['order' => 'sorting ASC']);
        if ($objCategories != null) {
            foreach ($objCategories as $objCategory) {
                $color = deserialize($objCategory->color);
                $arrData = $objCategory->row();
                $arrData['color'] = $color;
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $href = parent::getHrefAndCount('category', $objCategory->alias, $objCategory->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                if (!$this->blnFilterEmpty || $arrData['resultCount'] > 0 || $arrData['selected'])
                    $arrChildCategories[] = (object)$arrData;
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
                $href = parent::getHrefAndCount('category', $objCategory->alias, $objCategory->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                $arrCategoryBreadCrumb[] = (object)$arrData;
                $intId = $objCategory->pid;
            }
            $href = parent::getHrefAndCount('category', false);
            $arrData = array(
                'title' => $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['all_categories'],
                'singleSRC' => false,
                'href' => $href['href'],
                'resultCount' => $href['resultCount']
            );
            $arrCategoryBreadCrumb[] = (object)$arrData;
        }
        $this->Template->categoryBreadcrumb = array_reverse($arrCategoryBreadCrumb);

        $arrPropertiesCheckbox = array();
        $arrPropertiesDropDown = array();
        $objProperties = ClothingPropertyModel::findAll(['order' => 'title ASC']);
        if ($objProperties != null) {
            foreach ($objProperties as $objProperty) {
                $arrData = $objProperty->row();
                if ($objProperty->type == 'checkbox') {
                    $href = parent::getHrefAndCount($objProperty->alias, !isset(parent::$arrProperties[$objProperty->alias]));
                    $arrData['selected'] = isset(parent::$arrProperties[$objProperty->alias]);
                    $arrData['href'] = $href['href'];
                    $arrData['resultCount'] = $href['resultCount'];
                    if (!$this->blnFilterEmpty || $arrData['resultCount'] > 0 || $arrData['selected'])
                        $arrPropertiesCheckbox[] = (object)$arrData;
                } else if ($objProperty->type == 'select') {
                    $objValues = ClothingPropertyValueModel::findByPid($objProperty->id, ['order' => 'sorting ASC']);
                    if ($objValues != null) {
                        $totalCount = 0;
                        $anySelected = false;
                        $arrValues = array();
                        foreach ($objValues as $objValue) {
                            $arrValue = $objValue->row();
                            $href = parent::getHrefAndCount($objProperty->alias, $objValue->alias);
                            $arrValue['selected'] = isset(parent::$arrProperties[$objProperty->alias]) && parent::$arrProperties[$objProperty->alias] == $objValue->alias;
                            $arrValue['href'] = $href['href'];
                            $arrValue['resultCount'] = $href['resultCount'];
                            $totalCount += $href['resultCount'];
                            $anySelected |= $arrValue['selected'];
                            $arrValues[] = (object)$arrValue;
                        }
                        $arrData['values'] = $arrValues;
                        $href = parent::getHrefAndCount($objProperty->alias, false);
                        $arrData['selected'] = !isset(parent::$arrProperties[$objProperty->alias]);
                        $arrData['href'] = $href['href'];
                        $arrData['resultCount'] = $href['resultCount'];
                        $arrData['totalCount'] = $totalCount;
                        if (!$this->blnFilterEmpty || $totalCount > 0 || $anySelected)
                            $arrPropertiesDropDown[] = (object)$arrData;
                    }
                }
            }
        }
        $this->Template->checkboxes = $arrPropertiesCheckbox;
        $this->Template->dropdowns = $arrPropertiesDropDown;

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

        $size = deserialize($this->clothingDetailSize);
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
        $this->Template->imgParamsDetail = $imgParams;
        $this->Template->items = parent::$arrMatchedItems;
    }

    /**
     * Render a category level
     * @param int $intPid
     * @param array $arrItems
     * @param int $intLevel
     */
    protected function renderCategoryTree(int $intPid, array &$arrItems, int $intLevel = 0) {
        global $objPage;
        $objCategories = ClothingCategoryModel::findByPid($intPid, ['order' => 'sorting ASC']);
        if ($objCategories != null) {
            foreach ($objCategories as $objCategory) {
                $color = deserialize($objCategory->color);
                $arrData = $objCategory->row();
                $arrData['color'] = $color;
                $objFile = \FilesModel::findByUuid($arrData['singleSRC']);
                $arrData['singleSRC'] = $objFile != null ? (object)$objFile->row() : false;
                $arrData['selected'] = $objCategory->id == parent::$intCategory;
                $arrData['level'] = $intLevel;
                $href = parent::getHrefAndCount('category', $objCategory->alias, $objCategory->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                $arrItems[] = (object)$arrData;
                $this->renderCategoryTree($objCategory->id, $arrItems, $intLevel + 1);
            }
        }
    }
}