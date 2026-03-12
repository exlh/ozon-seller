<?php

declare(strict_types=1);

namespace Gam6itko\OzonSeller\Service\V2;

use Gam6itko\OzonSeller\Service\AbstractService;
use Gam6itko\OzonSeller\TypeCaster;
use Gam6itko\OzonSeller\Utils\ArrayHelper;

/**
 * @psalm-type TWarehouseListRequest = array{
 *     limit: int,
 *     cursor: string,
 *     warehouse_ids: int[]
 * }
 * @psalm-type TAddressData = array{
 *     address: string,
 *     latitude: float,
 *     longitude: float,
 *     utc: string
 * }
 * @psalm-type TFirstMileData = array{
 *     type: string,
 *     dropoff_point_id: string,
 *     timeslot_from: string,
 *     timeslot_id: int,
 *     timeslot_to: string,
 *     first_mile_is_changing: bool
 * }
 * @psalm-type TWorkingHoursData = array{
 *     time_from: string,
 *     time_to: string
 * }
 * @psalm-type TTimeTableData = array{
 *     timetable_from: string,
 *     timetable_to: string,
 *     working_hours: TWorkingHoursData[]
 * }
 * @psalm-type TWarehouseData = array{
 *     address_info: TAddressData,
 *     carriage_label_type: string,
 *     courier_comment: string,
 *     courier_phones: string[],
 *     created_at: string,
 *     first_mile: TFirstMileData,
 *     has_entrusted_acceptance: bool,
 *     has_postings_limit: bool,
 *     is_auto_assembly: bool,
 *     is_kgt: bool,
 *     is_rfbs: bool,
 *     is_waybill_enabled: bool,
 *     min_postings_limit: int,
 *     is_comfort: bool,
 *     is_express: bool,
 *     warehouse_type: string,
 *     cut_in_time: int,
 *     name: string,
 *     phone: string,
 *     postings_limit: int,
 *     sla_cut_in: int,
 *     status: string,
 *     timetable: TTimeTableData,
 *     updated_at: string,
 *     warehouse_id: int,
 *     with_item_list: bool,
 *     working_days: string[]
 * }
 * @psalm-type TWarehouseListResponseData = array{
 *     cursor: string,
 *     warehouses: TWarehouseData[],
 *     has_next: bool
 * }
 */
class WarehouseService extends AbstractService
{
    private $path = '/v2/warehouse';

    /**
     * Returns a list of FBS and rFBS warehouses.
     *
     * @see https://docs.ozon.ru/api/seller/en/?__rr=1&abt_att=1#operation/WarehouseListV2
     *
     * @param TWarehouseListRequest $query
     *
     * @return TWarehouseListResponseData
     */
    public function list(array $query): array
    {
        $body = ArrayHelper::pick($query, ['cursor', 'limit', 'warehouse_ids']);
        $body = TypeCaster::castArr($body, ['cursor' => 'string', 'limit' => 'int', 'warehouse_ids' => 'arrayOfInt']);

        return $this->request('POST', "{$this->path}/list", $body);
    }
}
