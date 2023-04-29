<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    private $post;
    const LOCAL_STORAGE_FOLDER = 'public/images/';

    public function __construct(Post $post){
        $this->post = $post;
    }

    public function index(){
        $all_posts = $this->post->latest()->get();

        return view('posts.index')->with('posts', $all_posts);
    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|min:1|max:50',
            'body' => 'required|min:1|max:1000',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:1048'
        ]);

        $this->post->user_id = Auth::user()->id;

        $this->post->title = $request->title;
        $this->post->body = $request->body;
        $this->post->image = $this->saveImage($request);
        $this->post->save();

        #BAck to Home
        return redirect()->route('index');
    }

    private function saveImage($request){
        // Change the name of the image to CURRENT TIME to avoid overwriting.
        $image_name = time() . "." . $request->image->extension();
        // image_name = 100020.jpg ".jpg" is extension name.

        // Save the image inside storage/app/public/images
        $request->image->storeAs(self::LOCAL_STORAGE_FOLDER, $image_name);

        return $image_name;
    }

    public function show($id){
        $post = $this->post->findOrFail($id);

        return view('posts.show')->with('post', $post);
    }

    public function edit($id){
        $post = $this->post->findOrFail($id);

        if($post->user->id != Auth::user()->id){
            return redirect()->back();
        }

        return view('posts.edit')->with('post', $post);
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|min:1|max:50',
            'body'  => 'required|min:1|max:1000',
            'image' =>'mimes:jpg,jpeg,png,gif|max:1048'
        ]);

        $post        = $this->post->findOrFail($id);
        $post->title = $request->title;
        $post->body  = $request->body;

        // If there is a new image
        if($request->image){
            #Delete the previous image from the local storage
            $this->deleteImage($post->image);

            #Move the new image to local storage
            $post->image = $this->saveImage($request);
        }

        $post->save();

        return redirect()->route('post.show', $id);
    }

    private function deleteImage($image_name){
        $image_path = self::LOCAL_STORAGE_FOLDER . $image_name;
        // image_path = "/public/images/1632278.jpeg";

        if(Storage::disk('local')->exists($image_path)){
            Storage::disk('local')->delete($image_path);
        }
    }

    public function destroy($id){
        $post = $this->post->findOrFail($id);
        $this->deleteImage($post->image);

        $this->post->destroy($id);

        return redirect()->back();
    }
}
