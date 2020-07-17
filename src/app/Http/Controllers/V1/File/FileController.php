<?php

namespace App\Http\Controllers\V1\File;

use App\Contracts\Storage\ImageStorageInterface;
use App\Contracts\User\UserInterface;
use App\Exceptions\ValidationFailedException;
use App\Models\Enum\PhotoTypes;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{

    private $storageService;
    private $userInterface;
    public function __construct(ImageStorageInterface $storage, UserInterface $userInterface){
        $this->storageService = $storage;
        $this->userInterface = $userInterface;
    }

    public function viewImage($imageType, $userId)
    {
        $user = $this->userInterface->find($userId);

        $photoContent = null;
        if ($imageType === 'cover_photo')
            $photoContent = $this->storageService->getImage($user->id, PhotoTypes::USER_COVER_PHOTO);
        else if ($imageType === 'profile_photo')
            $photoContent = $this->storageService->getImage($user->id, PhotoTypes::USER_PROFILE_PHOTO);
        else
            throw new ValidationFailedException("Oops. Your photo type is not valid.");

        return response($photoContent, 200, ['Content-Type' => 'image/png']);
    }

    public function createImage($imageType, Request $request)
    {
        $binaryContent = $this->storageService->convertBase64ToBinary($request->base64_content);
        $mimeType = $this->storageService->getMimeType($binaryContent);

        if (!in_array($mimeType, ['image/jpeg', 'image/png']))
            throw new ValidationFailedException("Oops. Your photo should be JPEG or PNG.");

        if ($imageType === 'cover_photo')
            $this->storageService->uploadUserCoverPhoto(Auth::user()->id, $binaryContent);
        else if ($imageType === 'profile_photo')
            $this->storageService->uploadUserProfilePhoto(Auth::user()->id, $binaryContent);
        else
            throw new ValidationFailedException("Oops. Your photo type is not valid.");

        // Reset left, top, zoom to zero.
        $user = Auth::user();
        $user->left = 0;
        $user->top = 0;
        $user->zoom = 0;
        $this->userInterface->save($user);

        return response(null, Response::HTTP_CREATED);
    }
}
