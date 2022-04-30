<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Eloquent;
use Illuminate\Notifications\Notifiable;
use RvMedia;
use MacroableModels;

class CorporateCode extends BaseModel
{
    protected $table = 'ec_corporate_code';

    protected $fillable = [
        'list_of_corporate_code',
        'member_under',
        'max_allow_to_be_user',
        'assign_corporate',
    ];
}
