<?php
declare(strict_types=1);

namespace Common;

final class HttpApiExtra
{
    /**
     * @param string $url
     * @param array $data An array of data which will be encoded as form data
     * @return string The response content
     * @throws \RuntimeException
     */
    public static function postFormData(string $url, array $data): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'timeout' => 5,
                'header' => [
                    'Content-type: application/x-www-form-urlencoded',
                    'Accept: application/json'
                ],
                'content' => \http_build_query($data),
                'ignore_errors' => true
            ]
        ]);

        return \file_get_contents($url, false, $context);
    }
}
