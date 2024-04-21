<?php

$filePath = __DIR__ . '/.clients';

if (!file_exists($filePath)) {
    touch($filePath);

    file_put_contents($filePath, serialize([]));
}
$fileContents = file_get_contents($filePath);

return unserialize($fileContents);
