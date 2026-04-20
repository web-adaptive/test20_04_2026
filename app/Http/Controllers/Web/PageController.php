<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Catalog/Home');
    }

    public function productShow(int $id): Response
    {
        return Inertia::render('Catalog/ProductShow', [
            'productId' => $id,
        ]);
    }

    public function login(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function adminProducts(): Response
    {
        return Inertia::render('Admin/Products/Index');
    }

    public function adminProductCreate(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'mode' => 'create',
            'productId' => null,
        ]);
    }

    public function adminProductEdit(int $id): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'mode' => 'edit',
            'productId' => $id,
        ]);
    }
}
