<?php

namespace App\Http\Controllers;

use App\Constants\Category;
use App\Constants\Condition;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // 商品一覧画面の表示
    public function index(Request $request)
    {
        $tab = $request->query('page', 'all');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist') {
            // ログイン済ならお気に入り取得、未ログインなら空のコレクションを渡す
            if (auth()->check()) {
                $user = auth()->user();

                $items = $user->favoriteItems->filter(function ($item) use ($keyword, $user) {
                    return (
                        (!$keyword || str_contains(mb_strtolower($item->name), mb_strtolower($keyword)))
                        && $item->user_id !== $user->id
                    );
                });
            } else {
                $items = collect();
            }
        } else {
            // オススメタブ全商品（自分の出品商品除外）＋いいねの多い順
            $items = Item::withCount('favorites')
                ->when(auth()->check(), function ($query) {
                    return $query->where('user_id', '!=', auth()->id());
                })
                // 部分一致検索
                // SQLのlikeは大文字小文字区別しない
                ->when($keyword, function ($query) use ($keyword) {
                    return $query->where('name', 'like', '%' . $keyword . '%');
                })
                ->orderByDesc('favorites_count')
                ->orderBy('created_at',)
                ->get();
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
        $item->condition_id = Condition::codeToId($request->input('condition_code'));
        $item->price = $request->input('price');
        $item->user_id = Auth::id();
        $item->item_status = 'on_sale';
        $item->save();

        if ($request->filled('category_codes')) {
            $categoryIds = Category::codesToIds($request->input('category_codes'));
            $item->categories()->attach($categoryIds);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }
}
