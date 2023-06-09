<?php

namespace Modules\Shop\Http\Controllers;

use App\Models\brand;
use App\Models\Category;
use App\Models\info;
use App\Models\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $menus = Product::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
            return DataTables::of($menus)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct"><i class="fa-solid fa-pen-to-square"></i></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct"><i class="fa-solid fa-trash"></i></a>';

                    $btn = $btn . ' <a href="/admin/admin-detail-room/' . $row->id . '"  data-id="' . $row->id . '" class="btn btn-info btn-sm"><i class="fa-solid fa-circle-info"></i></a>';
                    return $btn;
                })
                ->make(true);
        }
        return view('shop::product.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $category = Category::all();
        $brand = brand::all();
        return view('shop::product.create', ['category' => $category, 'brand' => $brand]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $array = [
            'name' => $request->name1,
            'value' => $request->value1
        ];
        $result = array_map(function ($a, $b) {
            return [
                'name' => $a,
                'value' => $b
            ];
        }, $request->name1, $request->value1);

        $data = Product::Create([
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'desc' => $request->desc,
            'info' => $request->info,
            'name' => $request->name,
            'price' => $request->price,
            'sale' => $request->sale,
            'quantity' => $request->quantity,
            // 'image' => $request->image,
            'code_product' => strtoupper(Str::random(8)),
            'view' => 0
        ]);
        $product = Product::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->first();
        foreach ($result as $row) {
            $info =  info::Create([
                'name' => $row['name'],
                'value' => $row['value'],
                'product_id' => $product->id
            ]);
        }
        return response()->json(['data' => $data, 'info' => $info]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('shop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('shop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        Product::find($id)->delete();
        info::where('product_id', $id)->delete();
        return response()->json(['status' => 1, 'success' => "xóa thành công"]);
    }
}
