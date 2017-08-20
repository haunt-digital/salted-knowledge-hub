<?php

class KnowledgeCategory extends DataObject
{
    private static $db = array(
        'Title'         =>  'Varchar(32)'
    );

    private static $has_many = array(
        'Articles'      =>  'KnowledgeArticle'
    );

}
