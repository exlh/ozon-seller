<?php

declare(strict_types=1);

namespace Gam6itko\OzonSeller\Service\V3;

use Gam6itko\OzonSeller\Service\AbstractService;
use Gam6itko\OzonSeller\Utils\ArrayHelper;


class ReturnService extends AbstractService
{
    private $path = '/v3/returns/company';

    /**
     * @deprecated use V1\ReturnService::list
     *
     * @see https://api-seller.ozon.ru/v3/returns/company/fbo
     *
     * @param array $filter
     * @param int $lastId
     * @param int $limit
     *
     * @return array
     */
    public function fbo(array $filter, int $lastId = 0, int $limit = 100): array
    {
        assert($limit > 0 && $limit <= 1000);

        $body = [
            'filter'  => ArrayHelper::pick($filter, ['posting_number', 'status']),
            'last_id' => $lastId,
            'limit'   => $limit,
        ];

        return $this->request('POST', "{$this->path}/fbo", $body);
    }

    /**
     * @deprecated use V1\ReturnService::list
     *
     * @see https://api-seller.ozon.ru/v3/returns/company/fbs
     *
     * @param array $filter
     * @param int $lastId
     * @param int $limit
     *
     * @return array
     */
    public function fbs(array $filter, int $lastId = 0, int $limit = 100): array
    {
        assert($limit > 0 && $limit <= 1000);

        $body = [
            'filter'  => ArrayHelper::pick($filter, [
                'accepted_from_customer_moment', 'last_free_waiting_day', 'posting_number',
                'product_name', 'product_offer_id', 'status'
            ]),
            'last_id' => $lastId,
            'limit'   => $limit,
        ];

        return $this->request('POST', "{$this->path}/fbs", $body);
    }
}
