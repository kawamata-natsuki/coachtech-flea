<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // マイページのプロフィール表示
    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');

        // 出品商品
        $sellingItems = $user->items()->latest()->get();

        // 購入商品
        $purchasedItems = $user->orders()->with('item')->latest()->get();

        // 取引中商品取得
        $tradingItems = Order::with(['item'])
            ->tradingOrPendingReview()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('item', function ($q) {
                        $q->where('user_id', auth()->id());
                    });
            })
            ->get();

        // 全商品のチャット未読数を合計
        $totalUnreadCount = $tradingItems->sum(fn($entry) => $entry->unread_count ?? 0);

        return view('user.profile', compact(
            'user',
            'page',
            'sellingItems',
            'purchasedItems',
            'tradingItems',
            'totalUnreadCount',
        ));
    }

    // プロフィール編集画面の表示
    public function edit()
    {
        $user = auth()->user();
        return view('user.profile-edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(Request $request)
    {
        // 複数のバリデーションルールとメッセージを統合して、バリデーション実行
        $rules = array_merge(
            (new AddressRequest())->rules(),
            (new ProfileRequest())->rules()
        );
        $messages = array_merge(
            (new AddressRequest())->messages(),
            (new ProfileRequest())->messages()
        );
        $request->validate($rules, $messages);

        // ユーザー情報を更新し、DBに保存
        $user = auth()->user();
        $user->fill($request->only(['name', 'postal_code', 'address', 'building']));
        $this->handleProfileImageUpload($request, $user);
        $user->save();

        // 初回（会員登録直後）はトップページへリダイレクト
        if (session('profile_edit_first_time')) {
            session()->forget('profile_edit_first_time');
            return redirect('/')->with('success', 'プロフィールを登録しました');
        }
        // 2回目以降（通常のプロフィール更新）は編集画面のまま
        return redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました');
    }

    // 商品購入画面用の住所変更フォームを表示
    public function editAddress(Item $item)
    {
        $user = auth()->user();
        return view('user.profile-address', compact('user', 'item'));
    }

    // 購入画面用の住所変更を保存する処理
    public function updateAddress(Request $request, Item $item)
    {
        // 複数のバリデーションルールとメッセージを統合して、バリデーション実行
        $rules = (new AddressRequest())->rules();
        $messages = (new AddressRequest())->messages();
        unset($rules['name'], $messages['name.required']);
        $request->validate($rules, $messages);

        // ユーザー住所情報を更新
        $user = auth()->user();
        $user->update($request->only(['postal_code', 'address', 'building']));

        auth()->user()->refresh();

        // 元の購入画面に戻る（支払い方法選択状態を保持）
        return redirect()->route('purchase.show', [
            'item' => $item->id,
        ])->withInput($request->only('payment_method'))->with('success', '住所を更新しました');
    }

    // プロフィール画像のアップロード処理
    private function handleProfileImageUpload(Request $request, $user)
    {
        // 通常の画像ファイルがアップロードされた場合
        if ($request->hasFile('profile_image')) {
            // 既存の画像があれば削除、新しい画像を保存してパスを設定
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // base64形式（Cropper.js等）の画像データがアップロードされた場合
        if ($request->filled('cropped_image')) {
            // 既存の画像があれば削除、base64データをデコードして画像として保存
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $imageData = base64_decode(
                preg_replace('/^data:image\/\w+;base64,/', '', $request->input('cropped_image'))
            );

            $fileName = 'profile_images/' . uniqid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);
            $user->profile_image = $fileName;
        }
    }
}
