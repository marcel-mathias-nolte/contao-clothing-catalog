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
 * Reads and writes clothing colors
 *
 * @property integer $id
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 * @property string  $type
 *
 * @method static ClothingPropertyModel|null findById($id, array $opt=array())
 * @method static ClothingPropertyModel|null findByPk($id, array $opt=array())
 * @method static ClothingPropertyModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingPropertyModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingPropertyModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingPropertyModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingPropertyModel|null findOneByType($val, array $opt=array())
 *
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findByType($val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingPropertyModel[]|ClothingPropertyModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByType($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingPropertyModel extends Model
{
    /**
     * Table name
     * @var string
     */
    public static $strTable = 'tl_clothing_properties';

    /**
     * Check if the value is valid for this property
     * @param string $property
     * @param string $value
     * @return bool
     */
    public static function isValidValue(string $property, string $value) : bool {
        $objResult = \Database::getInstance()->prepare("SELECT COUNT(*) as amt FROM " . static::$strTable . " a INNER JOIN " . ClothingPropertyValueModel::$strTable . " b ON a.id = b.pid AND a.alias = ? AND b.alias = ?")->execute($property, $value);
        if ($objResult->next() && $objResult->amt > 0) {
            return true;
        }
        return false;
    }
}

class_alias(ClothingPropertyModel::class, 'ClothingPropertyModel');
