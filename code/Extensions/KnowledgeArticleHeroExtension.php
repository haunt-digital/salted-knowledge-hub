<?php

class KnowledgeArticleHeroExtension extends DataExtension
{
    private static $has_one = array(
        'PageHero'      =>  'Image'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Main',
            class_exists('SaltedUploader') ?
            SaltedUploader::create('PageHero', 'Page hero image')->setCropperRatio(16/9) :
            UploadField::create('PageHero', 'Page hero image')
        );
    }
}
