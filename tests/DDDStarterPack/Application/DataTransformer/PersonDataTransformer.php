<?php

declare(strict_types=1);

namespace Tests\DDDStarterPack\Application\DataTransformer;

use DDDStarterPack\Application\DataTransformer\BasicItemDataTransformer;
use Tests\Support\Model\Person;

/**
 * @extends BasicItemDataTransformer<Person, array>
 */
class PersonDataTransformer extends BasicItemDataTransformer
{
    /**
     * @psalm-return array
     */
    public function read(): array
    {
        $i = $this->item;

        return $i->toArray();
    }
}
