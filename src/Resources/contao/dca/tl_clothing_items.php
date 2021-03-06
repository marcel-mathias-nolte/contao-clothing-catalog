<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

$GLOBALS['TL_DCA']['tl_clothing_items'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'ptable'                      => 'tl_clothing_categories',
        'enableVersioning'            => true,
        'markAsCopy'                  => 'title',
        'onload_callback'             => array(array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'showAbsoluteCount')),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid' => 'index',
                'alias' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 6,
            'panelLayout'             => 'filter;search'
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'label_callback'          => array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'generateLabel')
        ),
        'global_operations' => array
        (
            'toggleNodes' => array
            (
                'href'                => '&amp;ptg=all',
                'class'               => 'header_toggle',
                'showOnSelect'        => true
            ),
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
                'icon'                => 'edit.svg',
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
            'toggle' => array
            (
                'icon'                => 'visible.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'toggleIcon'),
                'showInHeader'        => true
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
            array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'addAliasButton')
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,alias;{properties_legend},color,materials,properties,options,multiSRC;{published_legend},published,totalPieces'
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
            'foreignKey'              => 'tl_clothing_category.title',
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
            'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'generateAlias')
            ),
            'sql'                     => "varchar(255) BINARY NOT NULL default ''"
        ),
        'color' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'foreignKey'              => 'tl_clothing_colors.title',
            'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'long clr'),
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'materials' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'foreignKey'              => 'tl_clothing_materials.title',
            'relation'                => array('type'=>'hasMany', 'load'=>'lazy'),
            'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class' => 'long clr'),
            'sql'                     => "blob NULL"
        ),
        'published' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy'=>true, 'tl_class' => 'w50'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'totalPieces' => array
        (
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class' => 'w50', 'rgxp' => 'natural', 'maxlength' => 3),
            'sql'                     => "int(10) NOT NULL default 1"
        ),
		'multiSRC' => array
        (
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'orderField'=>'orderSRC', 'files'=>true, 'extensions'=>\Contao\Config::get('validImageTypes'), 'isGallery' => true),
            'sql'                     => "blob NULL"
        ),
        'orderSRC' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['sortOrder'],
            'sql'                     => "blob NULL"
        ),
        'properties' => array
        (
            'exclude'               => true,
            'inputType'             => 'checkboxWizard',
            'options_callback'       => array('MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks\ClothingItems', 'getProperties'),
            'eval'                  => array('multiple'=>true, 'tl_class' => 'long clr'),
            'sql'                   => 'blob NULL'
        ),
        'options' => array
        (
            'sql'                   => 'blob NULL'
        )
    )
);