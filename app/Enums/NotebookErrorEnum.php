<?php

namespace App\Enums;

enum NotebookErrorEnum : string
{
    case CERTAINTY = 'certainty';
    case CONTENT = 'content';
    case INTERPRETATION = 'interpretation';
    case DISTRACTION = 'distraction';
    case KICKED = 'kicked';
}
