<?php

class KnowledgeHeroExtension extends DataExtension
{
    private static $has_one = array(
        'PageHero'          =>  'Image',
        'PageHeroCropped'   =>  'Image'
    );

    public function updateCMSFields(FieldList $fields)
    {
        if (class_exists('CroppableImageField')) {
            $fields->addFieldToTab(
                'Root.Main',
                CroppableImageField::create('PageHeroID', 'Page hero image')->setCropperRatio(16/9)
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
        if (!class_exists('CroppableImageField')) {
            return $this->owner->PageHeroCropped();
        }
        
        if (method_exists($this->owner->PageHero(), 'Cropped')) {
            return $this->owner->PageHero()->Cropped()
        }

        return $this->owner->PageHero();
    }
}
