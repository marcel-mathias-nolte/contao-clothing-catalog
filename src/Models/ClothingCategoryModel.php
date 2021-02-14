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

/**
 * Reads and writes clothing categories
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $sorting
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 * @property string  $color
 * @property string  $singleSRC
 *
 * @method static ClothingCategoryModel|null findById($id, array $opt=array())
 * @method static ClothingCategoryModel|null findByPk($id, array $opt=array())
 * @method static ClothingCategoryModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneByPid($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneBySorting($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneByColor($val, array $opt=array())
 * @method static ClothingCategoryModel|null findOneBySingleSRC($val, array $opt=array())
 *
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findByPid($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findBySorting($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findByColor($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findBySingleSRC($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingCategoryModel[]|ClothingCategoryModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByPid($val, array $opt=array())
 * @method static integer countBySorting($val, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByColor($val, array $opt=array())
 * @method static integer countBySingleSRC($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingCategoryModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_clothing_categories';

    /**
     * Find the parent categories of a category
     *
     * @param integer $intId The category's ID
     *
     * @return Collection|ClothingCategoryModel[]|ClothingCategoryModel|null A collection of models or null if there are no parent categories
     */
    public static function findParentsById($intId)
    {
        $arrModels = array();

        while ($intId > 0 && ($objClothingCategory = static::findByPk($intId)) !== null)
        {
            $intId = $objClothingCategory->pid;
            $arrModels[] = $objClothingCategory;
        }

        if (empty($arrModels))
        {
            return null;
        }

        return static::createCollection($arrModels, static::$strTable);
    }

    /**
     * Find the parent categories of a category
     *
     * @param integer $intId The category's ID
     *
     * @return integer[] A collection of integers
     */
    public static function findChildIdsById($intId)
    {
        $arrIds = array($intId);
        $arrToDo = array($intId);

        while (count($arrToDo) > 0)
        {
            $objCategories = static::findByPid(array_pop($arrToDo));
            while ($objCategories->next()) {
                $arrToDo[] = $objCategories->id;
                $arrIds[] = $objCategories->id;
            }
        }

        return $arrIds;
    }
}

class_alias(ClothingCategoryModel::class, 'ClothingCategoryModel');
