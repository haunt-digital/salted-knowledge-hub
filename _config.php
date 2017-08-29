<?php
define('HUB_DIR', basename(dirname(__FILE__)));

SS_Cache::set_cache_lifetime('KnowledgeArticle', 31536000); // 1 year
SS_Cache::set_cache_lifetime('KnowledgeCategoryList', 31536000); // 1 year

HtmlEditorConfig::get('cms')->setOption('theme_advanced_styles',
                    'Tick List=tick-list,
                    Left-edged quote box - blue=left-edged-quote-box blue,
                    Right-edged quote box - green=right-edged-quote-box green,
                    Quoted by=quoted-by');
                    
HtmlEditorConfig::get('cms')->setOption('theme_advanced_blockformats', 'p,h2,h3,h4,h5,h6');
