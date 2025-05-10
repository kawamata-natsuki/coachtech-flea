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
        // URLのクエリパラメータを取得
        $tab = $request->query('page', 'all');
        // 検索窓の入力内容を取得
        $keyword = $request->query('keyword');
        $user = auth()->user();

        if ($tab === 'mylist') {
            $items = $user
                ? $this->getFavoriteItems($keyword, $user) // ログインしていればいいねした商品を取得
                : collect(); // 未ログインなら空のコレクションを返す
        } else {
            $items = $this->getRecommendedItems($keyword);
        }
        return view('items.index', compact('items', 'tab'));
    }

    // 商品詳細画面の表示
    public function show(Item $item)
    {
        $item->load([
            'comments' => fn($query) => $query->latest(),
            'categories',
            'favorites',
        ]);

        return view('items.detail', compact('item'));
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
            // 検索ワードがあるときだけ、商品名を部分一致で検索
            ->when($keyword, fn($query) => $query->where('name', 'like', "%{$keyword}%"))
            // いいねの数が多い順に並び替え
            ->orderByDesc('favorites_count')
            // いいね数が同じなら、新しい順に並べる
            ->orderByDesc('created_at')
            ->get();
    }

    // マイリストに表示する商品を取得する
    private function getFavoriteItems($keyword, $user)
    {
        return $user->favoriteItems->filter(function ($item) use ($keyword, $user) {
            return (
                // キーワードがある場合は商品名に部分一致（大文字小文字を無視）
                (!$keyword || str_contains(mb_strtolower($item->name), mb_strtolower($keyword)))
                // 自分が出品した商品は除外
                && $item->user_id !== $user->id
            );
        });
    }
}
