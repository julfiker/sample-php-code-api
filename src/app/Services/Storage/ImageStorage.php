<?php  namespace App\Services\Storage;


use App\Contracts\Storage\ImageStorageInterface;
use App\Models\Enum\PhotoTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageStorage implements ImageStorageInterface
{

    /**
     * Storage to access
     * @var string
     */
    private $storage = 'local';

    /**
     * Gets the image file from storage
     *
     * @param $userId
     * @param $type
     * @return mixed
     */
    public function getImage($userId, $type) {
        $disk = Storage::disk($this->storage);

        $image = null;
        $fileLocation = $this->generateFileName($userId, $type);
        if ($disk->exists($fileLocation)) {
            $image = $disk->get($fileLocation);
        } else {
            // if uploaded photo is not found, send the default image
            if ($type == PhotoTypes::USER_PROFILE_PHOTO) {
                $path = public_path() . "/images/default_profile_photo_256x256.png";
                $image = File::get($path);
            } else if ($type == PhotoTypes::USER_COVER_PHOTO) {
                $path = public_path() . "/images/default_cover_photo_800x450.png";
                $image = File::get($path);
            }
        }
        return $image;
    }

    /**
     * Determine the mime type from file
     *
     * @param $binaryContent
     * @return string
     */
    public function getMimeType($binaryContent) {
        $f = finfo_open();
        return finfo_buffer($f, $binaryContent, FILEINFO_MIME_TYPE);
    }

    /**
     * Converts base64 string to binary data
     *
     * @param $base64Content
     * @return string
     */
    public function convertBase64ToBinary($base64Content){
        // remove the initial base64 mime part (if exists)
        $data=explode(",", $base64Content);
        $content = $data[count($data) -1];
        return base64_decode($content);
    }

    /**
     * Uploads image to storage, resize and crop it and save as PNG image
     *
     * @param $fileName
     * @param $fileContent
     * @param null $width
     * @param null $height
     */
    public function uploadImage($fileName, $fileContent, $width = null, $height = null, $crop = false)
    {
        $img = Image::make($fileContent);

        if(($width !== null && $height !== null) && $crop == true) {
            $img->resize($width, null, function($constraint) {
                $constraint->aspectRatio();
            })->crop($width, $height);
        }

        $disk = Storage::disk($this->storage);
        $fileName = $fileName;

        $disk->put($fileName, $img->encode('png'));
    }

    /**
     * Uploads user profile photo
     *
     * @param $userId
     * @param $fileContent
     */
    public function uploadUserProfilePhoto($userId, $fileContent)
    {
        $fileName = $this->generateFileName($userId, PhotoTypes::USER_PROFILE_PHOTO);
        $this->uploadImage($fileName, $fileContent, 256, 256, true);
    }

    /**
     * Uploads user cover photo
     *
     * @param $userId
     * @param $fileContent
     */
    public function uploadUserCoverPhoto($userId, $fileContent)
    {
        $fileName = $this->generateFileName($userId, PhotoTypes::USER_COVER_PHOTO);
        $this->uploadImage($fileName, $fileContent, 800, 450);
    }

    /**
     * Generates unique file name based on file name and user id
     *
     * @param $imageType
     * @param $userId
     * @param string $imageExtension
     * @return string
     */
    private function generateFileName($imageType, $userId, $imageExtension = 'png') {
        return $imageType . '/' . $userId . '.' . $imageExtension;
    }

}