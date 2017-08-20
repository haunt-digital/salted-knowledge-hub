<?php

class DataListJSONExtension extends Extension
{
    public function json()
    {
        $lst                =   $this->owner;
        $json_list          =   array();

        foreach ($lst as $item) {
            $json_list[]    =  $item->cached(true);
        }

        return $json_list;
    }
}
