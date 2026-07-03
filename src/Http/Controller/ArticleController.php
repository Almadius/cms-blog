<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Service\ArticleService;
use Smarty\Smarty;

final class ArticleController
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly Smarty $smarty,
    ) {
    }

    public function show(Request $request): Response
    {
        $slugOrId = $request->getRouteParam('slugOrId', '');

        try {
            $data = $this->articleService->getArticlePage($slugOrId);
        } catch (NotFoundException) {
            return $this->render404();
        }

        $this->smarty->assign('article', $data['article']);
        $this->smarty->assign('similar', $data['similar']);
        $this->smarty->assign('page_title', $data['article']->title);

        return Response::html($this->smarty->fetch('pages/article.tpl'));
    }

    private function render404(): Response
    {
        $this->smarty->assign('page_title', 'Не найдено');
        $this->smarty->assign('message', 'Статья не найдена');

        return Response::notFound($this->smarty->fetch('pages/error.tpl'));
    }
}
