<?php

namespace App\Http\Controllers\backEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

use App\Models\Resource;
use Hash;
use PhpParser\Comment\Doc;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $users = Resource::get();
        $page = 'resource';
        return view('backEnd.resource.index', compact('page', 'users'));
    }

    public function add(Request $request, $id = null)
    {
        if ($request->isMethod('GET')) {
            if ($id) {
                $user = Resource::where('id', $id)->first();
            } else {
                $user = [];
            }
            $page = 'resource';
            return view('backEnd.resource.form', compact('page', 'user'));
        }

        if ($request->isMethod('post')) {
            $data = $request->all();
            $validator = Validator::make(
                $data,
                [
                    'title' => 'required',
                    'name' => 'required',
                    'description' => 'required',
                    'image' => @$data['id'] ? 'nullable|' : 'required|' . 'image|mimes:jpeg,png,jpg|max:2048'
                ]
            );

            if ($validator->fails()) {
                return back()->withInput($request->all())->withErrors($validator->errors());
            } else {
                $user = Resource::where('id', $request->id)->first();
                if ($request->image) {
                    $fileName = time() . '.' . $request->image->extension();
                    $request->image->move(public_path('uploads/resource'), $fileName);
                    $data['image'] = $fileName;
                } else {
                    $data['image'] = $user['image'];
                }
                // return $data;
                Resource::addEdit($data);
                return redirect('admin/manage-resource')->with('success', 'Record updated successfully');
            }
        }
    }

    public function delete($user_id)
    {
        $del = Resource::where('id', $user_id)->update(['deleted_at' => date('Y-m-d h:i:s')]);
        if ($del) {
            return redirect()->back()->with('success', 'Record deleted successfully');
        } else {
            return redirect()->back()->with('error', COMMON_ERROR);
        }
    }
}
