<?php
/**
 * Created by PhpStorm.
 * User: payam
 * Date: 2/20/18
 * Time: 2:50 PM
 */

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload($basedir = '/files', UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($basedir, $fileName);

        return $fileName;
    }
}
