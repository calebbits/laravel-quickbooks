<?php
namespace Myleshyson\LaravelQuickBooks\Facades;

use Illuminate\Support\Facades\Facade;

class RefundReceipt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'refund_receipt';
    }
}
