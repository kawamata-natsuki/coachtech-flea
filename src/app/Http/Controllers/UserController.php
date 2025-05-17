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
        $rules = array_merge(
            (new AddressRequest())->rules(),
            (new ProfileRequest())->rules()
        );
        $messages = array_merge(
            (new AddressRequest())->messages(),
            (new ProfileRequest())->messages()
        );

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

        // base64を画像として保存
        if ($request->filled('cropped_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $imageData = $request->input('cropped_image');
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = base64_decode($imageData);

            $fileName = 'profile_images/' . uniqid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);
            $user->profile_image = $fileName;
        }

        $user->save();

        // 初回（会員登録直後）はトップページへ遷移
        if (session('profile_edit_first_time')) {
            session()->forget('profile_edit_first_time');
            return redirect('/')->with('success', 'プロフィールを登録しました');
        }
        // 2回目以降（通常のプロフィール更新）は編集画面のまま
        return redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');

        $sellingItems = $user->items;
        $purchasedItems = $user->orders()->with('item')->get();

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

        unset($rules['name'], $messages['name.required']);
        $request->validate($rules, $messages);

        $user = auth()->user();
        $user->update($request->only(['postal_code', 'address', 'building']));

        auth()->user()->refresh();

        return redirect()->route('purchase.show', [
            'item' => $item->id,
        ])->withInput($request->only('payment_method'))->with('success', '住所を更新しました');
    }
}