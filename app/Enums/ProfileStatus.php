<?php

namespace App\Enums;

use App\Enums\Traits\Values;

enum ProfileStatus: string
{
    use Values;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
