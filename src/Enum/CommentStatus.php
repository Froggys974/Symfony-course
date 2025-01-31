<?php

declare(strict_types=1);

namespace App\Enum;

enum CommentStatus: string
{
    case PENDING = 'pending';
    case PUBLISH = 'publish';
    case REJECTED = 'rejected';
    case SPAM = 'spam';
}