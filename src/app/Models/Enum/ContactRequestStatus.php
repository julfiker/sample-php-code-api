<?php
namespace App\Models\Enum;


interface ContactRequestStatus
{
    const PENDING = "pending";
    const ACCEPTED = "accepted";
    const DECLINED = "declined";
    const CANCELLED = "cancelled";
    const RESET = 'reset';
}