<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

$GLOBALS['TL_DCA']['tl_clothing_property_values'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'ptable'                      => 'tl_clothing_properties',
        'markAsCopy'                  => 'title',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'alias' => 'index',
                'pid' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('sorting'),
            'panelLayout'             => 'search',
            'headerFields'            => array('title', 'type'),
            'child_record_callback'   => array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingPropertyValues', 'listPropertyValues')
        ),
        'label' => array
        (
            'fields'                  => array('title')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="Elements"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'href'                => 'act=edit',
                'icon'                => 'edit.svg'
            ),
            'copy' => array
            (
                'href'                => 'act=paste&amp;mode=copy',
                'icon'                => 'copy.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()"'
            ),
            'cut' => array
            (
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()"'
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
            array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingPropertyValues', 'addAliasButton')
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,alias,type'
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
        'pid' => array
        (
            'foreignKey'              => 'tl_clothing_properties.title',
            'sql'                     => "int(10) unsigned NOT NULL default 0",
            'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
        ),
        'sorting' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default 0"
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
                array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingPropertyValues', 'generateAlias')
            ),
            'sql'                     => "varchar(255) BINARY NOT NULL default ''"
        )
    )
);