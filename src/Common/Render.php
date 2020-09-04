<?php
declare(strict_types=1);

namespace Common;

use NaiveSerializer\Serializer;

final class Render
{
    public static function jsonOrHtml($data): void
    {
        $jsonSerialized = Serializer::serialize($data);

        if (strpos($_SERVER['HTTP_ACCEPT'] ?? 'application/json', 'json') !== false) {
            header('Content-Type: application/json');
            echo $jsonSerialized;
        } else {
            include __DIR__ . '/header.php';

            ?>
            <pre><code class="data"><?php echo $jsonSerialized; ?>
</code></pre>
            <?php

            include __DIR__ . '/footer.php';
        }
    }
}
