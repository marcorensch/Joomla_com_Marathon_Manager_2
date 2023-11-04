<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Helper;



use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Image\Image;

\defined('_JEXEC') or die;

class EventGalleryHelper{
    public static function getPictures(String $pathInsideImagesFolder):array
    {

        $path = JPATH_ROOT . '/images/' . $pathInsideImagesFolder;
        if(Folder::exists($path)){
            $folders = Folder::folders($path, '', true, true);
            $picturesTree = self::getPicturesTree($folders);
            return $picturesTree;
        }else{
            return array();
        }

    }

    private static function getPicturesTree(Array $folders):array
    {
        $fileFormats = '\.jpg|\.jpeg|\.png|\.webp';
        $fileFormats .= "|" . strtoupper($fileFormats);
        $picturesTree = array();
        foreach ($folders as $folder){
            $pictures = Folder::files($folder, $fileFormats, false, true);

            foreach ($pictures as &$picture){
                $picture = str_replace(JPATH_ROOT ."/", '', $picture);
            }

            $picturesTree[$folder]['name'] = basename($folder);
            $picturesTree[$folder]['images'] = $pictures;

        }

        return $picturesTree;
    }
}

