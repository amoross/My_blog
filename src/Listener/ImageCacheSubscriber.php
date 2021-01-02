<?php

namespace App\EventListener;

use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::preRemove
        ];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Article && $args->hasChangedField('image')) {
            $entity->setImage($args->getOldValue('image'));
            if ($entity->getImageFile() instanceof UploadedFile) {
                $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
    }
}