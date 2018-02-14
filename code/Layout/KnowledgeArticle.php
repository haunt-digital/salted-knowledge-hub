<?php
use SaltedHerring\Debugger;
use SaltedHerring\SaltedCache;
use SaltedHerring\Grid;
use SaltedHerring\Utilities;
class KnowledgeArticle extends Page
{
    protected $AddThis              =   true;
    protected $isArticle            =   true;
    private static $singular_name   =   'Knowledge article';
    private static $plural_name     =   'Knowledge articles';

    private static $show_in_sitetree = false;
    private static $allowed_children = array();
    private static $can_be_root = false;

    private static $create_table_options = array(
        'MySQLDatabase'     =>  'ENGINE=MyISAM'
    );

    private static $indexes = array(
        'SearchFields'      =>  array(
            'type'          =>  'fulltext',
            'name'          =>  'SearchFields',
            'value'         =>  '"Title", "Content", "MetaKeywords"'
        )
    );

    private static $searchable_fields = array(
        'Title',
        'Content',
        'MetaKeywords'
    );

    private static $db = array(
        'Title'             =>  'Varchar(255)',
        'Content'           =>  'HTMLText',
        'MetaKeywords'      =>  'Varchar(256)',
        'PublishDate'       =>  'Date',
        'ExpiryDate'        =>  'SS_Datetime',
        'RemoveWhenExpire'  =>  'Boolean',
        'TileLabel'         =>  'Varchar(128)',
        'Excerpt'           =>  'Text'
    );

    /**
     * Defines extension names and parameters to be applied
     * to this object upon construction.
     * @var array
     */
    private static $extensions = array(
        'KnowledgeHeroExtension'
    );

    private static $default_sort = array(
        'PublishDate'       =>  'DESC',
        'Created'           =>  'DESC'
    );

    private static $has_one = array(
        'Author'            =>  'Author',
        'PreviewImage'      =>  'Image',
        'CrpdPrevImg'       =>  'Image',
        'Category'          =>  'KnowledgeCategory'
    );

    private static $many_many = array(
        'Related'           =>  'KnowledgeArticle'
    );

    private static $belongs_many_many = array(
        'RelatedTo'         =>  'KnowledgeArticle.RelatedArticles'
    );

    public function populateDefaults()
    {
        $this->PublishDate  =   date("Y-m-d");
        $this->AuthorID     =   Member::currentUserID();
    }

    public function getCMSFields()
    {
        $fields             =   parent::getCMSFields();

        $this->SetupMainTab($fields);
        $this->SetupRelatedTab($fields);

        return $fields;
    }

    private function SetupRelatedTab(&$fields)
    {
        $fields->addFieldToTab(
            'Root.RelatedArticles',
            $grid = Grid::make('Related', 'Related articles', $this->Related(), false, 'GridFieldConfig_RelationEditor')->setDescription('You <strong>CANNOT</strong> create new articles here. Please use the search box to link existing articles. <br />You can only link up to <strong>3</strong> articles.')
        );

        $config = $grid->getConfig();
        $config->removeComponentsByType('GridFieldAddNewButton');

        if ($this->Related()->count() >= 3) {
            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $grid->setConfig($config);
        }
    }

    private function SetupMainTab(&$fields)
    {
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'TileLabel',
                'Tile label'
            )->setDescription('Leave blank to use default label: ' . $this->singular_name())
        );

        if (class_exists('CroppableImageField')) {
            $fields->addFieldToTab(
                'Root.Main',
                CroppableImageField::create('PreviewImageID', 'Tile\'s thumbnail')
                    ->setCropperRatio(460/245)
                    ->setDescription('Choose a different image for when displayed in tile, or leave it empty to use the page hero image')
            );
        } elseif (class_exists('CropperField\CropperField')) {
            $im                 =   new UploadField('PreviewImage', 'Preview image');
            $fields->addFieldsToTab(
                'Root.Main',
                [
                    $im,
                    CropperField\CropperField::create(
                        'CrpdPrevImg',
                        'Cropping preview image',
                        new CropperField\Adapter\UploadField(
                            $im
                        ),
                        array(
                            'aspect_ratio' 			=> 460/245,
                            'generated_max_width' 	=> 460
                        )
                    )->setDescription('Choose a different image for when displayed in tile, or leave it empty to use the page hero image')
                ]
            );
        } else {
            $fields->addFieldToTab(
                'Root.Main',
                UploadField::create(
                    'PreviewImage',
                    'Tile\'s thumbnail'
                )->setDescription('Choose a different image for when displayed in tile, or leave it empty to use the page hero image')
            );
        }



        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextareaField::create('Excerpt', 'Summary Snippet'),
                HtmlEditorField::create('Content', 'Content')
            )
        );

        $publishDate = DateField::create('PublishDate', 'Publish date');
        $publishDate->setConfig('showcalendar', true);

        $source = function()
        {
            return KnowledgeCategory::get()->map()->toArray();
        };

        $category_field = DropdownField::create('CategoryID', 'Category', $source())->setEmptyString(' - select one - ');
        $category_field->useAddNew('KnowledgeCategory', $source);

        $author_source = function()
        {
            return Author::get()->map()->toArray();
        };

        $author_field = DropdownField::create('AuthorID', 'Author', $author_source())->setEmptyString(' - no author - ');
        $author_field->useAddNew('Author', $author_source);

        $fields->addFieldsToTab('Root.Main', array($publishDate, $author_field, $category_field), 'URLSegment');
    }

    public function cached($jons_format = false)
    {
        $start              =   microtime();
        $factory            =   $this->ClassName;
        $key                =   $this->ID . '_' . strtotime($this->LastEdited);

        $data = SaltedCache::read($factory, $key);

        if (empty($data)) {
            $date_field             =   new Date();
            $date_field->setValue($this->PublishDate);
            $group_name             =   !empty($this->TileLabel) ? $this->TileLabel : $this->singular_name();
            $data = array(
                'ID'                =>  $this->ID,
                'Title'             =>  $this->Title,
                'Link'              =>  $this->Link(),
                'PublishedDate'     =>  $date_field->Format('d F Y'),
                'Excerpt'           =>  $this->Excerpt,
                'HubGroup'          =>  $group_name,
                'HubClass'          =>  Utilities::sanitise($group_name),  // maybe we should replace this with the model's classname?
                'Thumbnail'         =>  $this->getThumbnail()
            );

            if (!empty($this->AuthorID)) {
                $author = $this->Author();
                $data['Author']     =   $author->Title();
            }

            // if (!empty($this->PreviewImageID)) {
            //     $thumbnail = method_exists($this->PreviewImage(), 'Cropped') ? $this->PreviewImage()->Cropped() : $this->PreviewImage();
            //     $data['Thumbnail']  =   array(
            //         'Large'         =>  $thumbnail->FillMax(460, 245)->URL,
            //         'Small'         =>  $thumbnail->FillMax(220, 135)->URL
            //     );
            // } elseif (!empty($this->PageHeroID)) {
            //     $thumbnail = method_exists($this->PageHero(), 'Cropped') ? $this->PageHero()->Cropped() : $this->PageHero();
            //     $data['Thumbnail']  =   array(
            //         'Large'         =>  $thumbnail->FillMax(460, 245)->URL,
            //         'Small'         =>  $thumbnail->FillMax(220, 135)->URL
            //     );
            // } else {
            //     $data['Thumbnail']  =   array(
            //         'Large'         =>  'https://via.placeholder.com/460x245',
            //         'Small'         =>  'https://via.placeholder.com/220x135'
            //     );
            // }

            SaltedCache::save($factory, $key, $data);
        }

        if ($jons_format) {
            return $data;
        }

        return new ArrayData($data);
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (empty($this->Excerpt)) {
            $this->Excerpt  =   $this->getFirstParagraph();
        }
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        $this->cached();
    }

    private function getFirstParagraph()
    {
        $html           =   $this->Content;
        $exploded       =   explode('</p>', $html);

        foreach($exploded as $paragraph)
        {
            $txt        =   strip_tags(trim($paragraph));
            if (!empty($txt)) {
                return $txt;
            }
        }

        return '';
    }

    public function getThumbnail()
    {
        // Debugger::inspect($this->PreviewImage());
        if (!empty($this->PreviewImageID)) {
            $thumbnail = method_exists($this->PreviewImage(), 'Cropped') ? $this->PreviewImage()->Cropped() : (!empty($this->CrpdPrevImgID) ? $this->CrpdPrevImg() : $this->PreviewImage());
        } elseif (!empty($this->PageHeroID)) {
            $thumbnail = method_exists($this->PageHero(), 'Cropped') ? $this->PageHero()->Cropped() : (!empty($this->PageHeroCroppedID) ? $this->PageHeroCropped() : $this->PageHero());
        }

        if (!is_null($thumbnail)) {
            $large = $thumbnail->FillMax(460, 245);
            $small = $thumbnail->FillMax(220, 135);

            if ($large && $small) {
                return  array(
                            'Large' =>  $large->URL,
                            'Small' =>  $small->URL
                        );
            }
        }

        return array(
                        'Large' =>  'https://via.placeholder.com/460x245',
                        'Small' =>  'https://via.placeholder.com/220x135'
                    );
    }

}

class KnowledgeArticle_Controller extends Page_Controller
{
    public function getHeaderContent()
    {
        return $this->Parent()->getHeaderContent();
    }

    public function getMyGroup()
    {
        return $this->Parent()->Title;
    }
}
