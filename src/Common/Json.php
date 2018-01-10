<?php
declare(strict_types=1);

namespace Common;

final class Json
{
    /**
     * Fetch JSON from a remote URL, then decode it (converts objects to arrays).
     *
     * @param string $url
     * @return mixed
     * @throws \RuntimeException In case of communication or decoding failure
     */
    public static function decodeFromRemoteUrl(string $url)
    {
        $context = stream_context_create(['http' => ['timeout' => 1]]);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Failed to make a request to: ' . $url);
        }
        $data = json_decode($response, true);

        if ($data === null && json_last_error()) {
            throw new \RuntimeException('JSON decoding error: ' . json_last_error_msg());
        }

        return $data;
    }
}
