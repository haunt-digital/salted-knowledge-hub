<?php

class GenericKnowledgeArticle extends KnowledgeArticle
{
    private static $singular_name   =   'Knowledge article';
    private static $plural_name     =   'Knowledge articles';
    private static $hide_ancestor   =   'KnowledgeArticle';
}

class GenericKnowledgeArticle_Controller extends KnowledgeArticle_Controller
{

}
