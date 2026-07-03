<?php

declare(strict_types=1);

namespace App;

use App\Database\Connection;
use App\Http\Controller\ArticleController;
use App\Http\Controller\CategoryController;
use App\Http\Controller\HomeController;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryInterface;
use App\Repository\CategoryRepository;
use App\Repository\CategoryRepositoryInterface;
use App\Service\ArticleService;
use App\Service\CategoryService;
use PDO;
use RuntimeException;
use Smarty;

/**
 * Minimal hand-rolled DI container — explicit wiring, no magic.
 */
final class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            $this->instances[$id] = $this->resolve($id);
        }

        return $this->instances[$id];
    }

    private function resolve(string $id): object
    {
        return match ($id) {
            Config::class => $this->config,
            PDO::class => Connection::get($this->config),
            CategoryRepositoryInterface::class => new CategoryRepository($this->get(PDO::class)),
            ArticleRepositoryInterface::class => new ArticleRepository($this->get(PDO::class)),
            CategoryService::class => new CategoryService(
                $this->get(CategoryRepositoryInterface::class),
                $this->get(ArticleRepositoryInterface::class),
                $this->config,
            ),
            ArticleService::class => new ArticleService(
                $this->get(ArticleRepositoryInterface::class),
                $this->config,
            ),
            Smarty::class => $this->createSmarty(),
            HomeController::class => new HomeController(
                $this->get(CategoryService::class),
                $this->get(Smarty::class),
            ),
            CategoryController::class => new CategoryController(
                $this->get(CategoryService::class),
                $this->get(Smarty::class),
            ),
            ArticleController::class => new ArticleController(
                $this->get(ArticleService::class),
                $this->get(Smarty::class),
            ),
            default => throw new RuntimeException("Unknown service: {$id}"),
        };
    }

    private function createSmarty(): Smarty
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($this->config->get('smarty.template_dir'));
        $smarty->setCompileDir($this->config->get('smarty.compile_dir'));
        $smarty->setCacheDir($this->config->get('smarty.cache_dir'));
        $smarty->escape_html = true;
        $smarty->assign('app_name', 'Blog CMS');

        return $smarty;
    }
}
