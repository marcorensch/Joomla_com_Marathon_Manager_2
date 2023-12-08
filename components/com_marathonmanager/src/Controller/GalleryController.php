<?php

namespace NXD\Component\MarathonManager\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filesystem\Path;
use NXD\Component\MarathonManager\Site\Helper\ThumbnailImage;

class GalleryController extends BaseController
{
    public function createThumbnails()
    {
        // Check if GD Library is available
        if (!ThumbnailImage::checkGDLibrary()) {
            echo new \JResponseJson(null, 'GD Library not available', true);
            return;
        }

        // Check Token
        if (Session::checkToken('post')) {
            // Get the images from the request as array of relative paths
            $images = json_decode($this->input->post->get('images', '', 'raw'));
            $width = $this->input->post->get('width', 200, 'int');
            $quality = $this->input->post->get('quality', 100, 'int');

            $errors = array();
            foreach ($images as $originalImageRelativePath) {
                $thumbnailImage = new ThumbnailImage(JPATH_ROOT . $originalImageRelativePath);
                if ($thumbnailImage->checkOriginal()) {
                    // Use the Joomla Method to fix the pathstring
                    $thumbnailImagePath = Path::clean(JPATH_ROOT . '/cache/com_marathonmanager' . $originalImageRelativePath);
                    if (!$thumbnailImage->createThumbnailIfNotExist($thumbnailImagePath, $width, $quality)) {
                        $errors[] = 'Thumbnail for ' . $originalImageRelativePath . ' not created';
                    }
                } else {
                    $errors[] = 'Original Image ' . $originalImageRelativePath . ' not found';
                }
            }

            if (empty($errors)) {
                echo new \JResponseJson(null, 'Thumbnails created', false);
            } else {
                echo new \JResponseJson($errors, 'Thumbnails created with errors', false);
            }

        } else {
            echo new \JResponseJson(null, 'Invalid Token', true);
        }
    }
}