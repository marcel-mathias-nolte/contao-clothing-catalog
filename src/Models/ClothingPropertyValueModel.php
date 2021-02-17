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
 * @property string  $pid
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 *
 * @method static ClothingPropertyValueModel|null findById($id, array $opt=array())
 * @method static ClothingPropertyValueModel|null findByPk($id, array $opt=array())
 * @method static ClothingPropertyValueModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingPropertyValueModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingPropertyValueModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingPropertyValueModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingPropertyValueModel|null findOneByPid($val, array $opt=array())
 *
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findByPid($val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingPropertyValueModel[]|ClothingPropertyValueModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByPid($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingPropertyValueModel extends Model
{
    /**
     * Table name
     * @var string
     */
    public static $strTable = 'tl_clothing_property_values';
}

class_alias(ClothingPropertyValueModel::class, 'ClothingPropertyValueModel');
