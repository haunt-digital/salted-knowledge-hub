<?php
use SaltedHerring\SaltedCache;
use SaltedHerring\Debugger;
use SaltedHerring\Utilities;

class KnowledgeHubGroupPage extends Page
{
    private static $can_be_root = false;
    private static $description = 'Knowledge hub group page. To create this, make sure you have a KnowledgeHubLandingPage exists. Please note, a knowledge hub group page will automatically sit under the knowledge hub landing page, regardlessly';

    private static $db = array(
        'AllowedChildTypes'     =>  'Varchar(256)'
    );

    private static $extensions = array(
        'Lumberjack',
        'PaginationExtension'
    );

    private static $allowed_children = array();

    public function canCreate( $member = null )
    {
        $result = parent::canCreate($member);

        $test = Versioned::get_by_stage('KnowledgeHubLandingPage', 'Stage')->count() > 0;

        return ($result && $test);
    }

    public function getCMSFields() {
        $fields         =   parent::getCMSFields();
        $article_types  =   ClassInfo::subclassesFor('KnowledgeArticle');
        unset($article_types['KnowledgeArticle']);
        foreach ($article_types as &$article_type)
        {
            $article_type = $article_type::create()->plural_name();
        }

        $fields->addFieldToTab(
            'Options',
            CheckboxsetField::create(
                'AllowedChildTypes',
                'Allowed child types',
                $article_types
            )
        );

        return $fields;
    }

    public function allowedChildren()
    {
        if (!empty($this->AllowedChildTypes)) {
            $allowed_types  =   explode(',', $this->AllowedChildTypes);
            $type_array     =   array();
            foreach($allowed_types as $allowed_type)
            {
                $type_array[$allowed_type] =   $allowed_type;
            }

            return $allowed_types;
        }

        return parent::allowedChildren();
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (Versioned::get_by_stage('KnowledgeHubLandingPage', 'Stage')->count() > 0) {
            $this->ParentID = Versioned::get_by_stage('KnowledgeHubLandingPage', 'Stage')->first()->ID;
        }
    }

    public function getHeaderContent()
    {
        return $this->Parent()->getHeaderContent();
    }
}

class KnowledgeHubGroupPage_Controller extends Page_Controller
{
    protected $ItemCount = 0;

    public function index()
    {
        $this->ItemCount = $this->AllChildren()->count();
        return $this->customise(['GroupID' => $this->ID])->renderWith(['KnowledgeHubLayout', 'Page']);
    }

    public function appendingVars()
    {
        $vars = [];
        $var_string = '';
        if ($category = $this->request->getVar('category')) {
            $vars['category'] = $category;
        }

        if ($keywords = $this->request->getVar('keywords')) {
            $vars['keywords'] = $keywords;
        }

        if (!empty($vars)) {
            foreach ($vars as $key => $value)
            {
                $var_string .= '&' . $key . '=' . $value;
            }

            $var_string = '?' . ltrim($var_string, '&');
        }

        return $var_string;
    }

    public function getSubCategories()
    {
        $category                           =   $this->request->getVar('category');

        $factory                            =   'KnowledgeCategoryList';

        $query = new SQLQuery();
        $max = $query->setFrom('KnowledgeCategory')->aggregate('MAX("LastEdited")');
        $result = $query->execute();

        foreach ($result as $row) {
            $key = 'KnowledgeCategory_' . strtotime($row['LastEdited']) . '_' . Utilities::sanitise($this->Title, '_');
            break;
        }

        $key                                =   empty($key) ? '0' : $key;

        $data = SaltedCache::read($factory, $key);

        if (empty($data)) {

            $children                       =   $this->AllChildren();
            $data                           =   [];
            $existings                      =   [];
            $active_taken                   =   false;

            foreach ($children as $child)
            {
                if (!empty($child->CategoryID)) {
                    $title  =   $child->Category()->Title;
                    if (empty($existings[$title])) {

                        if (!$active_taken) {
                            $active_taken   =   $title == $category;
                        }

                        $existings[$title]  =   true;

                        $data[]             =   new ArrayData(array(
                                                    'Title'     =>  $title,
                                                    'Link'      =>  $this->Link() . '?category=' . $title,
                                                    'isActive'  =>  $title == $category
                                                ));
                    }
                }
            }

            array_unshift($data, new ArrayData(array(
                                        'Title'     =>  'All',
                                        'Link'      =>  $this->Link(),
                                        'isActive'  =>  !$active_taken
                                    )));

            SaltedCache::save($factory, $key, $data);
        } else {
            $isAll = true;
            foreach ($data as $item)
            {
                if ($item->Title == $category) {
                    $isAll = false;
                    $item->isActive = true;
                }
            }

            if (!$isAll) {
                if (!empty($data)) {
                    $data[0]->isActive = false;
                }
            }
        }

        return new ArrayList($data);
    }

}
