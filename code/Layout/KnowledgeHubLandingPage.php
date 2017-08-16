<?php

class KnowledgeHubLandingPage extends Page
{
    private static $description = 'Knowledge hub landing page. You may only create 1 knowledge hub landing page at all times';
    private static $can_be_root = true;
    private static $allowed_children = array('KnowledgeHubGroupPage');

    private static $extensions = array(
        'PaginationExtension'
    );

    public function canCreate( $member = null )
    {
        $result = parent::canCreate($member);
        $test = Versioned::get_by_stage('KnowledgeHubLandingPage', 'Stage')->count() == 0;

        return ($result && $test);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            'Root.PageHeader',
            TextareaField::create('Content', 'Introduction')
        );
        return $fields;
    }

    public function getHeaderContent()
    {
        return new ArrayData(array('Title' => $this->Title, 'Content' => $this->Content));
    }

}

class KnowledgeHubLandingPage_Controller extends Page_Controller
{
    protected $ItemCount = 0;

    public function index()
    {
        $this->ItemCount = KnowledgeArticle::get()->count();
        return $this->renderWith(array('KnowledgeHubLayout', 'Page'));
    }

    public function appendingVars()
    {
        $var_string = '';

        if ($keywords = $this->request->getVar('keywords')) {
            $var_string = '?keywords=' . $keywords;
        }

        return $var_string;
    }
}
