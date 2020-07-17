<?php
namespace App\Models\Enum;


interface NotificationStatus
{
    const NEW_NOTIFICATION = "NEW";
    const SENT = "SENT";
    const FAILED_TO_SEND = "FAILED";
    const READ = "READ";
}