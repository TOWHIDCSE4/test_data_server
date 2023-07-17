<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\ImageAccess;
use App\Services\InterventionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AccessImageController extends Controller
{
    function webpImage($source, $quality = 100, $removeOld = false)
    {
        $dir = pathinfo($source, PATHINFO_DIRNAME);
        $name = pathinfo($source, PATHINFO_FILENAME);
        $destination = $dir . DIRECTORY_SEPARATOR . $name . '.webp';
        $info = getimagesize($source);
        $isAlpha = false;
        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($isAlpha = $info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($isAlpha = $info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return $source;
        }

        if ($isAlpha) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
        imagewebp($image, $destination, $quality);

        if ($removeOld)
            unlink($source);

        return $destination;
    }

    public function show($slug, Request $request)
    {
        $interventionService = new InterventionService();
        $pathFileHasReduceImg = null;
        $pathFileHasImg = null;
        $pathFileReduce = null;

        $imageExists = Image::where('title', $slug)
            ->first();

        if ($imageExists == null) {
            return response()->json(['image_not_found_in_db'], 404);
        }
        $pathFileHasImg = $imageExists->path;

        if (filter_var($request->reduce_file, FILTER_VALIDATE_BOOLEAN) && $request->reduce_file == "true") {
            $splitPath = explode("/", $imageExists->path);
            $pathFileHasImg = $imageExists->path;
            if ($splitPath != null && count($splitPath) > 0) {
                $pathFileHasReduceImg = $splitPath[0] . "/" . $splitPath[1] . "/REDUCE_QUALITY/" . $splitPath[2] . "/" . $splitPath[3];
                $pathFileReduce = $splitPath[0] . "/" . $splitPath[1] . "/REDUCE_QUALITY/" . $splitPath[2];
            }

            if (!file_exists(public_path($pathFileHasReduceImg)) && $pathFileHasReduceImg != null && $pathFileReduce != null) {
                if (!file_exists(public_path($pathFileHasImg))) {
                    try {
                        $interventionService->setImage($pathFileHasImg)
                            ->setSize(600)
                            ->setDestPath($pathFileReduce)
                            ->save('resize', 50);

                        $accessExists = ImageAccess::where('path', $pathFileHasReduceImg)->first();

                        if ($accessExists != null) {
                            $accessExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        } else {
                            ImageAccess::create([
                                "path" => $pathFileHasReduceImg,
                                "time_access" => now()
                            ]);
                        }

                        if ($imageExists != null) {
                            $imageExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        }

                        $domain = $_SERVER['HTTP_HOST'];
                        header('Location: https://' . $domain . "/" . $pathFileHasReduceImg);
                        die();
                    } catch (Exception $ex) {
                        return response()->json(['image_not_found_in_source'], 404);
                    }
                } else {
                    try {
                        $interventionService->setImage($pathFileHasImg)
                            ->setSize(600)
                            ->setDestPath($pathFileReduce)
                            ->save('resize', 50);

                        $accessExists = ImageAccess::where('path', $pathFileHasReduceImg)->first();

                        if ($accessExists != null) {
                            $accessExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        } else {
                            ImageAccess::create([
                                "path" => $pathFileHasReduceImg,
                                "time_access" => now()
                            ]);
                        }

                        if ($imageExists != null) {
                            $imageExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        }

                        $domain = $_SERVER['HTTP_HOST'];
                        header('Location: https://' . $domain . "/" . $pathFileHasReduceImg);
                        die();
                    } catch (Exception $ex) {
                        return response()->json(['image_not_found_in_source'], 404);
                    }
                }
            } else {

                $accessExists = ImageAccess::where('path', $pathFileHasReduceImg)->first();

                if ($accessExists != null) {
                    $accessExists->update(
                        [
                            "time_access" => now()
                        ]
                    );
                } else {
                    ImageAccess::create([
                        "path" => $pathFileHasReduceImg,
                        "time_access" => now()
                    ]);
                }

                if ($imageExists != null) {
                    $imageExists->update(
                        [
                            "time_access" => now()
                        ]
                    );
                }

                $domain = $_SERVER['HTTP_HOST'];
                header('Location: https://' . $domain . "/" . $pathFileHasReduceImg);
                die();
            }
        } else {
            if (!file_exists(public_path($pathFileHasImg))) {
                return response()->json(['image_not_found_in_source'], 404);
            } else {
                $accessExists = ImageAccess::where('path', $pathFileHasImg)->first();

                if ($accessExists != null) {
                    $accessExists->update(
                        [
                            "time_access" => now()
                        ]
                    );
                } else {
                    ImageAccess::create([
                        "path" => $pathFileHasImg,
                        "time_access" => now()
                    ]);
                }

                if ($imageExists != null) {
                    $imageExists->update(
                        [
                            "time_access" => now()
                        ]
                    );
                }

                $domain = $_SERVER['HTTP_HOST'];
                header('Location: https://' . $domain . "/" . $pathFileHasImg);
                die();
            }
        }
    }

    public function showNew($slug, Request $request)
    {
        $pathShow =   Cache::remember(json_encode([1, $_SERVER['REQUEST_URI'], "image"]), 60 * 60, function () use ($slug, $request) {

            $interventionService = new InterventionService();
            $pathFileHasReduceImg = null;
            $pathFileHasImg = null;
            $pathFileReduce = null;
            $image_type = request('image-type') ?? "";
            $newWidth = (int)request('new-width') ?: 600;
            $imageExists = Image::where('title', $slug)
                ->first();

            if ($imageExists == null) {
                return response()->json(['image_not_found_in_db'], 404);
            }
            $pathFileHasImg = $imageExists->path;

            if (filter_var($request->reduce_file, FILTER_VALIDATE_BOOLEAN)) {
                $splitPath = explode("/", $imageExists->path);
                if ($splitPath != null && count($splitPath) > 0) {
                    $pathFileHasReduceImg = $splitPath[0] . "/" . $splitPath[1] . "/REDUCE_QUALITY/" . $splitPath[2] . "/" . $splitPath[3];
                    $pathFileReduce = $splitPath[0] . "/" . $splitPath[1] . "/REDUCE_QUALITY/" . $splitPath[2];
                }
                if (file_exists(public_path($pathFileHasReduceImg))) {
                    try {
                        $accessExists = ImageAccess::where('path', $pathFileHasReduceImg)->first();

                        if ($accessExists != null) {
                            $accessExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        } else {
                            ImageAccess::create([
                                "path" => $pathFileHasReduceImg,
                                "time_access" => now()
                            ]);
                        }

                        if ($imageExists != null) {
                            $imageExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        }

                        return $pathFileHasReduceImg;
                    } catch (Exception $ex) {
                        return response()->json(['image_not_found_in_source'], 404);
                    }
                } else {
                    if (file_exists(public_path($pathFileHasImg))) {
                        try {
                            $exif = exif_read_data($pathFileHasImg);
                            if (!empty($exif['Orientation']) && $exif['Orientation'] == 6) {
                                list($width, $height) = getimagesize($pathFileHasImg);
                                $interventionService->setImage($pathFileHasImg)
                                    ->setSizeCropByWidth($width, $height, $newWidth)
                                    ->setDestPath($pathFileReduce)
                                    ->save('resize', 50);
                                $interventionService->setImage($pathFileHasReduceImg)
                                    ->rotateImage(-90, $pathFileHasReduceImg)
                                    ->save('nothing');
                            } else {
                                list($width, $height) = getimagesize($pathFileHasImg);
                                $interventionService->setImage($pathFileHasImg)
                                    ->setSizeCropByWidth($width, $height, $newWidth)
                                    ->setDestPath($pathFileReduce)
                                    ->save('resize', 50);
                            }
                        } catch (\Throwable $th) {
                            $pathFileHasReduceImg = $pathFileHasImg;
                        }

                        $accessExists = ImageAccess::where('path', $pathFileHasReduceImg)->first();

                        if ($accessExists != null) {
                            $accessExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        } else {
                            ImageAccess::create([
                                "path" => $pathFileHasReduceImg,
                                "time_access" => now()
                            ]);
                        }

                        if ($imageExists != null) {
                            $imageExists->update(
                                [
                                    "time_access" => now()
                                ]
                            );
                        }

                        // if ($image_type == 'webp' || !str_contains($slug, '.webp')) {
                        //     $im = imagecreatefrompng(public_path($pathFileHasReduceImg));
                        //     $webp = imagewebp($im, public_path($pathFileHasReduceImg), 90);
                        // }

                        return $pathFileHasReduceImg;
                    } else {
                        return response()->json(['image_not_found_in_source'], 404);
                    }
                }
            } else {
                if (!file_exists(public_path($pathFileHasImg))) {
                    return response()->json(['image_not_found_in_source'], 404);
                } else {
                    $accessExists = ImageAccess::where('path', $pathFileHasImg)->first();

                    if ($accessExists != null) {
                        $accessExists->update(
                            [
                                "time_access" => now()
                            ]
                        );
                    } else {
                        ImageAccess::create([
                            "path" => $pathFileHasImg,
                            "time_access" => now()
                        ]);
                    }

                    if ($imageExists != null) {
                        $imageExists->update(
                            [
                                "time_access" => now()
                            ]
                        );
                    }

                    return $pathFileHasImg;
                }
            }
        });

        if ($pathShow instanceof JsonResponse) {
            Cache::forget(json_encode([1, $_SERVER['REQUEST_URI'], "image"]));
            return ($pathShow);
        } else {
            // $img = file_get_contents(public_path($pathShow));
            // return response($img)->header('Content-type', 'image/png');
            header('Location: /' . $pathShow);
            die();
        }
    }
}
