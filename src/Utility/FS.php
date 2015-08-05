<?php namespace FHTeam\SelectelStorageApi\Utility;

use Exception;
use finfo;

class FS
{
    /**
     * Returns MIME information about the file
     *
     * @param string $filename
     *
     * @throws Exception
     * @return string
     */
    public static function getFileMimeType($filename)
    {
        $info = new finfo(FILEINFO_MIME_TYPE);
        $contentType = $info->file($filename);
        if (!$contentType) {
            throw new Exception("Unable to guess content type of '$filename'");
        }

        return $contentType;
    }
}
