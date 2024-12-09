<?php
namespace App\Enums;

enum ActiveState : string
{
    case ACTIVE = 'active';
    case DISABLE = 'disable';
    case REVISION = 'revision';
}
