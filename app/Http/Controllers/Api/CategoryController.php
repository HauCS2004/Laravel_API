<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
  public function index()
    {
        $category = Category::all(); // Lấy hết
        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }
    public function store(Request $request){
        $validation=Validator::make($request->all(),[
            'name'=>"required|string|max:255|unique:categories,name"
        ]);
        if($validation->fails()){
            return response()->json([
                'message'=>"loi nhap du lieu"
            ],401);
        }
        $category=Category::create([
            'name'=>$request->name
        ]);
        return response()->json([
            "status"=>true,
            "message"=>"add success",
            "data"=>$category
        ],200);
    }

    public function update(Request $request,$id){
        $category=Category::find($id);
        if(!$category){
            return response()->json(['message'=>"khong tim thay"],401);
        }
        $validation=Validator::make($request->all(),[
            "name"=>"required|string|max:255"
        ]);
        if($validation->fails()){
            return response()->json(["message"=>"loi nhap du lieu"]);
        }
        $category->update(['name'=>$request->name]);
      return response()->json([
        'status'=>true,
        "message"=>"update success",
         "data"=>$category  
        ],200);
    }

    public function delete($id){
        $category=Category::find($id);
        if(!$category){ return response()->json(['message'=>"khong tim thay"]);}
        $category->delete();
        
        return response()->json([
            'status'=>true,
            "message"=>"delete success"
        ],200);
    }


}
