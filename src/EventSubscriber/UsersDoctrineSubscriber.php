<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Entity\SummaryArticle;
use App\Form\SummaryArticleType;
use App\Services\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleDoctrineSubscriber implements EventSubscriber
{

    private $uploader;
    private $container;

    public function __construct(FileUploader $uploader, ContainerInterface $container)
    {
        $this->uploader = $uploader;
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function getBaseDir($entity)
    {
        if ($entity instanceof SummaryArticle) {
            return $this->container->getParameter('summary_articles_dir');
        } else {
            return $this->container->getParameter('full_articles_dir');
        }
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Article && !$entity instanceof SummaryArticle) {
            return;
        }

        $file = $entity->getFile();
        $basedir = $this->getBaseDir($entity);

        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($basedir, $file);
            $entity->setFile($fileName);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article && !$entity instanceof SummaryArticle) {
            return;
        }

        if ($fileName = $entity->getFile()) {
            $entity->setFile(new File($this->getBaseDir($entity) . '/' . $fileName));
        }
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::prePersist,
            Events::postLoad,
        ];
    }
}
