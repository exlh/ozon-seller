<?php

declare(strict_types=1);

namespace Gam6itko\OzonSeller\Tests\Service\V4\Posting;

use Gam6itko\OzonSeller\Service\V4\Posting\FbsService;
use Gam6itko\OzonSeller\Tests\Service\AbstractTestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @coversDefaultClass \Gam6itko\OzonSeller\Service\V4\Posting\FbsService
 */
final class FbsServiceTest extends AbstractTestCase
{
    protected function getClass(): string
    {
        return FbsService::class;
    }

    /**
     * @covers ::ship
     *
     * @dataProvider dataShipValid
     */
    public function testShipValid(array $arguments, string $expectedJson): void
    {
        $this->quickTest(
            'ship',
            $arguments,
            [
                'POST',
                '/v4/posting/fbs/ship',
                $expectedJson,
            ]
        );
    }

    public function dataShipValid(): iterable
    {
        yield [
            [
                // packages
                [
                    [
                        'products' => [
                            [
                                'product_id' => 185479045,
                                'quantity'   => 1,
                            ],
                        ],
                    ],
                ],
                // posting_number
                '89491381-0072-1',
                // with
                ['additional_data' => true],
            ],
            '{"packages":[{"products":[{"product_id":185479045,"quantity":1}]}],"posting_number":"89491381-0072-1","with":{"additional_data":true}}',
        ];

        yield [
            [
                // packages
                [
                    [
                        'products' => [
                            [
                                'product_id' => 185479045,
                                'quantity'   => 1,
                            ],
                            [
                                'product_id' => 185479111,
                                'quantity'   => 2,
                            ],
                        ],
                    ],
                    [
                        'products' => [
                            [
                                'product_id' => 222222222,
                                'quantity'   => 3,
                            ],
                            [
                                'product_id' => 444444444,
                                'quantity'   => 4,
                            ],
                        ],
                    ],
                ],
                // posting_number
                '89491382-0073-1',
            ],
            '{"packages":[{"products":[{"product_id":185479045,"quantity":1},{"product_id":185479111,"quantity":2}]},{"products":[{"product_id":222222222,"quantity":3},{"product_id":444444444,"quantity":4}]}],"posting_number":"89491382-0073-1","with":{"additional_data":false}}',
        ];
    }

    /**
     * @covers ::ship
     *
     * @dataProvider dataShipInvalidPayload
     */
    public function testShipInvalidPayload(array $packages, string $postingNumber, array $with = []): void
    {
        self::expectException(\AssertionError::class);
        $svc = new FbsService(
            [123, 'api-key', 'https://packagist.org/'],
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class)
        );
        $svc->ship($packages, $postingNumber);
    }

    public function dataShipInvalidPayload(): iterable
    {
        yield 'empty packages array' => [
            [],
            '89491381-0072-1',
        ];

        yield 'packages is not list' => [
            [
                'products' => [
                    [
                        'product_id' => 185479045,
                        'quantity'   => 1,
                    ],
                ],
            ],
            '89491381-0072-1',
        ];

        yield 'products is not list' => [
            [
                [
                    'products' => [
                        'product_id' => 185479045,
                        'quantity'   => 1,
                    ],
                ],
            ],
            '89491381-0072-1',
        ];

        yield 'empty products' => [
            [
                [
                    'products' => [],
                ],
            ],
            '89491381-0072-1',
        ];
    }

    /**
     * @covers ::list
     */
    public function testList(): void
    {
        $this->quickTest(
            'list',
            [
                [
                    'filter' => [
                        'since' => '2021-08-01T00:00:00+00:00',
                        'to'    => '2021-08-08T00:00:00+00:00',
                    ],
                ],
            ],
            [
                'POST',
                '/v4/posting/fbs/list',
                '{"with":{"analytics_data":false,"barcodes":false,"financial_data":false,"legal_info":false},"filter":{"since":"2021-08-01T00:00:00+00:00","to":"2021-08-08T00:00:00+00:00"},"sort_dir":"asc","translit":true,"cursor":"","limit":10}',
            ]
        );
    }

    /**
     * @covers ::list
     */
    public function testListFilterByStatuses(): void
    {
        $this->quickTest(
            'list',
            [
                [
                    'filter' => [
                        'since'    => '2021-08-01T00:00:00+00:00',
                        'to'       => '2021-08-08T00:00:00+00:00',
                        'statuses' => ['awaiting_packaging', 'delivering'],
                    ],
                ],
            ],
            [
                'POST',
                '/v4/posting/fbs/list',
                '{"with":{"analytics_data":false,"barcodes":false,"financial_data":false,"legal_info":false},"filter":{"since":"2021-08-01T00:00:00+00:00","to":"2021-08-08T00:00:00+00:00","statuses":["awaiting_packaging","delivering"]},"sort_dir":"asc","translit":true,"cursor":"","limit":10}',
            ]
        );
    }

    /**
     * @covers ::unfulfilledList
     */
    public function testUnfulfilledList(): void
    {
        $this->quickTest(
            'unfulfilledList',
            [
                [
                    'filter' => [
                        'cutoff_from' => '2021-11-12T00:00:00Z',
                        'cutoff_to'   => '2021-11-13T00:00:22Z',
                    ],
                ],
            ],
            [
                'POST',
                '/v4/posting/fbs/unfulfilled/list',
                '{"with":{"analytics_data":false,"barcodes":false,"financial_data":false,"legal_info":false},"filter":{"cutoff_from": "2021-11-12T00:00:00Z","cutoff_to": "2021-11-13T00:00:22Z"},"sort_dir":"asc","translit":true,"cursor":"","limit":10}',
            ]
        );
    }

    /**
     * * @dataProvider invalidUnfulfilledRequest
     */
    public function testUnfulfilledListNoMandatoryFilter(array $methodArguments): void
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('Not defined mandatory filter date ranges `cutoff` or `delivering_date`');

        $svc = new FbsService(
            [1, 1],
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class)
        );
        $svc->unfulfilledList($methodArguments);
    }

    public function invalidUnfulfilledRequest(): iterable
    {
        $invalidFilters = [
            [],
            ['cutoff_from'          => '2021-11-12T00:00:00Z'],
            ['cutoff_to'            => '2021-11-12T00:00:00Z'],
            ['delivering_date_from' => '2021-11-12T00:00:00Z'],
            ['delivering_date_to'   => '2021-11-12T00:00:00Z'],
            ['cutoff_to'            => '2021-11-12T00:00:00Z', 'delivering_date_to' => '2021-11-12T00:00:00Z'],
            ['cutoff_to'            => '2021-11-12T00:00:00Z', 'delivering_date_from' => '2021-11-12T00:00:00Z'],
            ['cutoff_from'          => '2021-11-12T00:00:00Z', 'delivering_date_to' => '2021-11-12T00:00:00Z'],
            ['cutoff_from'          => '2021-11-12T00:00:00Z', 'delivering_date_from' => '2021-11-12T00:00:00Z'],
        ];
        foreach ($invalidFilters as $filter) {
            yield [['filter' => $filter]];
        }
    }
}
