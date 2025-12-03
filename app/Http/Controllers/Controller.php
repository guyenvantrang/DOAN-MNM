<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Tài liệu API Watch Store",
 * description="Danh sách API cho ứng dụng Khách hàng (ReactJS)",
 * @OA\Contact(
 * email="admin@watchstore.com"
 * )
 * )
 *
 * @OA\Server(
 * url="http://127.0.0.1:8000",
 * description="Local API Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}