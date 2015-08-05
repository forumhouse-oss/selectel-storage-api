<?php namespace FHTeam\SelectelStorageApi\Service;

use League\Url\Url;

/**
 * Class OfflineStorageService
 *
 * @package ForumHouse\SelectelStorageApi\Service
 */
class LinkService
{
    /**
     * @param string $url       Url to generate access link for
     * @param int    $expires   Unix timestamp at which link should expire
     * @param string $secretKey The secret key set for file container
     *
     * @return string Url with embedded signature
     */
    public function signFileDownloadLink($url, $expires, $secretKey)
    {
        $method = 'GET';
        $url = Url::createFromUrl($url);
        $path = $url->getRelativeUrl();
        $body = sprintf("%s\n%s\n%s", $method, $expires, $path);
        $signature = hash_hmac('sha1', $body, $secretKey);

        $url->getQuery()->modify(['temp_url_sig' => $signature, 'temp_url_expires' => $expires]);

        return (string)$url;
    }
}
