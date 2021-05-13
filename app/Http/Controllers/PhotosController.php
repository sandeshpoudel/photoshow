<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    public function create($album_id)
    {
        return view ('photos.create')->with('album_id', $album_id);
    }

    public function store(Request $request)
    {
        //validate request
        $this->validate($request,[
            'title' => 'required',
            'photo' => 'image|max:1999',
            
         ]);
        //get file name with extension
         $filenameWithExt = $request->file('photo')->getClientOriginalName();
        //get just file name
         $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
         //get the extension of the file
        $extension = $request->file('photo')->getClientOriginalExtension();
        //create a new file name
        $filenameToStore = $filename.'_'.time().'.'.$extension;
        
        //Upload Image
        $path = $request->file('photo')->storeAs('public/photos/'.$request->input('album_id'), $filenameToStore);
         
        //create album
        $photo = new Photo();
        $photo->album_id = $request->input('album_id');
        $photo->title = $request->input('title');
        $photo->description = $request->input('description');
        $photo->photo = $filenameToStore;
        $photo->size = $request->file('photo')->getClientSize();
        $photo->save();
        return redirect('/albums/'. $request->input('album_id'))->with('success', 'New photos added to the album');
    }
    public function show($id)
    {
        $photo = Photo::find($id);
        return view('photos.show')->with('photo', $photo);
    }
    public function destroy($id)
    {
        $photo = Photo::find($id);
        if(Storage::delete('public/photos/'.$photo->album_id.'/'.$photo->photo))
        {
            $photo->delete();
            return redirect('/')->with('success', 'Image has been removed succcessfully');
        }
    

    }
}
