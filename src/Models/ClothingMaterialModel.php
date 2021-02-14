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
 * Reads and writes clothing materials
 *
 * @property integer $id
 * @property integer $tstamp
 * @property string  $title
 * @property string  $alias
 * @property string  $color
 * @property string  $singleSRC
 *
 * @method static ClothingMaterialModel|null findById($id, array $opt=array())
 * @method static ClothingMaterialModel|null findByPk($id, array $opt=array())
 * @method static ClothingMaterialModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingMaterialModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingMaterialModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingMaterialModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingMaterialModel|null findOneByColor($val, array $opt=array())
 * @method static ClothingMaterialModel|null findOneBySingleSRC($val, array $opt=array())

 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findByColor($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findBySingleSRC($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingMaterialModel[]|ClothingMaterialModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByColor($val, array $opt=array())
 * @method static integer countBySingleSRC($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingMaterialModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_clothing_materials';
}

class_alias(ClothingMaterialModel::class, 'ClothingMaterialModel');
