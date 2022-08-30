<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
//    临时照片处理
    public function TemporaryPhoto(Request $request){
       $file = $request->file('img');
       $filetype =$file->getClientOriginalExtension();
       $type = ['jpg','png','jepg'];
        if ($filetype && !in_array($filetype, $type)) {
            return ['error' => 'You may only upload png, jpg or gif.'];
        }
        $destinationPath = 'uploads/images/';
        $fileName = time().'.'.$filetype;

        $result = $file->move($destinationPath, $fileName);

        return $result;

    }






}
