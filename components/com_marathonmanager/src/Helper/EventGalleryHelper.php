<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Helper;



use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use stdClass;

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
            $picturesTree[$folder]['label'] = self::buildLabel($picturesTree[$folder]['name']);
            $picturesTree[$folder]['images'] = $pictures;

        }

        return $picturesTree;
    }

    private static function buildLabel(String $name)
    {
        $label = $name;
        $app = Factory::getApplication();
        $parameters = $app->getParams();
        $labelRules = $parameters->get('gallery_label_rules'); // Configured in the backend by subform field

        foreach ($labelRules as $rule)
        {
            switch ($rule->rule_type)
            {
                case 'removeAllNumbers':
                    $label = preg_replace('/\d/g', '', $label);
                    break;
                case 'removeLeadNumbers':
                    $label = ltrim($label, '0..9');
                    break;
                case 'removeTrailNumbers':
                    $label = rtrim($label, '0..9');
                    break;
                case 'before':
                    $label = $rule->rule_string_to_add . $label;
                    break;
                case 'after':
                    $label = $label . $rule->rule_string_to_add;
                    break;
                case 'replace':
                    switch ($rule->rule_string_replace_with)
                    {
                        case 'string':
                            //$modified = str_replace($rule->rule_string_to_find, $rule->rule_string_to_replace, $modified);
                            $label = self::replacer($rule, $rule->rule_string_to_replace, $label);
                            break;
                        case 'nbspace':
                            //$modified = str_replace($rule->rule_string_to_find, '&nbsp;', $modified);
                            $label = self::replacer($rule, '&nbsp;', $label);
                            break;
                        case 'space':
                            //$modified = str_replace($rule->rule_string_to_find, ' ', $modified);
                            $label = self::replacer($rule, ' ', $label);
                            break;
                        case 'break':
                            //$modified = str_replace($rule->rule_string_to_find, '<br>', $modified);
                            $label = self::replacer($rule, '<br>', $label);
                            break;
                        default:
                    };
                    break;
                default:
            }
        }


        return $label;
    }

    /**
     * @param $rule         stdClass      Object containing the ruleset
     * @param $replace      string      HTML String that should be used as replacement
     * @param $string       string      Original / already modified file / foldername
     *
     * @return string
     *
     * @since version 1.5.0
     */
    private static function replacer(stdClass $rule, string $replace, string $string): string
    {
        if ($rule->regexp)
        {
            $value = preg_replace($rule->rule_string_to_find, $replace, $string);
            if (!$value)
            {
                echo '<script>console.warn("%cOrielPro Warning:\n%c' . Text::_('COM_MARATHONMANAGER_REGEX_SYNTAX_ERROR') . ' (' . $rule->rule_string_to_find . ').","font-weight:bold;","font-weight:normal;")</script>';
            }
        }
        else
        {
            $value = str_replace($rule->rule_string_to_find, $replace, $string);
        }

        return $value;
    }
}

