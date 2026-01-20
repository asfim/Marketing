<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function index()
    {
        return view('admin.pages.settings');
    }

    public function store(Request $request)
    {

            $this->validate($request, [
                'company_name' => 'required',
                'address' => 'required',
                'phone' => 'nullable|numeric',
                'invoice_prefix' => 'required',
                'logo' => 'image|mimes:jpeg,jpg,png|max:1024|file',
                'favicon' => 'image|mimes:jpeg,jpg,png,ico|max:256|file',
                ],
                ['image.dimensions' => 'Please Upload 200x200 pixel size image!']
            );

            DB::beginTransaction();
            try {
                foreach ($request->all() as $key => $value) {
                    if($key != '_token') {
                        $config = Config::where('config_title', $key)->first();
                        if (!empty($config)) {
                            $config->update(['value' => $value]);
                        }
                    }
                }

                if (request()->hasFile('logo')) {
                    $logo = Config::where('config_title', 'logo')->first();
                    $file = request()->file('logo');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = ('assets/images/');
                    if(!empty($logo->value)){
                        File::delete('assets/images/' . $logo->value);
                    }
                    $file->move($path, $fileName);
                    $logo->update(['value' => $fileName]);
                }

                if (request()->hasFile('favicon')) {
                    $favicon = Config::where('config_title', 'favicon')->first();
                    $file = request()->file('favicon');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $path = ('assets/images/');
                    if(!empty($favicon->value)){
                        File::delete('assets/images/' . $favicon->value);
                    }
                    $file->move($path, $fileName);
                    $favicon->update(['value' => $fileName]);
                }


                DB::commit();
                Session::flash('message', 'Config Update Successful!');
                Session::flash('m-class', 'alert-success');
                return redirect()->route('config.index');
            } catch( \Exception $e){
                DB::rollBack();
                Session::flash('message', 'Config Update failed '. $e);
                Session::flash('m-class', 'alert-danger');
                return redirect()->route('config.index');
            }

    }
}
