<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Image;
use App\Services\InterventionService;
use Exception;
use Validator;

use Illuminate\Http\Request;

class MultipleUploadController extends Controller
{

    static function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function upload(Request $request)
    {
        $type = "ANOTHER_FILES_FOLDER";
        if (!empty($request->type)) {
            $type = $request->type;
        }

        $path_save = "images/SHUimages" . "/" . $type;

        $imageName = time() . '.' . $request->image->extension();
        $imageName = MultipleUploadController::generateRandomString() . $imageName;

        $request->image->move(public_path($path_save), $imageName);

        $imageSave = new Image();
        $imageSave->title = $imageName;
        $imageSave->path = $path_save . '/' . $imageName;
        $imageSave->time_access = now();
        $imageSave->type =  $type;
        $imageSave->save();


        $link = request()->getSchemeAndHttpHost() . "/api/SHImages/" . $imageName;


        return response()->json(['link' => $link], 200);
    }

    public function uploadCustomFile(Request $request)
    {
        $interventionService = new InterventionService();

        $type = "ANOTHER_FILES_FOLDER";
        if (!empty($request->type)) {
            $type = $request->type;
        }

        $path_save = "images/SHUimages" . "/" . $type;
        $newWidth = (int)request('new-width') ?: 600;
        $imageName = time() . '.' . $request->image->extension();
        $imageName = MultipleUploadController::generateRandomString() . $imageName;

        $request->image->move(public_path($path_save), $imageName);
        // try {
        //     list($width, $height) = getimagesize($path_save . "/" . $imageName);

        //     $sizeOfFile = filesize($path_save . "/" . $imageName) ?: 0;
        //     if ($sizeOfFile > 600000) {
        //         $pathFileReduceQuality = "images/SHUimages/REDUCE_QUALITY/" . $type;
        //         $interventionService->setImage($path_save . "/" . $imageName)
        //             ->setSizeCropByWidth($width, $height, $newWidth)
        //             ->setDestPath($pathFileReduceQuality)
        //             ->save('resize', 50);
        //         $exif = exif_read_data($path_save . "/" . $imageName);
        //         if (!empty($exif['Orientation']) && $exif['Orientation'] == 6) {
        //             try {
        //                 $interventionService->setImage($pathFileReduceQuality)
        //                     ->rotateImage(-90, $pathFileReduceQuality . "/" . $imageName)
        //                     ->save('nothing');
        //             } catch (\Throwable $th) {
        //             }
        //         }
        //     }
        // } catch (\Throwable $th) {
        // }
        $imageSave = new Image();
        $imageSave->title = $imageName;
        $imageSave->path = $path_save . '/' . $imageName;
        $imageSave->time_access = now();
        $imageSave->type =  $type;
        $imageSave->save();

        $link = request()->getSchemeAndHttpHost() . "/api/SHImages/" . $imageName;

        return response()->json(['link' => $link], 200);
    }
}
