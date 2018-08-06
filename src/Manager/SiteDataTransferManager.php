<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/13/18
 * Time: 11:35 AM
 */

namespace App\Manager;


use App\Entity\Category;
use App\Entity\Media;
use App\Entity\PostTag;
use App\Entity\WallPaintingPhoto;
use App\Repository\AdvertisementRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\MediaRepository;
use App\Repository\MessageRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\WallPaintingRepository;
use Goutte\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SiteDataTransferManager
{


    /** @var OutputInterface */
    private $output;
    /** @var InputInterface */
    private $input;
    /** @var SymfonyStyle */
    private $io;
    private $indexCrawler;

    const BACKENDHOME = 'http://www.freewings.me/backend.php';
    const MAX_PAGE = 1;
    const HOST = 'http://www.freewings.me';



    const ADVERTISEMENT_INDEX_URL = 'http://www.freewings.me/backend.php/link?page=%d';
    const SELECTOR_ADVERTISEMENT_EDIT_LINK = 'div.sf_admin_list .sf_admin_action_edit>a';
    const SELECTOR_ADVERTISEMENT_ID = '#link_id';
    const SELECTOR_ADVERTISEMENT_TITLE = '#link_title';
    const SELECTOR_ADVERTISEMENT_LINK = '#link_link';
    const SELECTOR_ADVERTISEMENT_PICTURE = '#link_picture';
    const SELECTOR_ADVERTISEMENT_WEIGHT = '#link_weight';
    const SELECTOR_ADVERTISEMENT_CREATED_AT = '.sf_admin_list .sf_admin_row';

    const MESSAGE_INDEX_URL = 'http://www.freewings.me/backend.php/news?page=%d';
    const SELECTOR_MESSAGE_EDIT_LINK = 'div.sf_admin_list .sf_admin_action_edit>a';
    const SELECTOR_MESSAGE_CREATED_AT = 'div.sf_admin_list td.sf_admin_list_td_created_at';
    const SELECTOR_MESSAGE_ID = '#news_id';
    const SELECTOR_MESSAGE_TITLE = '#news_title';
    const SELECTOR_MESSAGE_SUBTITLE = '#news_subtitle';
    const SELECTOR_MESSAGE_BRIEF = '#news_brief';
    const SELECTOR_MESSAGE_CATEGORY_ID = '#news_category_id';
    const SELECTOR_MESSAGE_TITLE_ICON = '#news_title_icon';
    const SELECTOR_MESSAGE_CONTENT = '#news_content';
    const SELECTOR_MESSAGE_IS_PUBLISHED = '#news_is_published';
    const SELECTOR_MESSAGE_WEIGHT = '#news_weight';

    const STATIC_PAGE_INDEX_URL = 'http://www.freewings.me/backend.php/static_page?page=%d';
    const SELECTOR_STATIC_PAGE_EDIT_LINK = 'div.sf_admin_list .sf_admin_action_edit>a';


    const MESSAGE_CATEGORY_INDEX_URL = 'http://www.freewings.me/backend.php/newscategory';
    const SELECTOR_MESSAGE_CATEGORY_EDIT_LINK = 'div.sf_admin_list .sf_admin_action_edit>a';
    const SELECTOR_MESSAGE_CATEGORY_SELECTED_ID = '#news_category_id>option[selected]';
    const SELECTOR_MESSAGE_CATEGORY_NAME = '#news_category_name';
    const SELECTOR_MESSAGE_CATEGORY_DESCRIPTION = '#news_category_description';
    const SELECTOR_MESSAGE_CATEGORY_TOKEN = '#news_category_token';


    const PORTFOLIO_INDEX_URL = 'http://www.freewings.me/backend.php/portfolio?sort=created_at&sort_type=desc&page=%d';
    const PORTFOLIO_CATEGORY_INDEX_URL = 'http://www.freewings.me/backend.php/portfoliocategory';
    const PORTFOLIO_CUSTOMER_INDEX_URL = 'http://www.freewings.me/backend.php/customer?sort=created_at&sort_type=asc&page=%d';
    const PORTFOLIO_TAG_INDEX_URL = 'http://www.freewings.me/backend.php/portfoliotag?page=%d';

    const SELECTOR_PORTFOLIO_EDIT_LINK = 'div.sf_admin_list>table>tbody>tr table p a';
    const SELECTOR_PORTFOLIO_ID = '#portfolio_id';
    const SELECTOR_PORTFOLIO_TITLE = '#portfolio_name';
    const SELECTOR_PORTFOLIO_CATEGORY_ID = '#portfolio_category_id>option[selected]';
    const SELECTOR_PORTFOLIO_CUSTOMER_ID = '#portfolio_customer_id>option[selected]';
    const SELECTOR_PORTFOLIO_ICON = '#portfolio_icon';
    const SELECTOR_PORTFOLIO_SMALLICON = '#portfolio_small_icon';
    const SELECTOR_PORTFOLIO_BRIEF = '#portfolio_brief';
    const SELECTOR_PORTFOLIO_DESCRIPTION = '#portfolio_description';
    const SELECTOR_PORTFOLIO_IS_PUBLISHED = '#portfolio_is_published';
    const SELECTOR_PORTFOLIO_WEIGHT = '#portfolio_weight';
    const SELECTOR_PORTFOLIO_TAGS_LIST = '#portfolio_tags_list';

    const SELECTOR_PORTFOLIO_CATEGORY = 'input.sf_admin_batch_checkbox';
    const SELECTOR_PORTFOLIO_CATEGORY_NAME = 'td.sf_admin_list_td_indentedName';

    const SELECTOR_CUSTOMER_EDIT_LINK = 'li.sf_admin_action_edit a';
    const SELECTOR_CUSTOMER_ID = '#customer_id';
    const SELECTOR_CUSTOMER_NAME = '#customer_name';
    const SELECTOR_CUSTOMER_IS_ALBUM = '#customer_is_album';
    const SELECTOR_CUSTOMER_PHONE = '#customer_phone';
    const SELECTOR_CUSTOMER_FAX = '#customer_fax';
    const SELECTOR_CUSTOMER_QQ = '#customer_qq';
    const SELECTOR_CUSTOMER_ADDRESS = '#customer_address';
    const SELECTOR_CUSTOMER_WEIGHT = '#customer_weight';
    const SELECTOR_CUSTOMER_LOGO = '#customer_logo';
    const SELECTOR_CUSTOMER_COVER = '#customer_cover';
    const SELECTOR_CUSTOMER_EMAIL = '#customer_email';
    const SELECTOR_CUSTOMER_CREATED_AT = 'div.sf_admin_list td.sf_admin_list_td_created_at';

    const SELECTOR_TAG_EDIT_LINK = 'li.sf_admin_action_edit a';
    const SELECT_TAG_ID = 'td.sf_admin_list_td_id a';
    const SELECT_TAG_NAME = 'td.sf_admin_list_td_name a';
    const SELECT_TAG_IS_TOP = 'td.sf_admin_list_td_is_top';


    const PORTFOLIO_MAX_PAGE = 32;
    const CUSTOMER_MAX_PAGE = 13;
    const TAG_MAX_PAGE = 2;
    const ADVERTISEMENT_MAX_PAGE = 3;
    const MESSAGE_MAX_PAGE = 8;

    private $wallPaintingRepository;
    private $categoryRepository;
    private $customerRepository;
    private $tagRepository;
    private $userRepository;
    private $mediaRepository;
    private $advertisementRepository;
    private $messageRepository;
    private $creator;
    private $uploaderHelper;
    private $router;

    /** @var SiteDataTransferState */
    private $state;
    /** @var Client  */
    private $client;

    /**
     * @param WallPaintingRepository $wallPaintingRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param TagRepository $tagRepository
     * @param UserRepository $userRepository
     */

    public function __construct(WallPaintingRepository $wallPaintingRepository,
                                CategoryRepository $categoryRepository,
                                CustomerRepository $customerRepository,
                                TagRepository $tagRepository,
                                UserRepository $userRepository,
                                MediaRepository $mediaRepository,
                                AdvertisementRepository $advertisementRepository,
                                MessageRepository $messageRepository,
                                UploaderHelper $uploaderHelper,
                                RouterInterface $router
    )
    {
        $this->wallPaintingRepository = $wallPaintingRepository;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->tagRepository = $tagRepository;
        $this->client = $this->createClient();
        $this->state = SiteDataTransferState::instance();
        $this->userRepository = $userRepository;
        $this->creator = $userRepository->getCreatorByName();
        $this->uploaderHelper = $uploaderHelper;
        $this->mediaRepository = $mediaRepository;
        $this->advertisementRepository = $advertisementRepository;
        $this->messageRepository = $messageRepository;
        $this->router = $router;


    }


    public function setupIO(OutputInterface $output, InputInterface $input)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
    }

    public function process($phase = null, $reset = false, $force = false)
    {

        $this->io->note('Begin transfer data from old site.');
        $this->authentication();
        $state = $this->state;
        $result = [
            'flag' => 'NO',
            'info' => 'End'
        ];

        if($reset) $this->state->reset();
        $phase = SiteDataTransferState::getPhaseByName($phase);

        while(true) {


            if($phase !== null)
            {
                $this->state->lock();
                $ph = $phase;
            }
            else
            {
                $ph = $this->state->getPhase();
            }

            switch ($ph) {
                case SiteDataTransferState::PHASE_PORTFOLIO_CATEGORY:
                    $this->transferPortfolioCategory($force);
                    break;
                case SiteDataTransferState::PHASE_CUSTOMER:
                    $this->transferPortfolioCustomer($force);
                    break;
                case SiteDataTransferState::PHASE_TAG:
                    $this->transferPortfolioTag($force);
                    break;
                case SiteDataTransferState::PHASE_PORTFOLIO:
                    $this->transferPortfolioToWallPainting($force);
                    break;
                case SiteDataTransferState::PHASE_ADVERTISEMENT:
                    $this->transferAdvertisement($force);
                    break;
                case SiteDataTransferState::PHASE_MESSAGE_CATEGORY:
                    $this->transferMessageCategory($force);
                    break;
                case SiteDataTransferState::PHASE_MESSAGE:
                    $this->transferMessage($force);
                    break;
                case SiteDataTransferState::PHASE_STATIC_PAGE:
                    $this->transferStaticPage($force);
                    break;
                default:
                    $this->io->title('Data transferring finished.');
                    exit;
            }

            if($this->state->lock())
            {
                $this->state->unlock();
                exit;
            }
        }

    }

    public function authentication()
    {
        $this->io->title('Start authentication');

        $backendHomeCrawler = $this->request(self::BACKENDHOME);
        $form = $backendHomeCrawler->selectButton('登录')->form();
        $form->setValues([
            'signin[username]' => 'kendoctor',
            'signin[password]' => 'kendoctor'
        ]);
        $this->client->submit($form);
        if($this->client->getResponse()->getStatus() === 200)
        {
            $this->io->success('Authentication Succeed.');
        }
        else
        {
            $this->io->error('Authentiaction Failed.');
            exit;
        }

    }

    function getExtension($path) {
        $tokens = explode(".", $path);
        $extension = end($tokens);
        return $extension ? $extension : false;
    }

    function createTempFile($path)
    {
        $tmpFilename = tempnam(sys_get_temp_dir(), 'symfans').'.'.$this->getExtension($path);
        try {
            $this->io->title(sprintf('Downloading file %s', $path));
            $ret = file_get_contents('http://www.freewings.me' . $path);
        } catch (\Exception $e) {
            $this->io->title(sprintf('Downloading file %s failed', $path));
            return null;
        }

        file_put_contents($tmpFilename, $ret);
        return $tmpFilename;
    }

    public function getPortfolioImageUploadedFile($path)
    {
        return $this->getUploadedFile('/uploads/portfolio/' . $path);
    }

    public function getMessageCoverUploadedFile($path)
    {
        return $this->getUploadedFile('/uploads/news/' . $path);
    }

    public function getLinkImageUploadedFile($path)
    {
        return $this->getUploadedFile('/uploads/links/' . $path);
    }


    public function getUploadedFile($path)
    {
        if(empty($path)) return null;
        $tmpPath = $this->createTempFile($path);
        if($tmpPath === null) return null;
        $uploadedFile =  new UploadedFile($tmpPath, basename($path), null, null, null, true);
        return $uploadedFile;
    }

    public function createClient()
    {
        return new Client();
    }

    /**
     * get first node of nodes
     */
    protected function firstNode(Crawler $crawler, $selector)
    {
        return $crawler->filter($selector)->getNode(0);
    }


    protected function getLevelAndName($indentedName)
    {

        $start = strpos($indentedName, '-');
        $end = strrpos($indentedName, '-');
        $level = 0;

        if ($start !== false && $end !== false) {
            $indented = substr($indentedName, $start, $end - $start + 1);
            $level = strlen($indented)/3;
            $name = substr($indentedName, $end+1);
        }
        else
            $name = $indentedName;

        $node = new \stdClass();
        $node->name = $name;
        $node->level = $level;
        $node->children = [];

        return $node;
    }

    protected function findParent(&$preNode, $levelDepth)
    {
        $node = $preNode;
        for($i = 0; $i<$levelDepth; $i++)
        {
            $node = $node['parent'];
        }

        return $node;
    }

    /**
     * 建立案例分类树型
     *
     * @param $nodesName
     * @param $nodesId
     * @return \stdClass
     */
    protected function buildTree($nodesName, $nodesId)
    {
        $root = new \stdClass();
        $root->id = '#root';
        $root->level = -1;
        $root->name = 'root';
        $root->children = [];


        $preNode = $root;

        $i = 0;
        foreach($nodesName as $nodeName)
        {
            $node = $this->getLevelAndName($nodeName->textContent);
            $node->id = $nodesId->getNode($i)->getAttribute('value');

            if($node->level == 0)
            {
                $root->children[] = $node;
                $node->parent = $root;
            }
            else if($node->level > $preNode->level)
            {
                $preNode->children[] = $node;
                $node->parent = $preNode;
            }
            else if($node->level == $preNode->level)
            {
                $preNode->parent->children[] = $node;
                $node->parent = $preNode->parent;
            }
            else
            {
//                dump($preNode);
//                echo 1;
//                $preNode = $this->findParent($preNode, $preNode['level'] - $node['level']);
//                dump($preNode);
//                $preNode['children'][] = $node;
//                $node['parent'] = $preNode;
            }

            $preNode = $node;
            $i++;
        }
        return $root;
    }




    protected function getIndexCrawler($url)
    {
        if(!$this->indexCrawler)
            $this->indexCrawler = $this->client->request('GET', $url);

        return $this->indexCrawler;
    }




    /**
     * 每条记录返回结果
     *
     * @param Client $client
     * @param int $startPage
     * @param int $endPage
     * @return array
     * @throws \Exception
     */
    public function transferPortfolioCustomer2()
    {

        $customerIndexCrawler = $this->request(sprintf(self::PORTFOLIO_CUSTOMER_INDEX_URL, $this->state->getPage()));

        $this->io->title(sprintf('Start transferring customers in page %d', $this->state->getPage()));
        $index = 0;
        $createdAts = $customerIndexCrawler->filter(self::SELECTOR_CUSTOMER_CREATED_AT);



        foreach($customerIndexCrawler->filter(self::SELECTOR_CUSTOMER_EDIT_LINK) as $node)
        {
            $strCreatedAt = $createdAts->getNode($index)->textContent;
            $createdAt = \DateTime::createFromFormat('Y-m-d', trim($strCreatedAt));

            $customerEditUrl = 'http://www.freewings.me' . $node->getAttribute('href');
            $crawlerCustomerEdit = $this->client->request('GET', $customerEditUrl);
            $id = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_ID)->getAttribute('value');
            $isAlbum = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_IS_ALBUM)->getAttribute('checked') == 'checked';
            $name = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_NAME)->getAttribute('value');
            $address = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_ADDRESS)->textContent;
            $fax = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_FAX)->getAttribute('value');
            $phone = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_PHONE)->getAttribute('value');
            $qq = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_QQ)->getAttribute('value');
            $weight = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_WEIGHT)->getAttribute('value');
            $email = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_EMAIL)->getAttribute('value');
            $logo = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_LOGO)->getAttribute('value');
            $cover = $this->firstNode($crawlerCustomerEdit, self::SELECTOR_CUSTOMER_COVER)->getAttribute('value');

            $customer = $this->customerRepository->getByOldId($id);
            if($customer === null)
                $customer = $this->customerRepository->create();

            $customer->setOldId($id);
            $customer->setTitle($name);
            $customer->setWeight((int)$weight);
            $customer->setEmail($email);
            $customer->setQq($qq);
            $customer->setTelephone($phone);
            $customer->setFax($fax);
            $customer->setAddress($address);
            $customer->setIsAlbum($isAlbum);
            $customer->setIsRecommended($isAlbum);
            $customer->setCreatedAt($createdAt);



            if(!empty($logo))
            {
                if(!$customer->getCover()) {
                    $customer->setCover(new Media());
                }
                $customer->getCover()->setCreatedAt($createdAt);
                $customer->getCover()->setFile($this->getPortfolioImageUploadedFile($logo));
            }

            if(!empty($cover))
            {
                if(!$customer->getLogo()) {
                    $customer->setLogo(new Media());
                }
                $customer->getLogo()->setCreatedAt($createdAt);
                $customer->getLogo()->setFile($this->getPortfolioImageUploadedFile($cover));
            }

            $this->customerRepository->persist($customer);
            $this->customerRepository->flush();

            $this->io->title(sprintf('Customer %s id(%d) oldId(%d) transferred.', $customer->getTitle(), $customer->getId(), $customer->getOldId()));
            $index++;
        }

        $this->io->title(sprintf('Finish transferring customers in page %d', $this->state->getPage()));

        $this->state->incPage();
        $this->indexCrawler = null;
        if ($this->state->getPage() > self::CUSTOMER_MAX_PAGE) {
            $this->state->nextPhase();
            $this->state->resetPage();
        }

        $this->state->save();

    }


    /**
     * 每个分页返回结果
     *
     * @param Client $client
     * @param int $startPage
     * @param int $endPage
     * @return array
     */
    public function transferPortfolioTag($forceUpdate = false)
    {

        $maxPage = 1;
        $indexURLPlaceholder = self::PORTFOLIO_TAG_INDEX_URL;

        $indexCrawler = $this->request(sprintf($indexURLPlaceholder, 1));
        $countContent = $indexCrawler->filter('.sf_admin_list tfoot th')->getNode(0)->textContent;

        if(preg_match('/\(page \d+\/(\d+)\)/', $countContent, $matches) !== false && isset($matches[1]))
        {
            $maxPage = (int)$matches[1];
        }

        $count = 0;
        for($page = $this->state->getPage(); $page <= $maxPage; $page ++)
        {
            $indexCrawler = $this->request(sprintf($indexURLPlaceholder, $page));

            $n = 0;
            foreach($indexCrawler->filter(self::SELECT_TAG_ID) as $node)
            {
                $id = $node->textContent;
                $name = $indexCrawler->filter(self::SELECT_TAG_NAME)->getNode($n)->textContent;
                $tmpCrawler = $indexCrawler->filter(self::SELECT_TAG_IS_TOP)->eq($n);
                $isTop = ($tmpCrawler->filter('img')->count()>0) ? true : false;

                $n++;
                $count++;

                $tag = $this->tagRepository->getByOldId($id);
                if($tag && !$forceUpdate) continue;

                if ($tag === null) {
                    $tag = $this->tagRepository->create();
                }

                $tag->setName($name);
                $tag->setOldId($id);
                $tag->setIsTop($isTop);
                $this->tagRepository->persist($tag);
                $this->io->title(sprintf('Tag %s id(%d) oldId(%d) transferred.', $tag->getName(), $tag->getId(), $tag->getOldId()));

            }

            $this->io->title(sprintf('Transferred entities total count %d', $count));
            $this->io->title(sprintf('Finish transferring tags in page %d', $page));
            //save page
            $this->state->incPage();
            $this->state->save();

        }
        //phase done
        //print phase done tip
        $this->state->nextPhase();
        $this->state->save();


    }



    protected function getCreatedAts($indexCrawler, $selector, $type = 1,  $format = 'Y-m-d')
    {
        $createdAts = [];

        switch($type)
        {
            case 1:
                foreach($indexCrawler->filter($selector) as $node)
                {
                    $createdAts[] = \DateTime::createFromFormat($format, trim($node->textContent));
                }
                break;
            case 2:
                foreach($indexCrawler->filter($selector) as $node)
                {
                    preg_match('/(\d{4}-\d{2}-\d{2})/', $node->textContent, $matches );
                    $createdAts[] = \DateTime::createFromFormat($format, $matches[1]);
                }
                break;

        }

        return $createdAts;
    }

    protected function parseRichTextField($content, $createdAt = null)
    {

        preg_match_all('/<img.+src="([^"]*)"\s[^>]*>/', $content, $matches);
        $parts = preg_split('/<img.+src="[^"]*"\s[^>]*>/', $content);

        $srcs = [];
        for($i=0;$i<count($matches[1]);$i++)
        {
            $media = new Media();
            $media->setCreatedAt($createdAt);
            $media->setFile($this->getUploadedFile($matches[1][$i]));
            $this->mediaRepository->persist($media);
            $this->mediaRepository->flush();
            $src = $this->uploaderHelper->asset($media, 'file');
            $srcs[] = $src;
        }

        $content = "";
        for($i=0;$i<count($parts);$i++)
        {
            $content .= $parts[$i];
            if(isset($srcs[$i]))
            {
                $content .= sprintf('<img alt="" src="%s"/>', $srcs[$i]);
            }
        }

        return $content;
    }


    /**
     * 创建或更新案例分类
     *
     * @param Category $parent
     * @param $nodes
     * @param bool $forceUpdate
     */
    protected function buildWallPaintingCategories(Category $parent ,$nodes, &$count, $forceUpdate = false)
    {
        foreach ($nodes  as $node) {
            $count++;
            /** @var Category $category */
            $category = $this->categoryRepository->getByOldId($node->id);

            if($category && !$forceUpdate) continue;

            if($category === null) {
                $category  = $this->categoryRepository->create();
                $parent->addChild($category);
            }

            $category->setOldId($node->id);
            $category->setName(trim($node->name));
            $category->setCreatedBy($this->creator);

            $this->categoryRepository->persist($category);

            $this->io->title(sprintf('Wallpainting Category: id(%s) oldId(%s) name(%s)', $category->getId(), $category->getOldId(), $category->getName()));

            if(count($node->children) > 0)
            {
                $this->buildWallPaintingCategories($category, $node->children, $count, $forceUpdate);
            }
        }
    }

    /**
     *
     * 创建或更新新闻分类
     *
     * @param $editCrawler
     * @param $root
     * @param bool $forceUpdate
     * @return Category|mixed
     */
    protected function createMessageCategory($editCrawler, $root,  $forceUpdate = false)
    {
        $id = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CATEGORY_ID)->getAttribute('value');

        $category = $this->categoryRepository->getByOldNewsCategoryId($id);

        if($category && !$forceUpdate) return $category;

        if(!$category)
            $category = $this->categoryRepository->create();

        $category->setParent($root);
        $category->setName($this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CATEGORY_NAME)->getAttribute('value'));
        $category->setToken($this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CATEGORY_TOKEN)->getAttribute('value'));
        $category->setDescription($this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CATEGORY_DESCRIPTION)->textContent);
        $category->setIsRecommended(false);
        $category->setCreatedBy($this->creator);
        $category->setOldNewsCategoryId($id);
        $this->categoryRepository->persist($category);
        $this->categoryRepository->flush();

        return $category;
    }

    /**
     * 创建或更新广告
     *
     * @param $editCrawler
     * @param null $createdAt
     * @param bool $forceUpdate
     * @return Category|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    protected function createAdvertisement($editCrawler, $createdAt = null, $forceUpdate = false)
    {

        $id = $this->firstNode($editCrawler, self::SELECTOR_ADVERTISEMENT_ID)->getAttribute('value');

        $advertisement = $this->advertisementRepository->getByOldId($id);

        if($advertisement && !$forceUpdate) return $advertisement;

        if(!$advertisement)
            $advertisement = $this->advertisementRepository->create();


        $advertisement->setTitle($this->firstNode($editCrawler, self::SELECTOR_ADVERTISEMENT_TITLE)->getAttribute('value'));

        $link = $this->firstNode($editCrawler, self::SELECTOR_ADVERTISEMENT_LINK)->getAttribute('value');

        preg_match('/(portfolio|news|album)\/(\d+)/', $link, $matches);

        if(isset($matches[1])) {
            switch ($matches[1]) {
                case 'portfolio':
                    $entity = $this->wallPaintingRepository->getByOldId($matches[2]);
                    $link = $this->router->generate('wall_painting_show', ['id' => $entity->getId()]);
                    break;
                case 'news':
                    $entity = $this->messageRepository->getByOldId($matches[2]);
                    $link = $this->router->generate('message_show', ['id' => $entity->getId()]);
                    break;
                case 'album':
                    $entity = $this->customerRepository->getByOldId($matches[2]);
                    $link = $this->router->generate('customer_show', ['id' => $entity->getId()]);
                    break;
            }
        }

        $advertisement->setLink($link);
        $weight = (int)$this->firstNode($editCrawler, self::SELECTOR_ADVERTISEMENT_WEIGHT)->getAttribute('value');
        $advertisement->setWeight($weight);
        $advertisement->setOldId($id);
        $advertisement->setIsPublished(false);
        $coverPath = $this->firstNode($editCrawler, self::SELECTOR_ADVERTISEMENT_PICTURE)->getAttribute('value');

        if(!empty($coverPath))
        {
            if(!$advertisement->getCover()) {
                $advertisement->setCover(new Media());
            }

            $advertisement->getCover()->setCreatedAt(new \DateTime());
            $advertisement->getCover()->setFile($this->getLinkImageUploadedFile($coverPath));
        }

        $this->advertisementRepository->persist($advertisement);
        $this->advertisementRepository->flush();

        $this->io->title(sprintf('Advertisement: id(%s) oldId(%s) title(%s)', $advertisement->getId(), $advertisement->getOldId(), $advertisement->getTitle()));

        return $advertisement;
    }

    /**
     * 创建新闻
     *
     * @param $editCrawler
     * @param null $createdAt
     * @return \App\Entity\Message|mixed
     * @throws \Exception
     */
    protected function createMessage($editCrawler, $createdAt = null, $forceUpdate = false)
    {
        $id = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_ID)->getAttribute('value');
        $message = $this->messageRepository->getByOldId($id);

        if($message && !$forceUpdate) return $message;

        if(!$message)
            $message = $this->messageRepository->create();

        $message->setOldId($id);
        $message->setCreatedAt($createdAt);
        $message->setTitle($this->firstNode($editCrawler, self::SELECTOR_MESSAGE_TITLE)->getAttribute('value'));
        $message->setBrief($this->firstNode($editCrawler, self::SELECTOR_MESSAGE_BRIEF)->textContent);
        $isPublished = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_IS_PUBLISHED)->getAttribute('checked') == 'checked';
        $message->setIsPublished($isPublished);
        $message->setWeight((int)$this->firstNode($editCrawler, self::SELECTOR_MESSAGE_WEIGHT)->getAttribute('value'));
        $message->setCreatedBy($this->creator);

        $categoryId = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CATEGORY_SELECTED_ID)->getAttribute('value');
        $category = $this->categoryRepository->getByOldNewsCategoryId($categoryId);
        $message->setCategory($category);

        $coverPath = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_TITLE_ICON)->getAttribute('value');
        if(!empty($coverPath))
        {

            if(!$message->getCover()) {
                $message->setCover(new Media());
            }

            $message->getCover()->setCreatedAt($createdAt);
            $message->getCover()->setFile($this->getMessageCoverUploadedFile($coverPath));
        }

        $content = $this->firstNode($editCrawler, self::SELECTOR_MESSAGE_CONTENT)->textContent;

        $content = $this->parseRichTextField($content, $createdAt);

        $message->setContent($content);

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();

        return $message;
    }

    /**
     * 创建客户
     *
     * @param $editCrawler
     * @param null $createdAt
     * @param bool $forceUpdate
     * @return \App\Entity\Customer|mixed
     * @throws \Exception
     */
    protected function createCustomer($editCrawler, $createdAt = null, $forceUpdate = false)
    {
        $id = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_ID)->getAttribute('value');
        $isAlbum = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_IS_ALBUM)->getAttribute('checked') == 'checked';
        $name = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_NAME)->getAttribute('value');
        $address = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_ADDRESS)->textContent;
        $fax = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_FAX)->getAttribute('value');
        $phone = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_PHONE)->getAttribute('value');
        $qq = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_QQ)->getAttribute('value');
        $weight = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_WEIGHT)->getAttribute('value');
        $email = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_EMAIL)->getAttribute('value');
        $logo = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_LOGO)->getAttribute('value');
        $cover = $this->firstNode($editCrawler, self::SELECTOR_CUSTOMER_COVER)->getAttribute('value');

        $customer = $this->customerRepository->getByOldId($id);

        if($customer && !$forceUpdate) return $customer;

        if($customer === null)
            $customer = $this->customerRepository->create();

        $customer->setOldId($id);
        $customer->setTitle($name);
        $customer->setWeight((int)$weight);
        $customer->setEmail($email);
        $customer->setQq($qq);
        $customer->setTelephone($phone);
        $customer->setFax($fax);
        $customer->setAddress($address);
        $customer->setIsAlbum($isAlbum);
        $customer->setIsRecommended($isAlbum);
        $customer->setCreatedAt($createdAt);


        if(!empty($logo))
        {
            if(!$customer->getCover()) {
                $customer->setCover(new Media());
            }
            $customer->getCover()->setCreatedAt($createdAt);
            $customer->getCover()->setFile($this->getPortfolioImageUploadedFile($logo));
        }

        if(!empty($cover))
        {
            if(!$customer->getLogo()) {
                $customer->setLogo(new Media());
            }
            $customer->getLogo()->setCreatedAt($createdAt);
            $customer->getLogo()->setFile($this->getPortfolioImageUploadedFile($cover));
        }

        $this->io->title(sprintf('Customer: id(%s) oldId(%s) title(%s)', $customer->getId(), $customer->getOldId(), $customer->getTitle()));


        $this->customerRepository->persist($customer);
        $this->customerRepository->flush();

        return $customer;
    }


    /**
     * 创建墙画
     */
    public function createWallPainting($editCrawler, $forceUpdate = false)
    {

        $id = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_ID)->getAttribute('value');
        //find by the id of portfolio in WallPaintings
        $wallPainting = $this->wallPaintingRepository->getByOldId($id);

        if($wallPainting && !$forceUpdate) return $wallPainting;

        if($wallPainting === null)
            $wallPainting = $this->wallPaintingRepository->create();

        $wallPainting->setTitle($this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_TITLE)->getAttribute('value'));
        $wallPainting->setBrief($this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_BRIEF)->textContent);
        $categoryId = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_CATEGORY_ID)->getAttribute('value');
        $customerId = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_CUSTOMER_ID)->getAttribute('value');

        $category = $this->categoryRepository->getByOldId($categoryId);
        $wallPainting->setCategory($category);

        $customer = $this->customerRepository->getByOldId($customerId);
        if($customer !== $wallPainting->getCustomer())
            $wallPainting->setCustomer($customer);

        $wallPainting->setCreatedAt($customer->getCreatedAt());

        $content = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_DESCRIPTION)->textContent;
        $content = $this->parseRichTextField($content, $customer->getCreatedAt());
        $wallPainting->setContent($content);


        $weight = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_WEIGHT)->getAttribute('value');
        $wallPainting->setWeight((int)$weight);
        $wallPainting->setOldId($this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_ID)->getAttribute('value'));
        $wallPainting->setIsPublished($this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_IS_PUBLISHED)->getAttribute('checked') == 'checked');

        $thumbImagePath = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_ICON)->getAttribute('value');

        if(!empty($thumbImagePath))
        {
            if(!$wallPainting->getCover()) {
                $wallPainting->setCover(new Media());
            }

            $wallPainting->getCover()->setCreatedAt($customer->getCreatedAt());
            $wallPainting->getCover()->setFile($this->getPortfolioImageUploadedFile($thumbImagePath));
        }


        $tags = $this->firstNode($editCrawler, self::SELECTOR_PORTFOLIO_TAGS_LIST)->getAttribute('value');

        $tagNames = explode(' ', $tags);
        foreach ($tagNames as $tagName)
        {
            if(!$wallPainting->hasTag($tagName))
            {
                $tag = $this->tagRepository->getByName($tagName);
                if($tag) {
                    $postTag = new PostTag();
                    $postTag->setTag($tag);
                    $wallPainting->addPostTag($postTag);
                }

            }
        }

        $wallPainting->setCreatedBy($this->creator);

        $this->wallPaintingRepository->persist($wallPainting);
        $this->wallPaintingRepository->flush();

        $this->io->title(sprintf('WallPainting: id(%s) oldId(%s) title(%s)', $wallPainting->getId(), $wallPainting->getOldId(), $wallPainting->getTitle()));
    }

    /**
     * 通过列表转移实体数据
     *
     * @param $indexURLPlaceholder
     * @param $editLinkSelector
     * @param $createEntityCallback
     * @param $createdAtSelector
     * @param int $getCreatedAtType
     * @param bool $forceUpdate
     */
    public function transferIndexData($indexURLPlaceholder, $editLinkSelector, $createEntityCallback, $createdAtSelector, $getCreatedAtType = 1, $forceUpdate = false )
    {
        $maxPage = 1;
        $indexCrawler = $this->request(sprintf($indexURLPlaceholder, 1));
        $countContent = $indexCrawler->filter('.sf_admin_list tfoot th')->getNode(0)->textContent;

        if(preg_match('/\(page \d+\/(\d+)\)/', $countContent, $matches) !== false && isset($matches[1]))
        {
            $maxPage = (int)$matches[1];
        }

        $count = 0;
        for($page = $this->state->getPage(); $page <= $maxPage; $page ++)
        {
            $indexCrawler = $this->request(sprintf($indexURLPlaceholder, $page));

            $createdAts = $this->getCreatedAts($indexCrawler, $createdAtSelector, $getCreatedAtType);

            $createdAtIndex = 0;

            foreach($indexCrawler->filter($editLinkSelector) as $node)
            {
                $createdAt = $createdAts[$createdAtIndex];
                $createdAtIndex++;

                $editCrawler = $this->request( self::HOST . $node->getAttribute('href'));

                $entity = call_user_func_array([$this, $createEntityCallback], [$editCrawler, $createdAt, $forceUpdate]);
                $count ++;

            }

            $this->io->title(sprintf('Transferred entities total count %d', $count));
            //save page
            $this->state->incPage();
            $this->state->save();

        }
        //phase done
        //print phase done tip
        $this->state->nextPhase();
        $this->state->save();
    }

    /**
     * 转移案例分类数据
     *
     * @param Client $client
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function transferPortfolioCategory($forceUpdate = false)
    {
        $this->io->title('Transfer portfolio categories to wallpainting categories');
        $indexCrawler = $this->request(self::PORTFOLIO_CATEGORY_INDEX_URL);

        $tree = $this->buildTree(
            $indexCrawler->filter(self::SELECTOR_PORTFOLIO_CATEGORY_NAME),
            $indexCrawler->filter(self::SELECTOR_PORTFOLIO_CATEGORY)
        );

        $root = $this->categoryRepository->getRoot('wall_painting', $this->creator);

        $count = 0;
        $this->buildWallPaintingCategories($root, $tree->children, $count, $forceUpdate);

        //$this->categoryRepository->persist($root);
        //$this->categoryRepository->flush();

        $this->io->title(sprintf('Finish transferring wallpainting categories, total count %d', $count));

        $this->state->nextPhase();
        $this->state->save();

    }

    public function transferPortfolioCustomer($forceUpdate = false)
    {
        $this->transferIndexData(
            self::PORTFOLIO_CUSTOMER_INDEX_URL,
            self::SELECTOR_CUSTOMER_EDIT_LINK,
            'createCustomer',
            self::SELECTOR_CUSTOMER_CREATED_AT,
            1,
            $forceUpdate
        );
    }

    /**
     * 转移新闻分类数据
     *
     * @param bool $forceUpdate
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function transferMessageCategory($forceUpdate = false)
    {
        $this->io->title('Transfer message categories');
        $indexCrawler = $this->request(self::MESSAGE_CATEGORY_INDEX_URL);

        $root = $this->categoryRepository->getRoot('message', $this->creator);

        foreach($indexCrawler->filter(self::SELECTOR_MESSAGE_CATEGORY_EDIT_LINK) as $node)
        {

            $editUrl = self::HOST . $node->getAttribute('href');
            $editCrawler = $this->request( $editUrl);

            $category = $this->createMessageCategory($editCrawler, $root, $forceUpdate);

            $this->io->title(sprintf('Message Category: id(%s) oldId(%s) name(%s)', $category->getId(), $category->getOldNewsCategoryId(), $category->getName()));
        }

        $this->io->title('Finish transferring message categories');

        $this->state->nextPhase();
        $this->state->save();
    }

    /**
     * 转移新闻数据
     *
     * @throws \Exception
     */
    public function transferMessage($forceUpdate = false)
    {
        $this->transferIndexData(
            self::MESSAGE_INDEX_URL,
            self::SELECTOR_MESSAGE_EDIT_LINK,
            'createMessage',
            self::SELECTOR_MESSAGE_CREATED_AT,
            1,
            $forceUpdate
        );

    }

    /**
     * 转移静态页面数据
     */
    public function transferStaticPage($forceUpdate = false)
    {
        $this->transferIndexData(
            self::STATIC_PAGE_INDEX_URL,
            self::SELECTOR_STATIC_PAGE_EDIT_LINK,
            'createMessage',
            self::SELECTOR_MESSAGE_CREATED_AT,
            1,
            $forceUpdate
        );
    }


    /**
     * 转移广告数据
     */
    public function transferAdvertisement($forceUpdate = false)
    {
        $this->transferIndexData(
            self::ADVERTISEMENT_INDEX_URL,
            self::SELECTOR_ADVERTISEMENT_EDIT_LINK,
            'createAdvertisement',
            self::SELECTOR_ADVERTISEMENT_CREATED_AT,
            2,
            $forceUpdate
        );
    }

    /**
     * 转移案例数据
     *
     * 每条记录返回结果
     *
     * @return array
     * @throws \Exception
     */
    public function transferPortfolioToWallPainting($forceUpdate = false)
    {
        $indexURLPlaceholder = self::PORTFOLIO_INDEX_URL;
        $editLinkSelector = self::SELECTOR_PORTFOLIO_EDIT_LINK;

        $maxPage = 1;
        $indexCrawler = $this->request(sprintf($indexURLPlaceholder, 1));
        $countContent = $indexCrawler->filter('.sf_admin_list tfoot th')->getNode(0)->textContent;

        if(preg_match('/\(page \d+\/(\d+)\)/', $countContent, $matches) !== false && isset($matches[1]))
        {
            $maxPage = (int)$matches[1];
        }

        $count = 0;
        for($page = $this->state->getPage(); $page <= $maxPage; $page ++)
        {
            $indexCrawler = $this->request(sprintf($indexURLPlaceholder, $page));

            foreach($indexCrawler->filter($editLinkSelector) as $node)
            {

                $editCrawler = $this->request( self::HOST . $node->getAttribute('href'));


                $entity = $this->createWallPainting($editCrawler, $forceUpdate);
                $count ++;

            }

            $this->io->title(sprintf('Transferred entities total count %d', $count));
            //save page
            $this->state->incPage();
            $this->state->save();

        }
        //phase done
        //print phase done tip
        $this->state->nextPhase();
        $this->state->save();


    }

    protected function request($uri, $retry = 3)
    {
        for ($i = 0; $i < $retry; $i++) {
            try {
                $this->io->text(sprintf('Request uri: %s', $uri));
                return $this->client->request('GET', $uri);

            } catch (\Exception $e) {
                $this->io->warning('Request failed. Start retrying.');
                $this->io->title(sprintf('Request uri: %s', $uri));
            }
        }

        $this->io->error(sprintf('Request failed after retrying %d times', $retry));
        die();
    }


}