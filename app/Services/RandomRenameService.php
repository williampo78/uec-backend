<?php

namespace CKSource\CKFinder\Plugin\RandomRenameService;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Event\BeforeCommandEvent;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Str;

class RandomRenameService implements PluginInterface, EventSubscriberInterface
{
    protected $app;

    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    public function getDefaultConfig()
    {
        return [];
    }

    public function onBeforeUpload(BeforeCommandEvent $event)
    {
        $request = $event->getRequest();
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('upload');
        if ($uploadedFile) {
            $uploadedFileName = $uploadedFile->getClientOriginalName();
            $extension = pathinfo($uploadedFileName, PATHINFO_EXTENSION);
            $randomName = Str::random(35);
            $uploadedFileName = "{$randomName}.{$extension}";
            $setOriginalName = function (UploadedFile $file, $newFileName) {
                $file->originalName = $newFileName;
            };
            $setOriginalName = \Closure::bind($setOriginalName, null, $uploadedFile);

            $setOriginalName($uploadedFile, $uploadedFileName);
        }
    }

    public static function getSubscribedEvents()
    {
        return [CKFinderEvent::BEFORE_COMMAND_QUICK_UPLOAD => 'onBeforeUpload'];
    }
}
