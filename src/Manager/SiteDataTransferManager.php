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
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\MediaRepository;
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
    const SELECTOR_CUSTOMER_CREATED_AT = 'td.sf_admin_list_td_created_at';


    const SELECTOR_TAG_EDIT_LINK = 'li.sf_admin_action_edit a';
    const SELECT_TAG_ID = 'td.sf_admin_list_td_id a';
    const SELECT_TAG_NAME = 'td.sf_admin_list_td_name a';
    const SELECT_TAG_IS_TOP = 'td.sf_admin_list_td_is_top';


    const PORTFOLIO_MAX_PAGE = 32;
    const CUSTOMER_MAX_PAGE = 13;
    const TAG_MAX_PAGE = 2;


    private $wallPaintingRepository;
    private $categoryRepository;
    private $customerRepository;
    private $tagRepository;
    private $userRepository;
    private $mediaRepository;
    private $creator;
    private $uploaderHelper;

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
                                UploaderHelper $uploaderHelper
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

    }


    public function setupIO(OutputInterface $output, InputInterface $input)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
    }

    public function process()
    {

        $this->io->note('Begin transfer data from old site.');
        $this->authentication();
        $state = $this->state;
        $result = [
            'flag' => 'NO',
            'info' => 'End'
        ];

        while(true) {
            switch ($state->getPhase()) {
                case SiteDataTransferState::PHASE_PORTFOLIO_CATEGORY:
                    $this->transferPortfolioCategory();
                    break;
                case SiteDataTransferState::PHASE_CUSTOMER:
                    $this->transferPortfolioCustomer();
                    break;
                case SiteDataTransferState::PHASE_TAG:
                    $this->transferPortfolioTag();
                    break;
                case SiteDataTransferState::PHASE_PORTFOLIO:
                    $this->transferPortfolioToWallPainting();
                    break;
                default:
                    $this->io->title('Data transferring finished.');
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


    protected function buildWallPaintingCategories(Category $parent ,$nodes)
    {
        foreach ($nodes  as $node) {
            /** @var Category $category */
            $category = $this->categoryRepository->getByOldId($node->id);
            $update = $category ? 'true' : 'false';
            if($category === null) {
                $category  = $this->categoryRepository->create();
                $parent->addChild($category);
            }

            $category->setOldId($node->id);
            $category->setName(trim($node->name));
            $category->setCreatedBy($this->creator);
            $this->io->text([
                sprintf('Category %s', $category->getName()),
                sprintf('Is update %s', $update),
                sprintf('Parent %s', $parent->getName())

            ]);
            $this->categoryRepository->persist($category);


            if(count($node->children) > 0)
            {
                $this->buildWallPaintingCategories($category, $node->children);
            }
        }
    }

    protected function getIndexCrawler($url)
    {
        if(!$this->indexCrawler)
            $this->indexCrawler = $this->client->request('GET', $url);
        return $this->indexCrawler;
    }


    /**
     * 所有分类返回结果
     * @param Client $client
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function transferPortfolioCategory()
    {
        $this->io->title('Transfer portfolio categories to wallpainting categories');
        $portfolioCategoryIndexCrawler = $this->request(self::PORTFOLIO_CATEGORY_INDEX_URL);
        $portfolioCategoryIndexCrawler->filter(self::SELECTOR_PORTFOLIO_CATEGORY_NAME);

        $tree = $this->buildTree(
            $portfolioCategoryIndexCrawler->filter(self::SELECTOR_PORTFOLIO_CATEGORY_NAME),
            $portfolioCategoryIndexCrawler->filter(self::SELECTOR_PORTFOLIO_CATEGORY)
        );



        $root = $this->categoryRepository->getRoot('wall_painting', $this->creator);
        $this->buildWallPaintingCategories($root, $tree->children);

        $this->categoryRepository->persist($root);
        $this->categoryRepository->flush();

        $this->state->nextPhase();

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
    public function transferPortfolioCustomer()
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
    public function transferPortfolioTag()
    {
        $this->io->title(sprintf('Start transferring tags in page %d', $this->state->getPage()));

        $tagIndexCrawler = $this->request(sprintf(self::PORTFOLIO_TAG_INDEX_URL, $this->state->getPage()));


        $n = 0;
        foreach($tagIndexCrawler->filter(self::SELECT_TAG_ID) as $node)
        {
            $id = $node->textContent;
            $name = $tagIndexCrawler->filter(self::SELECT_TAG_NAME)->getNode($n)->textContent;
            $tmpCrawler = $tagIndexCrawler->filter(self::SELECT_TAG_IS_TOP)->eq($n);
            $isTop = ($tmpCrawler->filter('img')->count()>0) ? true : false;

            $tag = $this->tagRepository->getByOldId($id);
            if ($tag === null) {
                $tag = $this->tagRepository->create();
            }
            $n++;
            $tag->setName($name);
            $tag->setOldId($id);
            $tag->setIsTop($isTop);
            $this->tagRepository->persist($tag);
            $this->io->title(sprintf('Tag %s id(%d) oldId(%d) transferred.', $tag->getName(), $tag->getId(), $tag->getOldId()));
        }

        $this->io->title(sprintf('Finish transferring tags in page %d', $this->state->getPage()));

        $this->state->incPage();
        $this->indexCrawler = null;
        if ($this->state->getPage() > self::TAG_MAX_PAGE) {
            $this->state->nextPhase();
            $this->state->resetPage();
        }
        $this->state->save();

    }


    /**
     * 转移案例数据
     *
     * 每条记录返回结果
     *
     * @return array
     * @throws \Exception
     */
    public function transferPortfolioToWallPainting()
    {
        //get the crawler of portfolio index page
        //filter the crawler to get edit url of each portfolio
        //get the list of portfolios and transfer to wallpaintings
        //generate information for feedback
        $this->io->title(sprintf('Start transferring portfolio to wallpainting in page %d', $this->state->getPage()));

        $portfolioIndexCrawler = $this->request(sprintf(self::PORTFOLIO_INDEX_URL, $this->state->getPage()));

        foreach($portfolioIndexCrawler->filter(self::SELECTOR_PORTFOLIO_EDIT_LINK) as $node)
        {
            $portfolioEditUrl = 'http://www.freewings.me' . $node->getAttribute('href');
            $crawlerPortfolioEdit = $this->request( $portfolioEditUrl);
            $id = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_ID)->getAttribute('value');
            //find by the id of portfolio in WallPaintings
            $wallPainting = $this->wallPaintingRepository->getByOldId($id);
            if($wallPainting === null)
                $wallPainting = $this->wallPaintingRepository->create();

            $wallPainting->setTitle($this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_TITLE)->getAttribute('value'));
            $wallPainting->setBrief($this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_BRIEF)->textContent);
            $categoryId = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_CATEGORY_ID)->getAttribute('value');
            $customerId = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_CUSTOMER_ID)->getAttribute('value');

            $category = $this->categoryRepository->getByOldId($categoryId);
            $this->io->note(sprintf('wallpainting category %s oldId(%d)', $category->getName(), $category->getOldId()));
            //if($category !== $wallPainting->getCategory())
            $wallPainting->setCategory($category);

            $customer = $this->customerRepository->getByOldId($customerId);
            if($customer !== $wallPainting->getCustomer())
                $wallPainting->setCustomer($customer);
            $wallPainting->setCreatedAt($customer->getCreatedAt());

            $content = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_DESCRIPTION)->textContent;

            preg_match_all('/<img alt="" src="([^"]*)"\s[^>]*>/', $content, $matches);
            $parts = preg_split('/<img alt="" src="[^"]*"\s[^>]*>/', $content);

           // $content = preg_replace('/<img\s[^>]*>/', '___tmpsp___', $content);
           // $tokens = explode('___tmpsp___', $content);
            $srcs = [];
            for($i=0;$i<count($matches[1]);$i++)
            {
                $media = new Media();
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

            $wallPainting->setContent($content);


            $weight = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_WEIGHT)->getAttribute('value');
            $wallPainting->setWeight((int)$weight);
            $wallPainting->setOldId($this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_ID)->getAttribute('value'));
            $wallPainting->setIsPublished($this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_IS_PUBLISHED)->getAttribute('checked') == 'checked');

           // $listImagePath = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_SMALLICON)->getAttribute('value');

            //$wallPainting->getListImage()->setCreatedAt($customer->getCreatedAt());
            //$wallPainting->getListImage()->setFile($this->getPortfolioImageUploadedFile($listImagePath));

            $thumbImagePath = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_ICON)->getAttribute('value');

            if(!empty($thumbImagePath))
            {
                if(!$wallPainting->getCover()) {
                    $wallPainting->setCover(new Media());
                }

                $wallPainting->getCover()->setCreatedAt($customer->getCreatedAt());
                $wallPainting->getCover()->setFile($this->getPortfolioImageUploadedFile($thumbImagePath));
            }

           // $wallPainting->getThumbImage()->setCreatedAt($customer->getCreatedAt());
           // $wallPainting->getThumbImage()->setFile($this->getPortfolioImageUploadedFile($thumbImagePath));

            $tags = $this->firstNode($crawlerPortfolioEdit, self::SELECTOR_PORTFOLIO_TAGS_LIST)->getAttribute('value');

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

        $this->io->title(sprintf('Finish transferring portfolio in page %d', $this->state->getPage()));

        $this->state->incPage();
        $this->indexCrawler = null;
        if ($this->state->getPage() > self::PORTFOLIO_MAX_PAGE) {
            $this->state->nextPhase();
            $this->state->resetPage();
        }

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