<?php

namespace App\Http\Controllers;

use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 商品一覧画面の表示
    public function index(Request $request)
    {
        $tab = $request->query('page', 'all');
        $keyword = $request->query('keyword');
        $user = auth()->user();

        // メニュータブの「おすすめ」「マイリスト」切り替え
        if ($tab === 'mylist') {
            $items = $user
                ? $this->getFavoriteItems($keyword, $user)
                : collect();
        } else {
            $items = $this->getRecommendedItems($keyword);
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

        $conditionLabel = ConditionConstants::label(
            ConditionConstants::idToCode($item->condition_id)
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
        $item->description = $request->input('description');
        $item->item_image = $path ?? null;
        $item->condition_id = ConditionConstants::codeToId($request->input('condition_code'));
        $item->price = $request->input('price');
        $item->user_id = auth()->id();
        $item->item_status = 'on_sale';
        $item->save();

        if ($request->filled('category_codes')) {
            $categoryIds = CategoryConstants::codesToIds($request->input('category_codes'));
            $item->categories()->attach($categoryIds);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }

    // おすすめタブに表示する商品を取得する
    private function getRecommendedItems($keyword)
    {
        return Item::withCount('favorites')
            // ユーザーがログインしてる時だけ、自分の出品商品を除外
            ->when(auth()->check(), fn($query) => $query->where('user_id', '!=', auth()->id()))
            // 検索ワードがあるときだけ、商品名を部分一致で検索(2文字以上)
            ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('name', 'like', "%{$keyword}%"))
            // 売り切れ商品を下に表示
            ->orderByRaw("FIELD(item_status, 'on_sale', 'sold_out')")
            // いいねの数が多い順に並び替え
            ->orderByDesc('favorites_count')
            // いいね数が同じなら、新しい順に並べる
            ->orderByDesc('created_at')
            ->get();
    }

    // マイリストに表示する商品を取得する
    private function getFavoriteItems($keyword, $user)
    {
        return $user->favoriteItems()
            ->where('items.user_id', '!=', $user->id)
            ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('items.name', 'like', "%{$keyword}%"))
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->orderByDesc('items.created_at')
            ->distinct() //
            ->get();
    }
}
