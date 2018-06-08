<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/30/18
 * Time: 8:03 AM
 */

namespace App\Form\DataTransformer;


use App\Entity\PostTag;
use App\Repository\PostTagRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Entity\Tag;

class ArrayToCollectionTransformer implements DataTransformerInterface
{
    private $tagRepository;
    private $postTagRepository;

    public function __construct(PostTagRepository $postTagRepository, TagRepository $tagRepository)
    {
        $this->postTagRepository = $postTagRepository;
        $this->tagRepository = $tagRepository;
    }

    public function transform($postTags)
    {
        $data = [];
        if($postTags instanceof Collection)
        {

            foreach($postTags as $postTag)
            {
                $tag = $postTag->getTag();
                $data['select2tag_'.$postTag->getId()] = $tag->getName();

            }
        }

        return $data;
    }


    /**
     * 把表单控件select的数据转换为PostTag集合
     *
     * 譬如  [ 'select2tag_3', 'select2tag_2', 'tag1', 'tag2' ]
     * 前两个值为前缀加标签id，后两个值为标签名
     *
     * @param mixed $values
     * @return array|mixed
     */
    public function reverseTransform($values)
    {

        $newPostTags = [];
        $ids = [];
        $names = [];

        $len = strlen('select2tag_');
        foreach($values as $key=>$value)
        {
            $id = substr($value, $len);
            $prefix = substr($value, 0, $len);
            if ($prefix != 'select2tag_') {
                $names[] = $value;
            }
            else
            {
                $ids[] = $id;
            }

        }

        $postTags  = $this->postTagRepository->findByIds($ids);

        foreach($postTags as $postTag)
        {
            if(($key = array_search($postTag->getTag()->getName(), $names)) !== false)
            {
                unset($names[$key]);
            }
        }

        $tags = $this->tagRepository->findByIdsOrNames($names, []);

        foreach($tags as $tag)
        {
            if(($key = array_search($tag->getName(), $names)) !== false)
            {
                unset($names[$key]);
            }
        }

        $newTags = [];
        foreach($names as $name)
        {
            $tag = new Tag();
            $tag->setName($name);
            $newTags[] = $tag;
        }

        foreach(array_merge($newTags, $tags) as $tag)
        {
            $postTag = new PostTag();
            $postTag->setTag($tag);
            $newPostTags[] = $postTag;
        }



        return array_merge($newPostTags, $postTags);

    }
}