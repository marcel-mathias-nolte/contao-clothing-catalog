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
 * Hooks
 */

/**
 * Backend Modules
 */
if (!isset($GLOBALS['BE_MOD']['clothing_catalog'])) {
    \ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, array(
        'clothing_catalog' => array()
    ));
}
if (!isset($GLOBALS['BE_MOD']['clothing_catalog']['clothing_catalog_categories'])) {
    \ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['clothing_catalog'], 3, array(
        'clothing_catalog_categories' => array(
            'tables' => array('tl_clothing_categories')
        ),
    ));
}
