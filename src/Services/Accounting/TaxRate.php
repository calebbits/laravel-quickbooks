<?php

namespace Myleshyson\LaravelQuickBooks\Services\Accounting;

use Myleshyson\LaravelQuickBooks\Quickbooks;

class TaxRate extends Quickbooks
{
    public function find($id)
    {
        $this->service = new \QuickBooks_Ipp_Service_TaxRate();
        return $this->service->query($this->context, $this->realm, "SELECT * FROM TaxRate WHERE Id = '$id' ")[0] ?: $this->service->lastError();
    }

    public function get()
    {
        $this->service = new \QuickBooks_Ipp_Service_TaxRate();
        return $this->service->query($this->context, $this->realm, "SELECT * FROM TaxRate") ?: $this->service->lastError();
    }
}
