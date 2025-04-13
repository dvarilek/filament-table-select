<?php

declare(strict_types=1);

namespace Dvarilek\FilamentTableSelect\Enums;

enum ConfirmationActionPosition
{

    case TOP_LEFT;

    case TOP_RIGHT;

    case BOTTOM_LEFT;

    case BOTTOM_RIGHT;

    /**
     * @return bool
     */
    public function isTop(): bool
    {
        return in_array($this, [self::TOP_LEFT, self::TOP_RIGHT]);
    }

    /**
     * @return bool
     */
    public function isBottom(): bool
    {
        return in_array($this, [self::BOTTOM_LEFT, self::BOTTOM_RIGHT]);
    }

    /**
     * @return bool
     */
    public function isLeft(): bool
    {
        return in_array($this, [self::TOP_LEFT, self::BOTTOM_LEFT]);
    }

    /**
     * @return bool
     */
    public function isRight(): bool
    {
        return in_array($this, [self::TOP_RIGHT, self::BOTTOM_RIGHT]);
    }
}
