<?php
namespace App\Models\Enum;


interface GroupMemberStatus
{
    /** Constant */
    const GROUP_LEAVED = 0;
    const GROUP_JOINED = 1;
    const GROUP_JOINREQUEST = 2;
}