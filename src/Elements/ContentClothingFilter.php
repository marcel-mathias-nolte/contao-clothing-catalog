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

        $strCssFile = 'bundles/contaoclothingcatalog/css/frontend.scss|static';
        if (!in_array($strCssFile, $GLOBALS['TL_CSS'])) {
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
            array(
                'title' => $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['all_colors'],
                'color' => false,
                'selected' => !parent::$intColor,
                'href' => $href['href'],
                'resultCount' => $href['resultCount']
           )
        );
        $objColors = ClothingColorModel::findAll(['order' => 'title ASC']);
        if ($objColors != null) {
            foreach ($objColors as $objColor) {
                $color = deserialize($objColor->color);
                $arrData = $objColor->row();
                $arrData['color'] = $color;
                $arrData['fgcolor'] = '#' . $color[0];
                $arrData['selected'] = $objColor->id == parent::$intColor;
                $arrData['bgcolor'] = parent::getContrastColor($arrData['fgcolor']);
                $href = parent::getHrefAndCount('color', $objColor->alias, $objColor->id);
                $arrData['href'] = $href['href'];
                $arrData['resultCount'] = $href['resultCount'];
                $arrColors[] = (object)$arrData;
            }
        }
        $this->Template->colors = $arrColors;

        $href = parent::getHrefAndCount('material', false);
        $arrMaterials = array(
            array(
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
                $arrChildCategories[] = (object)$arrData;
            }
        }
        $this->Template->childCategories = $arrChildCategories;

        $arrCategoryBreadCrumb = array();

        if (parent::$intCategory) {
            $href = parent::getHrefAndCount('category', false);
            $arrData = array(
				'title' => $GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['all_categories'],
				'singleSRC' => false,
                'href' => $href['href'],
                'resultCount' => $href['resultCount']
			);
            $arrCategoryBreadCrumb[] = (object)$arrData;
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
        }
        $this->Template->categoryBreadcrumb = $arrCategoryBreadCrumb;

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
                    $arrPropertiesCheckbox[] = (object)$arrData;
                } else if ($objProperty->type == 'select') {
                    $objValues = ClothingPropertyValueModel::findByPid($objProperty->id, ['order' => 'sorting ASC']);
                    if ($objValues != null) {
                        $totalCount = 0;
                        $arrValues = array();
                        foreach ($objValues as $objValue) {
                            $arrValue = $objValue->row();
                            $href = parent::getHrefAndCount($objProperty->alias, $objValue->alias);
                            $arrValue['selected'] = isset(parent::$arrProperties[$objProperty->alias]) && parent::$arrProperties[$objProperty->alias] == $objValue->alias;
                            $arrValue['href'] = $href['href'];
                            $arrValue['resultCount'] = $href['resultCount'];
                            $totalCount += $href['resultCount'];
                            $arrValues[] = $arrValue;
                        }
                        $href = parent::getHrefAndCount($objProperty->alias, false);
                        $arrData['selected'] = !isset(parent::$arrProperties[$objProperty->alias]);
                        $arrData['href'] = $href['href'];
                        $arrData['resultCount'] = $href['resultCount'];
                        $arrData['totalCount'] = $totalCount;
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