<?php

namespace App\Enums;

enum AutomationActionsEnum: int
{

    case NOTIFY_OWNER = 2;
    case SEND_WELCOME_EMAIL = 3;
    case SEND_EMAIL = 6;
    case ESCALATE = 7;
    case NOTIFY_MANAGER = 8;
    case CREATE_ONBOARDING_TASK = 9;
    case TAG_AND_REOPEN_LATER = 10;
    case SEND_INVOICE_EMAIL = 11;
    case SEND_REMINDER_AND_TASK = 13;
    case SEND_REMINDER = 14;
    case ESCALATE_TASK = 16;
    case MOVE_STAGE = 17;
    case CREATE_CONTACT = 20;
    case NOTIFY_ADMIN = 21;
    case ASSIGN_TO_SALES = 22;
    case CREATE_OPPORTUNITY = 23;
}
