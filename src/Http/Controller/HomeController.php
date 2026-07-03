<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;
use Smarty\Smarty;

final class HomeController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly Smarty $smarty,
    ) {
    }

    public function index(Request $request): Response
    {
        $categories = $this->categoryService->getHomePageData();

        $this->smarty->assign('categories', $categories);
        $this->smarty->assign('page_title', 'Главная');

        return Response::html($this->smarty->fetch('pages/home.tpl'));
    }
}
