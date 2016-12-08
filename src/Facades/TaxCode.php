<?php
namespace Myleshy\Quickbooks\Facades;

use Illuminate\Support\Facades\Facade;

class TaxCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tax_code';
    }
}
