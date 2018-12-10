<?php
use Ntb\RestAPI\BaseRestController as BaseRestController;
use SaltedHerring\Debugger;
use SaltedHerring\SaltedSearch;
/**
 * @file SiteAppController.php
 *
 * Controller to present the data from forms.
 * */
class ArticleAPI extends BaseRestController
{
    private static $https_only  =   false;
    private $pageSize           =   20;
    private $keywords           =   null;

    private static $allowed_actions = array (
        'get'                   =>  "->isAuthenticated",
        'post'                  =>  "->isAuthenticated"
    );

    public function isAuthenticated()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                if ($this->keywords = trim($this->request->postVar('keywords'))) {
                    if ($crsf = $this->request->postVar('csrf')) {
                        return $crsf == Session::get('SecurityID');
                    }

                    return false;
                }
            }

            return true;
        }

        return array('list' => array(), 'count' => 0, 'pagination' => array('message' => 'for some reason, your request didn\'t get through'));
    }

    public function post($request)
    {
        if (!empty($this->keywords)) {
            return $this->Paginate(SaltedSearch::Search('KnowledgeArticle', array('Title', 'Content', 'MetaKeywords'), $this->keywords));
        }

        return array('list' => array(), 'count' => 0, 'pagination' => array('message' => 'Keyword is empty'));
    }

    public function get($request)
    {
        if ($this->keywords = trim($this->request->getVar('keywords'))) {
            $this->keywords =   htmlspecialchars($this->keywords, ENT_QUOTES);
            return $this->Paginate(SaltedSearch::Search('KnowledgeArticle', array('Title', 'Content', 'MetaKeywords'), $this->keywords));
        }

        if ($groupID = $request->param('GroupID')) {
            $page           =   KnowledgeHubGroupPage::get()->byID($groupID);
            $this->pageSize =   $page->ItemsPerPage;
            if ($category   =   $this->request->getVar('category')) {
                $category   =   htmlspecialchars($category, ENT_QUOTES);
                $category   =   KnowledgeCategory::get()->filter(array('Title' => $category))->first();
                $articles   =   $category ? $category->Articles() : null;
            } else {
                $articles   =   $page->AllChildren();
            }

            return $this->Paginate($articles);
        }

        if ($page = KnowledgeHubLandingPage::get()->first()) {
            $this->pageSize     =   $page->ItemsPerPage;
        }

        return $this->Paginate(KnowledgeArticle::get());
    }

    private function Paginate($articles)
    {
        if (empty($articles)) {
            return array('list' => [], 'count' => 0, 'pagination' => array('message' => '- not found -'));
        }

        $artcile_count                  =   $articles->count();

        if (empty($artcile_count)) {
            return array('list' => array(), 'count' => 0, 'pagination' => array('message' => '- no result -'));
        }

        $start                          =   $this->request->getVar('start');
        $start                          =   htmlspecialchars($start, ENT_QUOTES);

        if ($artcile_count > $this->pageSize) {
            $paged                      =   new PaginatedList($articles, $this->request);

            $paged->setPageLength($this->pageSize);

            $articles                   =   $paged;
            $list                       =   $articles->getIterator();
            $data                       =   [];

            foreach ($list as $item)
            {
                $data[]                 =   $item->cached(true);
            }

            if (empty($start)) {
                $data[0]['BigTile']     =   true;
            }

            if ($paged->MoreThanOnePage()) {
                if ($paged->NotLastPage()) {
                    // $pagination         =   $paged->NextLink() . (!empty($this->keywords) ? ('&keywords=' . $this->keywords . '&csrf=' . Session::get('SecurityID')) : '');
                    $pagination         =   $this->sanitisePagination($paged->NextLink()); // . (!empty($this->keywords) ? ('&keywords=' . $this->keywords) : '');
                    return  array(
                        'list'          =>  $data,
                        'count'         =>  $artcile_count,
                        'pagination'    =>  array('href' => $pagination)
                    );
                }

                return  array(
                    'list'              =>  $data,
                    'count'             =>  $artcile_count,
                    'pagination'        =>  array('message' => '- all loaded -')
                );
            }
        }

        $data                           =   $articles->json();

        if (empty($start)) {
            $data[0]['BigTile']         =   true;
        }

        return array('list' => $data, 'count' => $artcile_count, 'pagination' => array('message' => '- all loaded -'));
    }

    private function sanitisePagination($pagination)
    {
        $pagination                     =   str_replace('&amp;', '&', $pagination);
        $parts                          =   parse_url($pagination);

        parse_str($parts['query'], $query);

        if (empty($query['keywords']) && !empty($this->keywords)) {
            $pagination .= '&keywords=' . $this->keywords;
        }

        return $pagination;
    }

}
