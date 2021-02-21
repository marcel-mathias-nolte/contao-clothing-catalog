<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

namespace MarcelMathiasNolte\ContaoClothingCatalogBundle\DcaCallbacks;

use Backend;
use Contao\ArrayUtil;
use Contao\Database;
use Contao\File;
use Contao\FilesModel;
use Contao\Message;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingColorModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingItemModel;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingPropertyValueModel;
use DataContainer;
use Exception;
use Image;
use Input;
use MarcelMathiasNolte\ContaoClothingCatalogBundle\Models\ClothingPropertyModel;
use PageModel;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use StringUtil;
use System;
use Versions;
use function in_array;

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingItems extends Backend
{

    private $strAliasPrefix = 'item-';
    private $strTableName = 'tl_clothing_items';

    /**
     * Auto-generate an alias if it has not been set yet
     *
     * @param mixed $varValue
     * @param DataContainer $dc
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $autoAlias = false;

        // Generate an alias if there is none
        if ($varValue == '')
        {
            $autoAlias = true;
            if (!in_array('ContaoSlugBackportBundle', $this->Config->getActiveModules()))
            {
                $varValue = StringUtil::generateAlias($dc->activeRecord->title);
            }
            else
            {
                $objPage = PageModel::findWithDetails($this->Database->prepare("SELECT id FROM tl_page WHERE `type` = 'root' LIMIT 1")->execute()->next()->id);
                $slugOptions = ['locale' => $objPage->language];

                if ($validAliasCharacters = PageModel::findByPk($objPage->id)->validAliasCharacters)
                {
                    $slugOptions['validChars'] = $validAliasCharacters;
                }

                $strSlug = $dc->activeRecord->title;
                $strSlug = StringUtil::stripInsertTags($strSlug);
                $strSlug = StringUtil::restoreBasicEntities($strSlug);
                $strSlug = StringUtil::decodeEntities($strSlug);

                $varValue = System::getContainer()->get('contao.slug.generator')->generate($strSlug, $slugOptions);
            }
        }

        // Add a prefix to reserved names (see #6066)
        if (in_array($varValue, array('top', 'wrapper', 'header', 'container', 'main', 'left', 'right', 'footer')))
        {
            $varValue = $this->strAliasPrefix . $varValue;
        }

        $objAlias = $this->Database->prepare("SELECT id FROM " . $this->strTableName . " WHERE id!=? AND alias=?")
            ->execute($dc->id, $varValue);

        // Check whether the page alias exists
        if ($objAlias->numRows > 1)
        {
            if (!$autoAlias)
            {
                throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }

    /**
     * Generate the list label
     *
     * @param $row
     * @param $label
     * @return var
     */
    public function generateLabel($row, $label) {
        $multiSRC = \Contao\StringUtil::deserialize($row['multiSRC']);
        if (!empty($multiSRC) && \is_array($multiSRC)) {
            $objFiles = FilesModel::findMultipleByUuids($multiSRC);
            if ($objFiles !== null) {
                $images = array();
                while ($objFiles->next()) {
                    if (isset($images[$objFiles->path]) || !file_exists(\Contao\System::getContainer()->getParameter('kernel.project_dir') . '/' . $objFiles->path)) {
                        continue;
                    }
                    if ($objFiles->type == 'file') {
                        $objFile = new File($objFiles->path);
                        if (!$objFile->isImage) {
                            continue;
                        }
                        $images[$objFiles->path] = array
                        (
                            'id' => $objFiles->id,
                            'uuid' => $objFiles->uuid,
                            'name' => $objFile->basename,
                            'singleSRC' => $objFiles->path,
                            'filesModel' => $objFiles->current()
                        );
                        $auxDate[] = $objFile->mtime;
                    } else {
                        $objSubfiles = FilesModel::findByPid($objFiles->uuid, array('order' => 'name'));
                        if ($objSubfiles === null) {
                            continue;
                        }
                        while ($objSubfiles->next()) {
                            if ($objSubfiles->type == 'folder') {
                                continue;
                            }
                            $objFile = new File($objSubfiles->path);
                            if (!$objFile->isImage) {
                                continue;
                            }
                            $images[$objSubfiles->path] = array
                            (
                                'id' => $objSubfiles->id,
                                'uuid' => $objSubfiles->uuid,
                                'name' => $objFile->basename,
                                'singleSRC' => $objSubfiles->path,
                                'filesModel' => $objSubfiles->current()
                            );
                            $auxDate[] = $objFile->mtime;
                        }
                    }
                }

                if (class_exists('ArrayUtil')) {

                    $images = ArrayUtil::sortByOrderField($images, $row['orderSRC']);
                    $images = array_values($images);
                }
                else if ($row['orderSRC'])
                {
                    $tmp = StringUtil::deserialize($row['orderSRC']);
                    if (!empty($tmp) && \is_array($tmp))
                    {
                        $arrOrder = array_map(static function () {}, array_flip($tmp));
                        foreach ($images as $k=>$v)
                        {
                            if (\array_key_exists($v['uuid'], $arrOrder))
                            {
                                $arrOrder[$v['uuid']] = $v;
                                unset($images[$k]);
                            }
                        }
                        if (!empty($images))
                        {
                            $arrOrder = array_merge($arrOrder, array_values($images));
                        }
                        $images = array_values(array_filter($arrOrder));
                        unset($arrOrder);
                    }
                }
                if (count($images) > 0) {
                    return '<div style="background-image: url(\'' . $images[0]['singleSRC'] . '\'); background-size: cover; background-repeat: no-repeat; background-position: center center; width: 100px; height: 100px; float: right;"></div>' . $label . '';
                }
            }
        }
        return '<div style="width: 100px; height: 100px; float: right;"></div>' . $label . '';    }

    /**
     * Automatically generate the aliases
     *
     * @param array         $arrButtons
     * @param DataContainer $dc
     *
     * @return array
     */
    public function addAliasButton($arrButtons, DataContainer $dc)
    {

        // Generate the aliases
        if (isset($_POST['alias']) && Input::post('FORM_SUBMIT') == 'tl_select')
        {
            /** @var SessionInterface $objSession */
            $objSession = System::getContainer()->get('session');

            $session = $objSession->all();
            $ids = $session['CURRENT']['IDS'] ?? array();

            foreach ($ids as $id)
            {
                $objClothingColor = ClothingColorModel::findWithDetails($id);

                if ($objClothingColor === null)
                {
                    continue;
                }

                $dc->id = $id;
                $dc->activeRecord = $objClothingColor;

                $strAlias = '';

                // Generate new alias through save callbacks
                if (is_array($GLOBALS['TL_DCA'][$dc->table]['fields']['alias']['save_callback'] ?? null))
                {
                    foreach ($GLOBALS['TL_DCA'][$dc->table]['fields']['alias']['save_callback'] as $callback)
                    {
                        if (is_array($callback))
                        {
                            $this->import($callback[0]);
                            $strAlias = $this->{$callback[0]}->{$callback[1]}($strAlias, $dc);
                        }
                        elseif (is_callable($callback))
                        {
                            $strAlias = $callback($strAlias, $dc);
                        }
                    }
                }

                // The alias has not changed
                if ($strAlias == $objClothingColor->alias)
                {
                    continue;
                }

                // Initialize the version manager
                $objVersions = new Versions($this->strTableName, $id);
                $objVersions->initialize();

                // Store the new alias
                $this->Database->prepare("UPDATE " . $this->strTableName . " SET alias=? WHERE id=?")
                    ->execute($strAlias, $id);

                // Create a new version
                $objVersions->create();
            }

            $this->redirect($this->getReferer());
        }

        // Add the button
        $arrButtons['alias'] = '<button type="submit" name="alias" id="alias" class="tl_submit" accesskey="a">' . $GLOBALS['TL_LANG']['MSC']['aliasSelected'] . '</button> ';

        return $arrButtons;
    }

    static $arrPropertyValueCache = array();

    public static function applyDcaExtension($table = '')
    {
        if ($table == ClothingItemModel::$strTable) {
            ClothingItems::applyDcaExtension();
            $options = static::getOptions();
            if (count($options) > 0) {
                $palette = '';
                foreach ($options as $alias => $label) {
                    $palette .= ',option_' . $alias;
                    $GLOBALS['TL_DCA']['tl_clothing_items']['fields']['option_' . $alias] = array(
                        'inputType' => 'select',
                        'label' => array($label, ''),
                        'options' => static::getOptionValues($alias),
                        'eval' => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
                        'save_callback' => array(function ($varValue, $dc) {
                            static::$arrPropertyValueCache[substr($dc->field, strlen('option_'))] = $varValue;
                            return $dc->value;
                        }),
                        'load_callback' => array(function ($varValue, $dc) {
                            $field = substr($dc->field, strlen('option_'));
                            $options = deserialize($dc->activeRecord->options);
                            return is_array($options) && isset($options[$field]) ? $options[$field] : '';
                        })
                    );
                }
                $GLOBALS['TL_DCA']['tl_clothing_items']['palettes']['default'] = str_replace(
                    ';{published_legend}',
                    ';{options_legend}' . $palette . ';{published_legend}',
                    $GLOBALS['TL_DCA']['tl_clothing_items']['palettes']['default']
                );
                $GLOBALS['TL_DCA']['tl_clothing_items']['config']['onsubmit_callback'][] = function ($dc) {
                    if (count(static::$arrPropertyValueCache) > 0) {
                        Database::getInstance()->prepare("UPDATE " . ClothingItemModel::$strTable . " SET options = ? WHERE id = ?")->execute(serialize(static::$arrPropertyValueCache), $dc->id);
                    }
                };
            }
        }
    }

    public function getProperties() {
        $arrValues = array();
        $objProperties = ClothingPropertyModel::findByType('checkbox', ['order' => 'title ASC']);
        if ($objProperties != null) {
            foreach ($objProperties as $objProperty) {
                $arrValues[$objProperty->alias] = $objProperty->title;
            }
        }
        return $arrValues;
    }

    protected static $arrOptionValuesCache = false;

    public function getOptionValues(string $alias) {
        if (static::$arrOptionValuesCache === false) {
            $arrValues = array();
            $objLister = \Database::getInstance()->prepare("SELECT a.alias AS grp, b.alias AS alias, b.title AS label FROM " . ClothingPropertyModel::$strTable . " a INNER JOIN " . ClothingPropertyValueModel::$strTable . " b ON a.id = b.pid AND a.type = ? ORDER BY b.sorting")->execute('select');
            while ($objLister->next()) {
                $arrValues[$objLister->grp][$objLister->alias] = $objLister->label;
            }
            static::$arrOptionValuesCache = $arrValues;
        }
        return isset(static::$arrOptionValuesCache[$alias]) ? static::$arrOptionValuesCache[$alias] : array();
    }

    protected static $arrOptionsCache = false;

    public function getOptions() {
        if (static::$arrOptionsCache === false) {
            $arrValues = array();
            $objProperties = ClothingPropertyModel::findByType('select', ['order' => 'title ASC']);
            if ($objProperties != null) {
                foreach ($objProperties as $objProperty) {
                    $arrValues[$objProperty->alias] = $objProperty->title;
                }
            }
            static::$arrOptionsCache = $arrValues;
        }
        return static::$arrOptionsCache;
    }

    public static function showAbsoluteCount($dc) {
        $objResult = Database::getInstance()->execute("SELECT SUM(totalPieces) AS s FROM " . ClothingItemModel::$strTable);
        if ($objResult->next()) {
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['MSC']['CLOTHING_CATALOG']['absoluteCount'], $objResult->s));
        }
    }

    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (Input::get('tid'))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.svg';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
    }

    /**
     * Disable/enable a item
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     *
     * @throws AccessDeniedException
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc)
        {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (is_array($GLOBALS['TL_DCA'][$this->strTableName]['config']['onload_callback'] ?? null))
        {
            foreach ($GLOBALS['TL_DCA'][$this->strTableName]['config']['onload_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        $objRow = $this->Database->prepare("SELECT * FROM " . $this->strTableName . " WHERE id=?")
            ->limit(1)
            ->execute($intId);

        if ($objRow->numRows < 1)
        {
            throw new AccessDeniedException('Invalid ' . $this->strTableName . ' ID "' . $intId . '".');
        }

        // Set the current record
        if ($dc)
        {
            $dc->activeRecord = $objRow;
        }

        $objVersions = new Versions($this->strTableName, $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA'][$this->strTableName]['fields']['published']['save_callback'] ?? null))
        {
            foreach ($GLOBALS['TL_DCA'][$this->strTableName]['fields']['published']['save_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                }
                elseif (is_callable($callback))
                {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE " . $this->strTableName . " SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
            ->execute($intId);

        if ($dc)
        {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (is_array($GLOBALS['TL_DCA'][$this->strTableName]['config']['onsubmit_callback'] ?? null))
        {
            foreach ($GLOBALS['TL_DCA'][$this->strTableName]['config']['onsubmit_callback'] as $callback)
            {
                if (is_array($callback))
                {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                }
                elseif (is_callable($callback))
                {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();

        if ($dc)
        {
            $dc->invalidateCacheTags();
        }
    }
}