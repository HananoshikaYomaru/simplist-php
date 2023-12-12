<?php

declare(strict_types=1);

function addNumbers(int $a, int $b): int
{
    return $a + $b;
}

echo addNumbers(5, "10"); // This will cause a TypeError in strict mode
