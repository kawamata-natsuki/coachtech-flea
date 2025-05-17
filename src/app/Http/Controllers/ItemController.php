<?php

namespace App\Http\Controllers;

use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
use App\Constants\ItemStatus;
use App\Http\Requests\ExhibitionRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ConditionRepository;
use App\Repositories\ItemRepository;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store']);
    }

    // 商品一覧画面の表示（タブ切り替え対応）
    public function index(Request $request)
    {
        $tab = $request->query('page', 'all');
        $keyword = $request->query('keyword');
        $user = auth()->user();

        if ($tab === 'mylist') {
            $items = $user
                ? (new ItemRepository)->getFavoriteItems($keyword, $user)
                : collect();
        } else {
            $items = (new ItemRepository)->getRecommendedItems($keyword, $user?->id);
        }
        return view('items.index', compact('items', 'tab'));
    }

    // 商品詳細画面の表示
    public function show(Item $item)
    {
        $item->load([
            'comments' => fn($query) => $query->latest()->with('user'),
            'categories',
            'favorites',
        ]);

        $categoryLabels = $item->categories->map(function ($category) {
            return CategoryConstants::label($category->code);
        });

        $conditionCode = ConditionRepository::getCodeById($item->condition_id);
        $conditionLabel = ConditionConstants::label(
            $conditionCode
        );

        return view('items.detail', compact('item', 'categoryLabels', 'conditionLabel'));
    }

    // 商品出品画面の表示
    public function create()
    {
        return view('items.create');
    }

    // 商品出品の処理
    public function store(ExhibitionRequest $request)
    {
        // 商品画像の保存処理
        if ($request->hasfile('item_image')) {
            $path = $request->file('item_image')->store('items', 'public');
        }

        // 商品保存処理
        $item = new Item();
        $item->name = $request->input('name');
        $item->brand = $request->input('brand');
        $item->description = $request->input('description');
        $item->item_image = $path ?? null;
        $item->condition_id = ConditionRepository::getIdByCode($request->input('condition_code'));
        $item->price = $request->input('price');
        $item->user_id = auth()->id();
        $item->item_status = ItemStatus::ON_SALE;
        $item->save();

        if ($request->filled('category_codes')) {
            $categoryIds = CategoryRepository::getIdsByCodes($request->input('category_codes'));
            $item->categories()->attach($categoryIds);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }
}
