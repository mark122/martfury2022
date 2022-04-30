<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Eloquent;
use Illuminate\Notifications\Notifiable;
use RvMedia;
use MacroableModels;

class DeliveryTime extends BaseModel
{
    protected $table = 'delivery_time';
}
