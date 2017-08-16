<?php

use SaltedHerring\Debugger;

class PageJSInjector extends DataExtension
{
    public function KnowledgeHubJSinit($combined_file_name)
    {
        Requirements::combine_files(
            $combined_file_name,
            $assets = array(
                HUB_DIR . '/js/plugins/handlebar-templates.js',
                HUB_DIR . '/js/plugins/ajax-content-fetcher.js',
                HUB_DIR . '/js/knowledge-hub.js'
            )
        );
    }
}
