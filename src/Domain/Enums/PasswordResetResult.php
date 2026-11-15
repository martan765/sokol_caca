<?php

namespace Sokol\Domain\Enums;

enum PasswordResetResult
{
    case LinkSent;
    case Reset;
    case InvalidUser;
    case InvalidToken;
    case Throttled;
}
