<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

namespace MarcelMathiasNolte\ContaoClothingCatalogBundle;

/**
 * Models
 */

$GLOBALS['TL_MODELS']['tl_clothing_categories'] = Models\ClothingCategoryModel::class;
$GLOBALS['TL_MODELS']['tl_clothing_colors'] = Models\ClothingColorModel::class;
$GLOBALS['TL_MODELS']['tl_clothing_materials'] = Models\ClothingMaterialModel::class;

/**
 * Hooks
 */

/**
 * Backend Modules
 */
if (!isset($GLOBALS['BE_MOD']['clothing_catalog'])) {
    array_insert($GLOBALS['BE_MOD'], 0, array(
        'clothing_catalog' => array()
    ));
}
if (!isset($GLOBALS['BE_MOD']['clothing_catalog']['clothing_catalog_categories'])) {
    array_insert($GLOBALS['BE_MOD']['clothing_catalog'], 3, array(
        'clothing_catalog_categories' => array(
            'tables' => array('tl_clothing_categories')
        ),
        'clothing_catalog_colors' => array(
            'tables' => array('tl_clothing_colors')
        ),
        'clothing_catalog_materials' => array(
            'tables' => array('tl_clothing_materials')
        ),
    ));
}
/**
 * Backend Stylesheet
 */
if( TL_MODE === 'BE' ) {
    $GLOBALS['TL_CSS'][] = 'bundles/contaoclothingcatalog/css/backend.css';
}