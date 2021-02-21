<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

/**
 * Extend palettes
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['clothing_catalog_filter'] = '{type_legend},type,headline;{appearance_legend},size,clothingDetailSize;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
//$GLOBALS['TL_DCA']['tl_content']['palettes']['clothing_catalog_list'] = '{type_legend},type,headline;{appearance_legend},size,clothingDetailPage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
//$GLOBALS['TL_DCA']['tl_content']['palettes']['clothing_catalog_details'] = '{type_legend},type,headline;{appearance_legend},size;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['clothingDetailPage'] = array
(
    'exclude'                 => true,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => array('fieldType'=>'radio', 'mandatory' => true),
    'sql'                     => "int(10) unsigned NOT NULL default 0",
    'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
);
$GLOBALS['TL_DCA']['tl_content']['fields']['clothingDetailSize'] = $GLOBALS['TL_DCA']['tl_content']['fields']['size'];
