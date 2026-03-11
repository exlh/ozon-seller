<?php

declare(strict_types=1);

namespace Gam6itko\OzonSeller\Tests\Service\V2;

use Gam6itko\OzonSeller\Service\V2\WarehouseService;
use Gam6itko\OzonSeller\Tests\Service\AbstractTestCase;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;

/**
 * @coversDefaultClass \Gam6itko\OzonSeller\Service\V2\WarehouseService
 */
class WarehouseServiceTest extends AbstractTestCase
{
    protected function getClass(): string
    {
        return WarehouseService::class;
    }

    /**
     * @covers ::list
     *
     * @dataProvider dataList
     */
    public function testList($query, $request): void
    {
        $this->quickTest(
            'list',
            [
                $query,
            ],
            [
                'POST',
                '/v2/warehouse/list',
                $request,
            ]
        );
    }

    public function dataList(): iterable
    {
        yield [
            ['limit' => 10, 'cursor' => 'string', 'warehouse_ids' => [20605650762000]],
            '{"cursor":"string","limit":10,"warehouse_ids":[20605650762000]}',
        ];
        yield [
            ['limit' => 15, 'warehouse_ids' => [20605650762000, 20605650762001]],
            '{"limit":15,"warehouse_ids":[20605650762000,20605650762001]}',
        ];
        yield [
            ['limit' => 10],
            '{"limit":10}',
        ];
    }

    /**
     * @dataProvider dataListResponse
     */
    public function testListResponse($query, $response): void
    {
        $config = [123, 'api-key'];
        $client = $this->createMock(ClientInterface::class);
        $client->method('sendRequest')->willReturn(new Response(200, [], $response));
        $svc = new WarehouseService($config, $client, $this->createRequestFactory(), $this->createStreamFactory());
        $testResponse = $svc->list($query);
        $this->assertEquals('string', $testResponse['cursor']);
        $this->assertCount(1, $testResponse['warehouses']);
        $this->assertEquals(20605650762000, $testResponse['warehouses'][0]['warehouse_id']);
    }

    public function dataListResponse(): iterable
    {
        yield [
            ['limit' => 10, 'cursor' => 'string', 'warehouse_ids' => [20605650762000]],
            <<<EOD
                {
                  "cursor": "string",
                  "warehouses": [
                    {
                      "address_info": {
                        "address": "Russia, Moscow Region, Sofyino, SST industrial zone, building 2, 2",
                        "latitude": 55.495093,
                        "longitude": 38.172731,
                        "utc": "UTC+03:00"
                      },
                      "carriage_label_type": "BIG",
                      "courier_comment": "",
                      "courier_phones": [
                        "+7(999)999-99-99"
                      ],
                      "created_at": "2025-03-11T11:57:51.811Z",
                      "first_mile": {
                        "type": "PICK_UP",
                        "dropoff_point_id": "1020002075314000",
                        "timeslot_from": "20:59",
                        "timeslot_id": 287231,
                        "timeslot_to": "21:00",
                        "first_mile_is_changing": false
                      },
                      "has_entrusted_acceptance": true,
                      "has_postings_limit": false,
                      "is_auto_assembly": true,
                      "is_kgt": true,
                      "is_rfbs": true,
                      "is_waybill_enabled": true,
                      "min_postings_limit": 2,
                      "is_comfort": true,
                      "is_express": true,
                      "warehouse_type": "string",
                      "cut_in_time": 0,
                      "name": "17023",
                      "phone": "+7(999)999-99-99",
                      "postings_limit": -1,
                      "sla_cut_in": 2939,
                      "status": "created",
                      "timetable": {
                        "timetable_from": "2025-03-11T11:57:51.811Z",
                        "timetable_to": "2025-03-11T11:57:51.811Z",
                        "working_hours": [
                          {
                            "time_from": "2025-03-11T11:57:51.811Z",
                            "time_to": "2025-03-11T11:57:51.811Z"
                          }
                        ]
                      },
                      "updated_at": "2025-03-11T11:57:51.811Z",
                      "warehouse_id": 20605650762000,
                      "with_item_list": true,
                      "working_days": [
                        "MONDAY",
                        "TUESDAY",
                        "WEDNESDAY",
                        "THURSDAY",
                        "FRIDAY"
                      ]
                    }
                  ],
                  "has_next": "string"
                }
EOD
            ,
        ];
    }
}
