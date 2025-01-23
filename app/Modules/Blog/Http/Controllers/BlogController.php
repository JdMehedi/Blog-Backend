<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{

    public function index(){
        $blogs = Blog::orderBy('id','desc')->get();
        return response()->json([
            'data' => $blogs,
            'response_code' => 201,
            'status' => 'success'
        ]);
    }
    public function view(Request $request)
    {
        $blog = Blog::find($request->id);
        if (empty($blog)) {
            return response()->json([
                'data' => null,
                'response_code' => 404,
                'status' => 'unsuccess',
            ]);
        }
$blog['date'] = Carbon::parse($blog->created_at)->format('d-m-Y');
        return response()->json([
            'data' => $blog,
            'response_code' => 200,
            'status' => 'success',
        ]);
    }
    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function storeBlog(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $validation = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

                if ($validation->fails()) {
                    return response()->json([
                        'message' => 'Validation errors occurred.',
                        'errors' => $validation->errors(),
                    ], 422);
                }
                // Create a new Blog instance
                $blog = new Blog();
                $blog->title = $request->get('title');
                $blog->description = $request->get('description');
                $blog->short_description = $request->get('short_description');
                $blog->author = $request->get('author');

                // Handle image upload
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $path = $image->getClientOriginalExtension();
                    $imagename = time() . '.' . $path;
                    $blog->image = $imagename;
                    $image->move(public_path('uploads/blogs/images'), $imagename);
                }
                $blog->save();

                return response()->json(['message' => 'Blog created successfully'], 201);
            }

            return response()->json(['message' => 'Invalid request method'], 405);
        } catch (\Exception $exception) {
            Log::error('Error creating blog: ' . $exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the blog.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get validation rules for storing a blog.
     *
     * @return array
     */

    public function update(Request $request)
    {
        // using for testing purposs
        Log::info('Incoming request:', $request->all());
//        dd($request->all());

        $blog = Blog::find($request->id);
        try {
            if ($request->isMethod('put')) {
                $validation = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

                if ($validation->fails()) {
                    return response()->json([
                        'message' => 'Validation errors occurred.',
                        'errors' => $validation->errors(),
                    ], 422);
                }

                // Update Blog instance
                $blog->title = ($request->get('title')) ? $request->get('title') : $blog->title;
                $blog->description = ($request->get('description')) ? $request->get('description') : $blog->description;
                $blog->short_description = ($request->get('short_description')) ? $request->get('short_description') : $blog->short_description;
                $blog->author = ($request->get('author')) ? $request->get('author') : $blog->author;


                // Handle image upload
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $path = $image->getClientOriginalExtension();
                    $imagename = time() . '.' . $path;
                    $blog->image = $imagename;
                    $image->move(public_path('uploads/blogs/images'), $imagename);
                }
                $blog->save();

                return response()->json(['message' => 'Blog Updated successfully'], 201);
            }

            return response()->json(['message' => 'Invalid request method'], 405);
        } catch (\Exception $exception) {
            Log::error('Error creating blog: ' . $exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the blog.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
    private function validationRules()
    {
        return [
            'title' => 'required|string|max:255',
//            'description' => 'required|string',
//            'short_description' => 'required|string|max:500',
            'author' => 'required|string|max:255',
//            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    private function validationMessages()
    {
        return [
            'title.required' => 'The blog title is required.',
            'description.required' => 'The blog description is required.',
            'short_description.required' => 'The blog short description is required.',
            'author.required' => 'The author name is required.',
//            'image.image' => 'The uploaded file must be an image.',
//            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
//            'image.max' => 'The image size must not exceed 2MB.',
        ];
    }


}
