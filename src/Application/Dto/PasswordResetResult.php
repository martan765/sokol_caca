<?php

namespace Sokol\Application\Dto;

enum PasswordResetResult
{
    case LinkSent;
    case Reset;
    case InvalidUser;
    case InvalidToken;
    case Throttled;
}
