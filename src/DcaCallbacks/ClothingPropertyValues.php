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
use ClothingPropertyValueModel;
use DataContainer;
use Exception;
use Image;
use Input;
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
class ClothingPropertyValues extends Backend
{

    private $strAliasPrefix = 'property-';
    private $strTableName = 'tl_clothing_property_values';

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
     * Add the type of input field
     *
     * @param array $arrRow
     *
     * @return string
     */
    public function listPropertyValues($arrRow)
    {
        return $arrRow['title'];
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
                $objClothingPropertyValue = ClothingPropertyValueModel::findWithDetails($id);

                if ($objClothingPropertyValue === null)
                {
                    continue;
                }

                $dc->id = $id;
                $dc->activeRecord = $objClothingPropertyValue;

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
                if ($strAlias == $objClothingPropertyValue->alias)
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
}