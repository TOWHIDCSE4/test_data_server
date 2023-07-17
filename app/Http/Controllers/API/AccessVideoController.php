<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AccessVideoController extends Controller
{
    public function show($slug)
    {
        $videoFolder = 'videos/SHUVideos/';
        $pathFile = $videoFolder . $slug;

        $storagePath = storage_path($pathFile);

        if (!file_exists(public_path($pathFile))) {
            return response()->json(['video_not_found'], 404);
        } else {

            $accessExists = VideoAccess::where(
                'path',
                $pathFile
            )->first();

            if ($accessExists != null) {
                $accessExists->update(
                    [
                        "time_access" => now()
                    ]
                );
            } else {
                VideoAccess::create([
                    "path" => $pathFile,
                    "time_access" => now()
                ]);
            }

            $video = Video::where(
                'path',
                $pathFile
            )->first();

            if ($video != null) {
                $video->update(
                    [
                        "time_access" => now()
                    ]
                );
            }

            // header("Location: /" . $pathFile);
            // exit;

            header('Location: /' . $pathFile);
            die();
        }
    }


    public function removeVideoAfterPeriodTime()
    {
        $listVideoListOver3Day = Video::where('created_at', '<=', date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d') - 4, date('Y'))))
            ->where('id', 31)
            ->first();
        dd($listVideoListOver3Day->path);
        File::delete($listVideoListOver3Day->path);
    }
}
