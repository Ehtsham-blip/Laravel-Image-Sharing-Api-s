<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UploadRequest;
use App\Models\Image;
use App\Models\User;
use App\Models\VerifyTokens;
class ImageController extends Controller
{
    public function uploadImage(Request $request){

        $user=UserVerify::where('token')->first()->user;
        $file=$request->file('image');
        $extension=$file->getClientOriginalExtension();
        $path = $request->image->store('Images');
        if($request->has('status')==""){
           $status="hidden";
        }else{
            $status=$request->status;
        }
        if ($file) {
            $image=Image::create([
                'tile'=>$request->input('title'),
                'path'=>$path,
                'status'=>$request->input('status'),
                'extension'=>$extension
            ]);
            $user->image()->attach($image->id);
            return response()->json(['status'=>'true','message'=>"Image Uploaded Successful"]);
        } 
        else {
            return response()->json(['status'=>'false','message'=>"Image Not Uploaded!!"]);    
        }
    }

    public function listImages(Request $request){
        $images=Image::where('status','public')->get();
        if ($images) {
            return response()->json([
                'status'=>'true',
                'message'=>"Images Found",
                'data'=>$images
            ]);
        
        } else {
            return response()->json([
                'status'=>'true',
                'message'=>"Images Not Found"
            ]);
        }
    }

    public function deleteImage(Request $request){
        $user=UserVerify::where('token')->first()->user;
        $image=Image::find($request->$id);
        if ($image) {
            $image->delete();
            return response()->json([
                'status'=>'true',
                'message'=>"Image deleted successfully",
            ]);
        } else {
            return response()->json([
                'status'=>'false',
                'message'=>"Image not found",
            ]);
        } 
    }
    
    public function searchImage(Request $request){
        $user=UserVerify::where('token')->first()->user;
        $image = $user->image();
        $images=$this->searchQuery($request,$image);
        if($images)
        { 
            return response()->json([
                'status'=>'true',
                'message'=>'Image Found',
                'data'=>$images
            ]); 
        }
        return response()->json('Image  does not exist',false);
    }

    public function searchQuery(Request $request,$image){
        if ($request->has('title'))
        {
            $image->where('title',$request->get('title'));
        }
        if ($request->has('extension'))
        {
            $image->where('extension', $request->get('extension'));
        }
        if ($request->has('status'))
        {
            $image->where('status', $request->get('status'));
        }
        return $image->orderBy('id')->get();
    }


  

}
