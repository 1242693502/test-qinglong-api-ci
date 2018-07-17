<?php

namespace App\Events\Models\Truck;

use App\Models\Truck\TruckApproval;
use Illuminate\Queue\SerializesModels;

class TruckApprovalUpdated
{
    use SerializesModels;

    /**
     * @var \App\Models\Truck\TruckApproval $truckApproval
     */
    public $truckApproval;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Truck\TruckApproval $truckApproval
     */
    public function __construct(TruckApproval $truckApproval)
    {
        $this->truckApproval = $truckApproval;
    }
}
