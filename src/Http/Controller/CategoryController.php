<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;
use Smarty;

final class CategoryController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly Smarty $smarty,
    ) {
    }

    public function show(Request $request): Response
    {
        $slugOrId = $request->getRouteParam('slugOrId', '');
        $sort = (string) $request->getQuery('sort', 'date');
        $order = (string) $request->getQuery('order', 'desc');
        $page = $request->getIntQuery('page', 1);

        try {
            $data = $this->categoryService->getCategoryPage($slugOrId, $sort, $order, $page);
        } catch (NotFoundException) {
            return $this->render404();
        }

        $this->smarty->assign('category', $data['category']);
        $this->smarty->assign('articles', $data['articles']);
        $this->smarty->assign('pagination', $data['pagination']);
        $this->smarty->assign('page_title', $data['category']->name);

        return Response::html($this->smarty->fetch('pages/category.tpl'));
    }

    private function render404(): Response
    {
        $this->smarty->assign('page_title', 'Не найдено');
        $this->smarty->assign('message', 'Категория не найдена');

        return Response::notFound($this->smarty->fetch('pages/error.tpl'));
    }
}
