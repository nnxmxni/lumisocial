<?php

namespace App\Enum;

enum SprintStatusEnum: string
{
    case INCOMING = 'incoming';
    case PENDING = 'pending';
    case OVERDUE = 'overdue';
    case COMPLETED = 'completed';
}
