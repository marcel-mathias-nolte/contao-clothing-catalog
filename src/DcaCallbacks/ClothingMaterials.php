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
use ClothingMaterialModel;
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
class ClothingMaterials extends Backend
{

    private $strAliasPrefix = 'material-';
    private $strTableName = 'tl_clothing_materials';

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
        if (trim($row['color']) != '') {
            $R1 = hexdec(substr($row['color'], 0, 2));
            $G1 = hexdec(substr($row['color'], 2, 2));
            $B1 = hexdec(substr($row['color'], 4, 2));

            $blackColor = "#000000";
            $R2BlackColor = hexdec(substr($blackColor, 0, 2));
            $G2BlackColor = hexdec(substr($blackColor, 2, 2));
            $B2BlackColor = hexdec(substr($blackColor, 4, 2));

            $L1 = 0.2126 * pow($R1 / 255, 2.2) +
                0.7152 * pow($G1 / 255, 2.2) +
                0.0722 * pow($B1 / 255, 2.2);

            $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
                0.7152 * pow($G2BlackColor / 255, 2.2) +
                0.0722 * pow($B2BlackColor / 255, 2.2);

            if ($L1 > $L2) {
                $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
            } else {
                $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
            }

            if ($contrastRatio > 5) {
                $color = '#000000';
            } else {
                $color = '#FFFFFF';
            }

            return '<span style="background-color: #' . $row['color'] . '; color: ' . $color . '; font-weight: bold;">' . $label . '</span>';
        }

        return $label;
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
                $objClothingMaterial = ClothingMaterialModel::findWithDetails($id);

                if ($objClothingMaterial === null)
                {
                    continue;
                }

                $dc->id = $id;
                $dc->activeRecord = $objClothingMaterial;

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
                if ($strAlias == $objClothingMaterial->alias)
                {
                    continue;
                }

                // Initialize the version manager
                $objVersions = new Versions('tl_clothing_materials', $id);
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