<?php

namespace App\Http\Controllers\backEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

use App\Models\Doctor;
use Hash;
use PhpParser\Comment\Doc;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $users    = Doctor::get();
        $page = 'doctors';
        return view('backEnd.doctor.index', compact('page', 'users'));
    }

    public function add(Request $request, $id = null)
    {
        if ($request->isMethod('GET')) {
            if ($id) {
                $user = Doctor::where('id', $id)->first();
            } else {
                $user = [];
            }
            $page = 'doctors';
            return view('backEnd.doctor.form', compact('page', 'user'));
        }

        if ($request->isMethod('post')) {
            $data = $request->all();
            $validator = Validator::make(
                $data,
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . @$data['id'] . ',id,deleted_at,NULL',
                    'password' => @$data['id'] ? 'nullable' : 'required',
                    'mobile_number' => 'required|numeric',
                    'gender' => 'required',
                    'location' => 'required',
                    'speciality' => 'required',
                    'description' => 'required',
                    'status' => 'required',
                    'profile_image' => @$data['id'] ? 'nullable|' : 'required|' . 'image|mimes:jpeg,png,jpg|max:2048'
                ]
            );

            if ($validator->fails()) {
                return back()->withInput($request->all())->withErrors($validator->errors());
            } else {
                $user = Doctor::where('id', $request->id)->first();
                if ($request->profile_image) {
                    $fileName = time() . '.' . $request->profile_image->extension();
                    $request->profile_image->move(public_path('uploads/doctor'), $fileName);
                    $data['profile_image'] = $fileName;
                } else {
                    $data['profile_image'] = $user['profile_image'];
                }
                if ($request->password) {
                    $data['password'] = Hash::make($request->password);
                } else {
                    $data['password'] = $user['password'];
                }
                Doctor::addEdit($data);
                return redirect('admin/manage-doctor')->with('success', 'Record updated successfully');
            }
        }
    }

    public function delete($user_id)
    {
        $del = User::where('id', $user_id)->update(['deleted_at' => date('Y-m-d h:i:s')]);
        if ($del) {
            return redirect()->back()->with('success', 'User deleted successfully');
        } else {
            return redirect()->back()->with('error', COMMON_ERROR);
        }
    }

}
