<?php
/**
 * Created by PhpStorm.
 * User: PrasathKumar Sagadevan
 * Date: 07/06/16
 * Time: 15:45.
 */
class LogoHelper
{

    /**
     * Renders the template
     *
     * @param $template
     *
     * @return mixed
     */
    public function render($template = '//base/_logo', $size = 100)
    {
        return Yii::app()->controller->renderPartial(
            $template,
            array(
                'logo' => $this->getLogo(),
                'size' => $size
            ),
            true
        );
    }

    /**
     * Gets the logo from where it might be, either uploaded or locally versioned.
     *
     * @return array
     */
    protected function getLogo()
    {
        if (isset(Yii::app()->params['letter_logo_upload']) && Yii::app()->params['letter_logo_upload']){
            return $this->getUploadedLogo();
        } else {
            return $this->getVersionedLogo();
        }
    }

    /**
     * @return array
     */
    public function getUploadedLogo()
    {
        $logo = array();

        $directory = \Yii::getPathOfAlias('application.runtime');
        $images = glob("$directory/*.{jpg,png,gif}", GLOB_BRACE);

        foreach($images as $image_path) {
            if (strpos($image_path, 'header') !== false) {
                Yii::app()->assetManager->publish($image_path, true);
                $logo['headerLogo'] = $image_path;
            }
            if (strpos($image_path, 'secondary') !== false) {
                Yii::app()->assetManager->publish($image_path, true);
                $logo['secondaryLogo'] = $image_path;
            }
        }

        return $logo;
    }

    /**
     * Get the logo from the repo
     *
     * @return mixed
     */
    protected function getVersionedLogo()
    {
        $path = Yii::app()->basePath . '/assets/img/_print/';
        $url = Yii::app()->assetManager->publish($path, true);
        $logo['headerLogo'] = $url . '/letterhead_Moorfields_NHS.jpg';
        $logo['secondaryLogo'] = $url . '/letterhead_seal.jpg';

        return $logo;
    }
}