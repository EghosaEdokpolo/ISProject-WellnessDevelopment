<?php

namespace App\Enums;

enum CommunicationChannel: string
{
    case Email          = 'email';
    case GoogleCalendar = 'google_calendar';
    case Sms            = 'sms';
}