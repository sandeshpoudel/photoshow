<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Album;
use App\Photo;

class AlbumsController extends Controller
{
    public function index()
    {
        $albums = Album::with('photos')->get();
        return view('albums.index')->with('albums', $albums);
    }

    public function create()
    {
        return view('albums.create');
    }
    public function store(Request $request)
    {
     $this->validate($request,[
        'name' => 'required',
        'cover_image' => 'image|max:1999',
        
     ]);
    //get file name with extension
     $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
    //get just file name
     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
     //get the extension of the file
    $extension = $request->file('cover_image')->getClientOriginalExtension();
    //create a new file name
    $filenameToStore = $filename.'_'.time().'.'.$extension;
    
    //Upload Image
    $path = $request->file('cover_image')->storeAs('public/album_covers', $filenameToStore);
     
    //create album
    $album = new Album();
    $album->name = $request->input('name');
    $album->description = $request->input('description');
    $album->cover_image = $filenameToStore;
    $album->save();
    return redirect('/albums')->with('success', 'New Album has been Created');
    }

    public function show($id)
    {
        $album = Album::with('photos')->find($id);
        return view('albums.show')->with('album', $album);
    }
}
