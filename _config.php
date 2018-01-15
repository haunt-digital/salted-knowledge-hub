<?php
define('HUB_DIR', basename(dirname(__FILE__)));

SS_Cache::set_cache_lifetime('KnowledgeArticle', 31536000); // 1 year
SS_Cache::set_cache_lifetime('KnowledgeCategoryList', 31536000); // 1 year
