<?php

class MemberAuthorExtension extends DataExtension
{
    private static $has_one = array(
        'Author'                    =>  'Author'
    );

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (empty($this->owner->AuthorID)) {
            $author                 =   new Author();
            $author->FirstName      =   $this->owner->FirstName;
            $author->Surname        =   $this->owner->Surname;
            $author->write();
            $this->owner->AuthorID  =   $author->ID;
        }
    }
}
