<?php

/**
 * clothing catalog for Contao Open Source CMS
 *
 * @package ContaoClothingCatalogBundle
 * @author  Marcel Mathias Nolte
 * @website	https://github.com/marcel-mathias-nolte
 * @license LGPL
 */

namespace MarcelMathiasNolte\ContaoClothingCatalogBundle\Models;

use Contao\Model;
use Contao\Model\Collection;
use Date;

/**
 * Reads and writes clothing categories
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $sorting
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 * @property integer $color
 * @property string  $materials
 * @property string  $singleSRC
 *
 * @method static ClothingItemModel|null findById($id, array $opt=array())
 * @method static ClothingItemModel|null findByPk($id, array $opt=array())
 * @method static ClothingItemModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingItemModel|null findOneByPid($val, array $opt=array())
 * @method static ClothingItemModel|null findOneBySorting($val, array $opt=array())
 * @method static ClothingItemModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingItemModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingItemModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingItemModel|null findOneByColor($val, array $opt=array())
 * @method static ClothingItemModel|null findOneBySingleSRC($val, array $opt=array())
 * @method static ClothingItemModel|null findOneByMaterials($val, array $opt=array())
 *
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByPid($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findBySorting($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByColor($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findBySingleSRC($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findByMaterials($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingItemModel[]|ClothingItemModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByPid($val, array $opt=array())
 * @method static integer countBySorting($val, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByColor($val, array $opt=array())
 * @method static integer countBySingleSRC($val, array $opt=array())
 * @method static integer countByMaterials($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingItemModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_clothing_items';

    private static $arrPropertyCache = array();

    /**
     * Find an item by its ID or alias and its category
     *
     * @param mixed   $varId      The numeric ID or alias name
     * @param integer $intPid     The category ID
     * @param array   $arrOptions An optional options array
     *
     * @return ClothingItemModel|null The model or null if there is no item
     */
    public static function findByIdOrAliasAndPid($varId, $intPid, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = !preg_match('/^[1-9]\d*$/', $varId) ? array("BINARY $t.alias=?") : array("$t.id=?");
        $arrValues = array($varId);

        if ($intPid)
        {
            $arrColumns[] = "$t.pid=?";
            $arrValues[] = $intPid;
        }

        return static::findOneBy($arrColumns, $arrValues, $arrOptions);
    }

    /**
     * Find a published item by its ID or alias and its category
     *
     * @param mixed   $varId      The numeric ID or alias name
     * @param integer $intPid     The category ID
     * @param array   $arrOptions An optional options array
     *
     * @return ClothingItemModel|null The model or null if there is no item
     */
    public static function findPublishedByIdOrAliasAndPid($varId, $intPid, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = !preg_match('/^[1-9]\d*$/', $varId) ? array("BINARY $t.alias=?") : array("$t.id=?");
        $arrValues = array($varId);

        if ($intPid)
        {
            $arrColumns[] = "$t.pid=?";
            $arrValues[] = $intPid;
        }

        if (!static::isPreviewMode($arrOptions))
        {
            $time = Date::floorToMinute();
            $arrColumns[] = "$t.published='1'";
        }

        return static::findOneBy($arrColumns, $arrValues, $arrOptions);
    }

    /**
     * Find a published item by its ID or alias and its category
     *
     * @param mixed   $varId       The numeric ID or alias name
     * @param integer $intCategory category ID
     * @param integer $intColor    color ID
     * @param integer $intMaterial material ID
     * @param array   $arrOptions  An optional options array
     * @param array   $arrProperties  Properties
     *
     * @return array The model or null if there is no item
     */
    public static function findPublishedByCategoryAndMaterialAndColor($intCategory, $intColor, $intMaterial, $arrProperties, array $arrOptions=array())
    {
        $t = static::$strTable;

        if ($intCategory)
        {
            $arrSegments = array();
            foreach (ClothingCategoryModel::findChildIdsById($intCategory) as $categoryId) {
                $arrSegments[] = "$t.pid=?";
                $arrValues[] = $categoryId;
            }
            $arrColumns[] = '(' . implode(' OR ', $arrSegments) . ')';
        }

        if ($intColor) {
            $arrColumns[] = "$t.color=?";
            $arrValues[] = $intColor;
        }

        if ($intMaterial) {
            $arrColumns[] = "$t.materials LIKE ?";
            $arrValues[] = '%"' . $intMaterial . '"%';
        }

        if (!static::isPreviewMode($arrOptions))
        {
            $time = Date::floorToMinute();
            $arrColumns[] = "$t.published='1'";
        }

        // Cache PropertyIds
        if (count(static::$arrPropertyCache) == 0) {
            $objProperties = ClothingPropertyModel::findAll();
            if ($objProperties != null) {
                foreach ($objProperties as $objProperty) {
                    static::$arrPropertyCache[$objProperty->alias] = $objProperty->id;
                }
                if ($objProperty->type == 'select') {
                    $objValues = ClothingPropertyValueModel::findByPid($objProperty->id);
                    if ($objValues != null) {
                        foreach ($objValues as $objValue) {
                            static::$arrPropertyCache[$objProperty->alias.'/'.$objValue->alias] = $objValue->id;
                        }
                    }
                }
            }
        }

        $arrResults = array();
        $objResult = static::findBy($arrColumns, $arrValues, $arrOptions);
        if ($objResult != null) {
            foreach ($objResult as $objRecord) {
                $arrRow = $objRecord->row();
                $arrRow['properties'] = deserialize($arrRow['properties']);
                $arrRow['options'] = deserialize($arrRow['options']);
                foreach ($arrProperties as $property => $value) {
                    if ($value === true && !in_array(static::$arrPropertyCache[$property], $arrRow['properties'])) {
                        continue;
                    } else if (false) {
                        continue;
                    }
                }
                $arrResults[] = (object)$arrRow;
            }
        }

        return $arrResults;
    }

    /**
     * Find a published item by its ID
     *
     * @param integer $intId      The item ID
     * @param array   $arrOptions An optional options array
     *
     * @return ClothingItemModel|null The model or null if there is no published item
     */
    public static function findPublishedById($intId, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = array("$t.id=?");

        if (!static::isPreviewMode($arrOptions))
        {
            $time = Date::floorToMinute();
            $arrColumns[] = "$t.published='1'";
        }

        return static::findOneBy($arrColumns, $intId, $arrOptions);
    }
}

class_alias(ClothingItemModel::class, 'ClothingItemModel');
