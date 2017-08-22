<?php

use SaltedHerring\Debugger;

class KnowledgeInjector extends DataExtension
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

    public function getKnowledgeHubs()
    {
        if ($hubs_landing = KnowledgeHubLandingPage::get()->first()) {
            $url                =   $this->owner->request->getVar('url');
            $hubs               =   $hubs_landing->AllChildren();
            $hubs_array         =   array(new ArrayData(array(
                                        'Title'     =>  'All',
                                        'Link'      =>  $hubs_landing->Link(),
                                        'isActive'  =>  $hubs_landing->Link() == $url
                                    )));


            foreach ($hubs as $hub) {
                $Link           =   $hub->Link();
                $data           =   array(
                                        'Title'     =>  $hub->Title,
                                        'Link'      =>  $Link,
                                        'isActive'  =>  $Link == $url
                                    );


                $hubs_array[]   =   new ArrayData($data);
            }

            return new ArrayList($hubs_array);
        }

        return null;
    }
}
