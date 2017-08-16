<?php

class PaginationExtension extends DataExtension
{
    private static $db = array(
        'ItemsPerPage'      =>  'Int'
    );

    private static $defaults = array(
        'ItemsPerPage'      =>  20
    );

    public function updateCMSFields(FieldList $fields)
    {
        if (!$fields->fieldByName('Options')) {

            $fields->insertBefore($right = RightSidebar::create('Options'), 'Root');
        }

        $fields->addFieldToTab('Options', TextField::create('ItemsPerPage', 'Items per page'));
    }
}
