<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Video;


use Illuminate\Http\Request;

class MultipleUploadVideoController extends Controller
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

    function get_last_item($ar)
    {
        $index_last_item =  array_key_last($ar);
        return isset($ar[$index_last_item]) ? $ar[$index_last_item] : "";
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('video')) {
            return response()->json('no_video', 400);
        }


        if (empty($request->video)) {
            return response()->json('INVALID PHOTO', 400);
        }

        $type = "ANOTHER_FILES_FOLDER";
        if (!empty($request->type)) {
            $type = $request->type;
        }

        $path_save = "videos/SHUVideos" . "/" . $type;

        $videoName = time() . '.' . $request->video->extension();
        $videoName = MultipleUploadController::generateRandomString() . $videoName;

        if (!strtolower($this->get_last_item(explode(".", $videoName))) == "mp4" && !strtolower($this->get_last_item(explode(".", $videoName))) == "avi" && !strtolower($this->get_last_item(explode(".", $videoName))) == "mpeg" && !strtolower($this->get_last_item(explode(".", $videoName))) == "mpg") {
            return response()->json('INVALID VIDEO', 400);
        }

        $request->video->move(public_path($path_save), $videoName);

        $videoSave = new Video();
        $videoSave->title = $videoName;
        $videoSave->path = $path_save . '/' . $videoName;
        $videoSave->time_access = now();
        $videoSave->save();


        $domain = $_SERVER['HTTP_HOST'];
        $link = "https://$domain/api/SHVideos/" . $videoName;

        return response()->json(['link' => $link], 200);
    }
}
