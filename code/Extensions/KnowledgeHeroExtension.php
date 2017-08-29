<?php

class KnowledgeHeroExtension extends DataExtension
{
    private static $has_one = array(
        'PageHero'          =>  'Image',
        'PageHeroCropped'   =>  'Image'
    );

    public function updateCMSFields(FieldList $fields)
    {
        if (class_exists('SaltedUploader')) {
            $fields->addFieldToTab(
                'Root.Main',
                SaltedUploader::create('PageHero', 'Page hero image')->setCropperRatio(16/9)
            );
        } else {
            $im                 =   new UploadField('PageHero', 'Page hero');
            $fields->addFieldsToTab(
                'Root.PageHero',
                [
                    $im,
                    new CropperField\CropperField(
                        'PageHeroCropped',
                        'Cropping page hero',
                        new CropperField\Adapter\UploadField(
                            $im
                        ),
                        array(
                            'aspect_ratio' 			=> 16/9,
                            'generated_max_width' 	=> 1920
                        )
                    )
                ]
            );
        }
    }

    public function getHeroImage()
    {
        if (!class_exists('SaltedUploader')) {
            return $this->owner->PageHeroCropped();
        }

        return $this->owner->PageHero()->Cropped();
    }
}
