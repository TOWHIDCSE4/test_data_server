<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\ImageAccess;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;

class ImagesController extends Controller
{
  public function getData()
  {

    $no_access_3_month =     filter_var(request("no_access_3_month"), FILTER_VALIDATE_BOOLEAN);

    $dt = new DateTime();
    $now = $dt->format('Y-m-d H:i:s');

    $images = Image::where('time_access', null)->orderBy('created_at', 'desc')
      ->get();

    foreach ($images as $image) {
      $imgA = ImageAccess::where('path', $image->path)->first();
      if ($image->time_access == null) {
        if ($imgA  != null) {
          $image->update([
            'time_access' =>   $imgA->time_access
          ]);
        }
        if ($imgA  == null) {
          $image->update([
            'time_access' =>    $image->created_at
          ]);
        }
      }
    }

    $lastTime = new DateTime();
    $lastTime->modify('-120 days');

    $total_all = Image::count();


    $total_no_access_3_month =  Image::where('time_access', '<', $lastTime)->orderBy('created_at', 'desc')
      ->count();


    // foreach ($images as $image) {
    //   $total_all += 1;
    //   $diff = $image->getDiffTimeAccess($image->image_access['time_access'] ?? null);
    //   $total_no_access_3_month =  $diff > 120 ? ($total_no_access_3_month = $total_no_access_3_month + 1) : $total_no_access_3_month;
    // }

    $images = Image::orderBy('created_at', 'desc')->paginate(40);



    return view('manage_images.show', [
      'images' => $images,
      'total_no_access_3_month' => $total_no_access_3_month,
      'total_all' => $total_all,
    ]);
  }


  public function removeImage()
  {

    $lastTime = new DateTime();
    $lastTime->modify('-120 days');
    $image3s =  Image::where('time_access', '<', $lastTime)->orderBy('created_at', 'desc')
      ->get();


    $total_not_found = 0;
    $remove_ok = 0;
    foreach ($image3s as  $image3) {
      $slug = $image3->title;
      $imageFolder = 'images/SHUimages/';
      $pathFile = $imageFolder . $slug;

      $storagePath = storage_path($pathFile);

      if (!file_exists(public_path($pathFile))) {
        $total_not_found += 1;
        $image3->delete();
      } else {
        unlink(public_path($pathFile));
        $remove_ok += 1;
      }
    }




    $no_access_3_month =     filter_var(request("no_access_3_month"), FILTER_VALIDATE_BOOLEAN);

    $dt = new DateTime();
    $now = $dt->format('Y-m-d H:i:s');

    $images = Image::where('time_access', null)->orderBy('created_at', 'desc')
      ->get();

    foreach ($images as $image) {
      $imgA = ImageAccess::where('path', $image->path)->first();
      if ($image->time_access == null) {
        if ($imgA  != null) {
          $image->update([
            'time_access' =>   $imgA->time_access
          ]);
        }
        if ($imgA  == null) {
          $image->update([
            'time_access' =>    $image->created_at
          ]);
        }
      }
    }



    $total_all = Image::count();


    $total_no_access_3_month =  Image::where('time_access', '<', $lastTime)->orderBy('created_at', 'desc')
      ->count();


    // foreach ($images as $image) {
    //   $total_all += 1;
    //   $diff = $image->getDiffTimeAccess($image->image_access['time_access'] ?? null);
    //   $total_no_access_3_month =  $diff > 120 ? ($total_no_access_3_month = $total_no_access_3_month + 1) : $total_no_access_3_month;
    // }

    $images = Image::orderBy('created_at', 'desc')->paginate(40);



    return view('manage_images.show', [
      'images' => $images,
      'total_no_access_3_month' => $total_no_access_3_month,
      'total_all' => $total_all,
      'total_not_found' => $total_not_found,
      'remove_ok' => $remove_ok
    ]);
  }
}
