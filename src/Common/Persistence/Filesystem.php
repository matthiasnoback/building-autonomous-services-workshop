<?php
declare(strict_types=1);

namespace Common\Persistence;

final class Filesystem
{
    /**
     * @param string $filePath
     * @return void
     * @throws \RuntimeException
     */
    public static function ensureFilePathIsWritable(string $filePath): void
    {
        $directory = dirname($filePath);

        if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf(
                'Directory "%s" does not exist yet and could not be created',
                $directory
            ));
        }

        if (!is_writable($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" is not writable', $directory));
        }
    }
}
