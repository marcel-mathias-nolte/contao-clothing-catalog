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
 * @property string  $color
 *
 * @method static ClothingColorModel|null findById($id, array $opt=array())
 * @method static ClothingColorModel|null findByPk($id, array $opt=array())
 * @method static ClothingColorModel|null findOneBy($col, $val, array $opt=array())
 * @method static ClothingColorModel|null findOneByTstamp($val, array $opt=array())
 * @method static ClothingColorModel|null findOneByTitle($val, array $opt=array())
 * @method static ClothingColorModel|null findOneByAlias($val, array $opt=array())
 * @method static ClothingColorModel|null findOneByColor($val, array $opt=array())
 *
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findByTstamp($val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findByTitle($val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findByAlias($val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findByColor($val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findMultipleByIds($val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findBy($col, $val, array $opt=array())
 * @method static Collection|ClothingColorModel[]|ClothingColorModel|null findAll(array $opt=array())
 *
 * @method static integer countById($id, array $opt=array())
 * @method static integer countByTstamp($val, array $opt=array())
 * @method static integer countByTitle($val, array $opt=array())
 * @method static integer countByAlias($val, array $opt=array())
 * @method static integer countByColor($val, array $opt=array())
 *
 * @author  Marcel Mathias Nolte
 */
class ClothingColorModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_clothing_colors';
}

class_alias(ClothingColorModel::class, 'ClothingColorModel');
