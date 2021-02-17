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
use ClothingColorModel;
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
    public function generateLabel($row, $label){
        $image = 'articles';
        $unpublished = ($row['start'] && $row['start'] > time()) || ($row['stop'] && $row['stop'] <= time());

        if ($unpublished || !$row['published'])
        {
            $image .= '_';
        }

        return '<a href="contao/preview.php?page=' . $row['pid'] . '&amp;article=' . ($row['alias'] ?: $row['id']) . '" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['view']) . '" target="_blank">' . Image::getHtml($image . '.svg', '', 'data-icon="' . ($unpublished ? $image : rtrim($image, '_')) . '.svg" data-icon-disabled="' . rtrim($image, '_') . '_.svg"') . '</a> ' . $label;
    }

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

    public function getProperties() {
        $arrValues = array();
        $objProperties = ClothingPropertyModel::findAllByType('checkbox', ['order' => 'title ASC']);
        if ($objProperties != null) {
            foreach ($objProperties as $objProperty) {
                $arrValues[$objProperty->alias] = $objProperty->title;
            }
        }
        return $arrValues;
    }

    public function getOptionValues() {
        return array('a' => 'b');
    }

    public function getOptions() {
        $arrValues = array();
        $objProperties = ClothingPropertyModel::findAllByType('select', ['order' => 'title ASC']);
        if ($objProperties != null) {
            foreach ($objProperties as $objProperty) {
                $arrValues[$objProperty->alias] = $objProperty->title;
            }
        }
        return $arrValues;
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