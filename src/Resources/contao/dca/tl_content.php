<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

use Haste\Dca\PaletteManipulator;

/**
 * Extend palettes
 */
/*
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'mobileImage';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'mobileImageCustomSize';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['mobileImage'] = 'mobileImageSrc,mobileImageCustomSize';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['mobileImageCustomSize'] = 'mobileImageSize';

foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $k => $v) {
    if (is_array($v)) {
        continue;
    }

    PaletteManipulator::create()
        ->addField('hideOnDesktop', 'publish_legend', PaletteManipulator::POSITION_APPEND)
        ->addField('hideOnMobile', 'publish_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette($k, 'tl_content');
}

PaletteManipulator::create()
    ->addField('mobileImage', 'singleSRC', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('image', 'tl_content')
    ->applyToSubpalette('addImage', 'tl_content')
    ->applyToSubpalette('useImage', 'tl_content');
*/
/**
 * Add fields
 */
/*
$GLOBALS['TL_DCA']['tl_content']['fields']['hideOnDesktop'] = &$GLOBALS['TL_DCA']['tl_article']['fields']['hideOnDesktop'];
$GLOBALS['TL_DCA']['tl_content']['fields']['hideOnMobile'] = &$GLOBALS['TL_DCA']['tl_article']['fields']['hideOnMobile'];

$GLOBALS['TL_DCA']['tl_content']['fields']['mobileImage'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['mobileImage'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['mobileImageSrc'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['mobileImageSrc'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => [
        'filesOnly' => true,
        'fieldType' => 'radio',
        'mandatory' => true,
        'extensions' => Config::get('validImageTypes'),
        'tl_class' => 'clr',
    ],
    'sql' => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['mobileImageCustomSize'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['mobileImageCustomSize'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['mobileImageSize'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['mobileImageSize'],
    'exclude' => true,
    'inputType' => 'imageSize',
    'options' => System::getImageSizes(),
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => [
        'rgxp' => 'natural',
        'includeBlankOption' => true,
        'nospace' => true,
        'helpwizard' => true,
        'tl_class' => 'clr',
    ],
    'sql' => "varchar(64) NOT NULL default ''",
];
*/