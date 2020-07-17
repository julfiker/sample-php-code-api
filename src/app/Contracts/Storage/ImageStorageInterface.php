<?php

namespace App\Contracts\Storage;

use App\Models\Eloquent\Notification\Notification;
use App\Models\Eloquent\User\User;

interface ImageStorageInterface
{

    public function getImage($userId, $type);

    public function getMimeType($binaryContent);

    public function convertBase64ToBinary($base64Content);

    public function uploadImage($fileName, $base64Content, $width = null, $height = null);

    public function uploadUserProfilePhoto($userId, $base64Content);

    public function uploadUserCoverPhoto($userId, $base64Content);

}