<?php

namespace App\Http\Controllers\image;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ImageController extends Controller
{
    //

    /**
     * @name imageUploadAjax
     * @role upload image and save data in json
     * @param Request form array
     * @return Json response
     *
     */
    public function imageUploadAjax(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'title'         => 'required|min:3',
                'image'         => 'required|image|mimes:png|max:5120'
            ],
            [
                'image.required' => 'Image is required.',
                'image.image'    => 'Photo must be an image.',
                'image.mimes'    => 'Photo must be in png format',
                'image.max'      => 'Photo must be less than 5 MB'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {

            //first retriving previous data from json


            $imageArray = [];
            $jsonString = file_get_contents('data/imageData.json');
            //$images = json_decode($jsonString, true);
            $jsonIterator = json_decode($jsonString, true);

            // $jsonIterator = new RecursiveIteratorIterator(
            //     new RecursiveArrayIterator(json_decode($jsonString, TRUE)),
            //     RecursiveIteratorIterator::SELF_FIRST
            // );
            if ($jsonIterator != null) {
               
                foreach ($jsonIterator as $image) {
                    array_push($imageArray,$image);
                }
            }
           

            //current time
            $current_timestamp = Carbon::now()->timestamp;
            //uploading image
            $file         = $request->file('image');
            $name = $file->getClientOriginalName();
            $EXT  = $file->getClientOriginalExtension();
            $imageFileName = base64_encode($name);
            $imageFileName = $imageFileName . time() . "." . $EXT;
            $attachment_path = 'data/uploads/' . $imageFileName;
            $file->move('data/uploads', $imageFileName);

            //saving in json    
            $attributeNames = array(
                'title'         => $request->title,
                'image_path'    => $attachment_path,
                'created_at'    => $current_timestamp
            );

            array_push($imageArray,$attributeNames);
            $jsonData = json_encode($imageArray, JSON_UNESCAPED_SLASHES);
            file_put_contents('data/imageData.json', $jsonData);

            return response()->json("Success");
        }
    }
}
