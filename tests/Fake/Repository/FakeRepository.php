<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests\Fake\Repository;

use Staccato\Component\Listable\ListStateInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Result;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FakeRepository extends AbstractRepository
{
    public function getResult(ListStateInterface $state): Result
    {
        $rows = 0 === $state->getLimit() ? $this->options['data'] :
            \array_slice($this->options['data'], $state->getPage() * $state->getLimit(), $state->getLimit());

        $result = new Result();
        $result
            ->setRows($rows)
            ->setTotalCount(\count($this->options['data']))
        ;

        return $result;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data' => [],
            ])
            ->setAllowedTypes('data', ['array'])
        ;
    }
}
