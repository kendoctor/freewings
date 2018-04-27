<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/27/18
 * Time: 1:17 AM
 */

namespace App\EventListener;


use App\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UploadSubscriber implements EventSubscriber
{

    /**
     * @var
     */
    private $postTitlePictureUploadDir;

    /**
     * UploadSubscriber constructor.
     * @param $postTitlePictureUploadDir
     */
    public function __construct($postTitlePictureUploadDir)
    {
        $this->postTitlePictureUploadDir = $postTitlePictureUploadDir;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
            'preRemove'
        ];
    }


    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->uploadPostTitlePicture($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->uploadPostTitlePicture($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Post) {
            $this->removePostTitlePicture($entity);
        }

    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function uploadPostTitlePicture(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Post) {
            $this->removePostTitlePicture($entity);

            $titlePictureFile = $entity->getTitlePictureFile();

            if ($titlePictureFile) {
                $titlePictureFilename = $this->generateUniqueFilename() . '.' . $titlePictureFile->guessExtension();

                $titlePictureFile->move(
                    $this->postTitlePictureUploadDir,
                    $titlePictureFilename
                );

                $entity->setTitlePicture($titlePictureFilename);
            }
        }

    }

    /**
     * @return string
     */
    private function generateUniqueFilename()
    {
        return md5(uniqid());
    }


    /**
     * @param Post $entity
     */
    private function removePostTitlePicture(Post $entity)
    {
        //delete previous uploaded file if exist
        if ($entity->getTitlePicture()) {
            //get absolute path of the image
            $previousTitlePictureFile = $this->postTitlePictureUploadDir . '/' . $entity->getTitlePicture();
            if (file_exists($previousTitlePictureFile)) {
                unlink($previousTitlePictureFile);
            }
        }
    }

}