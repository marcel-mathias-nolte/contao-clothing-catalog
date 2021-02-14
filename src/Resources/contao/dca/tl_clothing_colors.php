<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

$GLOBALS['TL_DCA']['tl_clothing_colors'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'alias' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'flag'                    => 11,
            'fields'                  => array('title'),
            'panelLayout'             => 'search',
            'disableGrouping'         => true
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'label_callback'          => array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingColors', 'generateLabel')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'href'                => 'act=edit',
                'icon'                => 'edit.svg',
            ),
            'delete' => array
            (
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            ),
        )
    ),

    // Select
    'select' => array
    (
        'buttons_callback' => array
        (
            array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingColors', 'addAliasButton')
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,alias;{appearance_legend},color'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'label'                   => array('ID'),
            'search'                  => true,
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'title' => array
        (
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'alias' => array
        (
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'folderalias', 'doNotCopy'=>true, 'maxlength'=>255, 'tl_class'=>'w50 clr'),
            'save_callback' => array
            (
                array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingColors', 'generateAlias')
            ),
            'sql'                     => "varchar(255) BINARY NOT NULL default ''"
        ),
        'color' => array
        (
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>6, 'multiple'=>true, 'size'=>2, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        )
    )
);