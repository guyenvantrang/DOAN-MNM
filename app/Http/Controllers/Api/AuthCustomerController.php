<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthCustomerController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/register",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Đăng ký tài khoản",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"hoten","email","password","password_confirmation","sdt"},
     * @OA\Property(property="hoten", type="string", example="Nguyễn Văn A"),
     * @OA\Property(property="email", type="string", format="email", example="khach@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="123456"),
     * @OA\Property(property="password_confirmation", type="string", example="123456"),
     * @OA\Property(property="sdt", type="string", example="0909123456"),
     * @OA\Property(property="diachi", type="string", example="TP.HCM")
     * )
     * ),
     * @OA\Response(response=200, description="Đăng ký thành công")
     * )
     */
    public function register(Request $request) {
        $request->validate([
            'hoten' => 'required|string|max:100',
            'email' => 'required|email|unique:khachhang,EMAIL',
            'password' => 'required|min:6|confirmed',
            'sdt' => 'required|string|max:15'
        ]);

        $user = KhachHang::create([
            'HOTEN' => $request->hoten,
            'EMAIL' => $request->email,
            'SDT' => $request->sdt,
            'DIACHI' => $request->diachi,
            'MATKHAU' => Hash::make($request->password),
            'TRANGTHAI' => 1,
            'NGAYTAO' => now()
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký thành công!',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Đăng nhập",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", example="khach@gmail.com"),
     * @OA\Property(property="password", type="string", example="123456")
     * )
     * ),
     * @OA\Response(response=200, description="Trả về Token")
     * )
     */
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = KhachHang::where('EMAIL', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->MATKHAU)) {
            return response()->json(['status' => 'error', 'message' => 'Email hoặc mật khẩu không đúng'], 401);
        }

        if ($user->TRANGTHAI == 0) {
            return response()->json(['status' => 'error', 'message' => 'Tài khoản đã bị khóa.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/user",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Lấy thông tin cá nhân",
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Thông tin user")
     * )
     */
    public function profile(Request $request) {
        return response()->json($request->user());
    }

    /**
     * @OA\Put(
     * path="/api/user/update",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Cập nhật thông tin cá nhân",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="hoten", type="string"),
     * @OA\Property(property="sdt", type="string"),
     * @OA\Property(property="diachi", type="string")
     * )
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function updateProfile(Request $request) {
        $user = $request->user();
        $request->validate([
            'hoten' => 'required|string|max:100',
            'sdt' => 'required|string|max:15',
        ]);

        $user->update([
            'HOTEN' => $request->hoten,
            'SDT' => $request->sdt,
            'DIACHI' => $request->diachi
        ]);

        return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công', 'data' => $user]);
    }

    /**
     * @OA\Put(
     * path="/api/user/change-password",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Đổi mật khẩu",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * required={"current_password","new_password","new_password_confirmation"},
     * @OA\Property(property="current_password", type="string"),
     * @OA\Property(property="new_password", type="string"),
     * @OA\Property(property="new_password_confirmation", type="string")
     * )
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed|different:current_password'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->MATKHAU)) {
            return response()->json(['status' => 'error', 'message' => 'Mật khẩu hiện tại không đúng'], 400);
        }

        $user->update(['MATKHAU' => Hash::make($request->new_password)]);
        // Xóa các token khác để đăng xuất thiết bị cũ
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Đổi mật khẩu thành công']);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * tags={"Khách hàng - Tài khoản"},
     * summary="Đăng xuất",
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Đã đăng xuất")
     * )
     */
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Đã đăng xuất']);
    }
}