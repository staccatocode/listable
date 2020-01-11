<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests;

use PHPUnit\Framework\TestCase;
use Staccato\Component\Listable\ListStateInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Result;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @covers \Staccato\Component\Listable\Repository\AbstractRepository
 */
class AbstractRepositoryTest extends TestCase
{
    public function testCreate(): void
    {
        /** @var ListStateInterface $state */
        $state = $this->getMockBuilder(ListStateInterface::class)->getMock();

        $repository = new class() extends AbstractRepository {
            public function getResult(ListStateInterface $state): Result
            {
                return new Result();
            }

            public function configureOptions(OptionsResolver $optionsResolver): void
            {
                parent::configureOptions($optionsResolver);

                $optionsResolver->setDefaults([
                    'test' => true,
                ]);
            }
        };

        $repository->setOptions(['test' => false]);

        $this->assertInstanceOf(AbstractRepository::class, $repository);
        $this->assertInstanceOf(Result::class, $repository->getResult($state));
    }
}
