<?php

class Resource extends KnowledgeArticle
{
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

class Resource_Controller extends KnowledgeArticle_Controller
{

}
