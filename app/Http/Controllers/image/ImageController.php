<?php

namespace App\Http\Controllers\image;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

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
    public function imageUploadAjax(Request $request){
       dd($request->all());
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
            
        }
    }
}
