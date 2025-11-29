<?php

namespace App\Enums;

enum AutomationActionsEnum: int
{
    case ASSIGN_CONTACT = 1;
    case NOTIFY_OWNER = 2;
    case SEND_WELCOME_EMAIL = 3;
    case ASSIGN_TO_TEAM = 4;
    case ASSIGN_OPPORTUNITY = 5;
    case SEND_EMAIL = 6;
    case ESCALATE = 7;
    case NOTIFY_MANAGER = 8;
    case CREATE_ONBOARDING_TASK = 9;
    case TAG_AND_REOPEN_LATER = 10;
    case SEND_INVOICE_EMAIL = 11;
    case NOTIFY_FINANCE = 12;
    case SEND_REMINDER_AND_TASK = 13;
    case SEND_REMINDER = 14;
    case TRIGGER_NEXT_STEP = 15;
    case ESCALATE_TASK = 16;
    case MOVE_STAGE = 17;
    case NOTIFY_OWNER_AND_RESCHEDULE = 18;
    case SEND_REMINDER_RESCHEDULE = 19;
    case CREATE_CONTACT = 20;
    case NOTIFY_ADMIN = 21;
    case ASSIGN_TO_SALES = 22;
    case CREATE_OPPORTUNITY = 23;
}
