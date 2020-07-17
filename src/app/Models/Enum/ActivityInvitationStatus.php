<?php
namespace App\Models\Enum;


interface ActivityInvitationStatus
{
    const INVITED = "invited";
    const JOINING = "joining";
    const DECLINED = "declined";
    const LEFT = "left";
}