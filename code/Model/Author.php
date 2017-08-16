<?php

class Author extends DataObject
{
    private static $db = array(
        'FirstName'     =>  'Varchar(64)',
        'Surname'       =>  'Varchar(64)'
    );

    public function Title()
    {
        return $this->FirstName . (!empty($this->Surname) ? (' ' . $this->Surname) : '');
    }

    public function getTitle()
    {
        return $this->Title();
    }
}
