<?php

/**
 * Static Analysis Results Baseliner (sarb).
 *
 * (c) Dave Liddament
 *
 * For the full copyright and licence information please view the LICENSE file distributed with this source code.
 */

declare(strict_types=1);

namespace DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Utils;

class ArrayUtils2
{
    /**
     * @param mixed $entity
     *
     * @throws ArrayParseException
     */
    public static function assertArray($entity): void
    {
        if (!is_array($entity)) {
            throw ArrayParseException::invalidType('base level', 'array');
        }
    }
}
