<?php

class ResourceKnowledgeArticle extends KnowledgeArticle
{
    private static $singular_name   =   'Resource';
    private static $plural_name     =   'Resources';

    private static $has_one = array(
        'AttachedFile'      =>  'File'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Attachment',
            UploadField::create(
                'AttachedFile',
                'File for people to download'
            )
        );

        return $fields;
    }
}

class ResourceKnowledgeArticle_Controller extends KnowledgeArticle_Controller
{

}
