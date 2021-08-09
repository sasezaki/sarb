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

use Exception;

class ArrayUtils4
{
    /**
     * @param mixed $entity
     *
     * @throws Exception
     */
    public static function assertArray($entity): void
    {
        if (!is_array($entity)) {
            throw new Exception('A message');
        }
    }
}
