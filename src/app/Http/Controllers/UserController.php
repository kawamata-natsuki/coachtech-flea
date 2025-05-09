<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
        // ルールとメッセージを合体！
        $rules = array_merge(
            (new AddressRequest())->rules(),
            (new ProfileRequest())->rules()
        );

        $messages = array_merge(
            (new AddressRequest())->messages(),
            (new ProfileRequest())->messages()
        );

        // バリデーション実行
        $request->validate($rules, $messages);

        // 更新処理
        $user = auth()->user();
        $user->fill($request->only(['name', 'postal_code', 'address', 'building']));

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        // 遷移分岐
        if (session('profile_edit_first_time')) {
            session()->forget('profile_edit_first_time');
            return redirect('/')->with('success', 'プロフィールを登録しました'); // 初回はトップページへ
        }

        return redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell'); // デフォルトは出品商品

        $sellingItems = $user->items; // 出品した商品
        $purchasedItems = $user->orders()->with('item')->get(); // 購入した商品（order経由）

        return view('user.profile', compact('user', 'page', 'sellingItems', 'purchasedItems'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('user.profile-edit', compact('user'));
    }

    // 購入画面用の住所変更フォームの表示
    public function editAddress(Item $item)
    {
        $user = auth()->user();
        return view('user.profile-address', compact('user', 'item'));
    }

    // 購入画面用の住所変更の保存処理
    public function updateAddress(Request $request, Item $item)
    {
        $rules = (new AddressRequest())->rules();
        $messages = (new AddressRequest())->messages();

        // nameバリデーションは除外
        unset($rules['name'], $messages['name.required']);

        $request->validate($rules, $messages);

        // 更新処理
        $user = auth()->user();
        $user->update($request->only(['postal_code', 'address', 'building']));

        // ★ 更新後にリフレッシュしてセッション内のユーザー情報も最新にする
        auth()->user()->refresh();

        // 保存後、元の商品購入画面に戻る
        return redirect()->route('purchase.show', [
            'item' => $item->id,
        ])->withInput($request->only('payment_method'))->with('success', '住所を更新しました');
    }
}
