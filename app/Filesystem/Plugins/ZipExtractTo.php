<?php

namespace App\Filesystem\Plugins;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Plugin\AbstractPlugin;
use ZipArchive;

/**
 * 問題以及解決
 * https://stackoverflow.com/questions/45286056/laravel-flysystem-integration-with-zip-archive-adapter
 *
 */
class ZipExtractTo extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return 'extractTo';
    }

    /**
     * Extract zip file into destination directory.
     *
     * @param string $path Destination directory 目標路經
     * @param string $zipFilePath The path to the zip file. 解壓對象的絕對路徑
     *
     * @return bool True on success, false on failure.
     */
    public function handle($path, $zipFilePath)
    {
        $path = $this->cleanPath($path);

        $zipArchive = new ZipArchive();
        $openZip = $zipArchive->open($zipFilePath);
        if ($openZip !== true)
        {
            switch($openZip) {
                case ZipArchive::ER_EXISTS:
                    echo 'File already exists.';
                    break;
                case ZipArchive::ER_INCONS:
                    echo 'Zip archive inconsistent.';
                    break;
                case ZipArchive::ER_INVAL:
                    echo 'Invalid argument.';
                    break;
                case ZipArchive::ER_MEMORY:
                    echo 'Malloc failure.';
                    break;
                case ZipArchive::ER_NOENT:
                    echo 'No such file.';
                    break;
                case ZipArchive::ER_NOZIP:
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    echo 'Not a zip archive';
                    break;
                case ZipArchive::ER_OPEN:
                    echo 'Can\'t open file.';
                    break;
                case ZipArchive::ER_READ:
                    echo 'Read error.';
                    break;
                case ZipArchive::ER_SEEK:
                    echo 'Seek error.';
                    break;
            }
            return false;
        }
        for ($i = 0; $i < $zipArchive->numFiles; ++$i)
        {
            $zipEntryName = $zipArchive->getNameIndex($i);
            $destination = $path . DIRECTORY_SEPARATOR . $this->cleanPath($zipEntryName);
            if ($this->isDirectory($zipEntryName))
            {
                $this->filesystem->createDir($destination);
                continue;
            }
            $this->filesystem->putStream($destination, $zipArchive->getStream($zipEntryName));
        }

        return true;
    }

    private function isDirectory($zipEntryName)
    {
        return substr($zipEntryName, -1) ===  '/';
    }

    private function cleanPath($path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

}
