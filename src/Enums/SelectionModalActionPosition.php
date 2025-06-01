<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Enums;

enum SelectionModalActionPosition
{
    case TOP_LEFT;

    case TOP_RIGHT;

    case BOTTOM_LEFT;

    case BOTTOM_RIGHT;

    public function isTop(): bool
    {
        return in_array($this, [self::TOP_LEFT, self::TOP_RIGHT]);
    }

    public function isBottom(): bool
    {
        return in_array($this, [self::BOTTOM_LEFT, self::BOTTOM_RIGHT]);
    }

    public function isLeft(): bool
    {
        return in_array($this, [self::TOP_LEFT, self::BOTTOM_LEFT]);
    }

    public function isRight(): bool
    {
        return in_array($this, [self::TOP_RIGHT, self::BOTTOM_RIGHT]);
    }
}
