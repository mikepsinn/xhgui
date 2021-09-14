<?php

namespace XHGui\ServiceProvider;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Slim as App;
use Slim\Views\Twig;
use XHGui\Controller;
use XHGui\Twig\TwigExtension;

class RouteProvider implements ServiceProviderInterface
{
    public function register(Container $di): void
    {
        $this->registerRoutes($di, $di['app']);
        $this->registerControllers($di);
    }

    private function registerRoutes(Container $di, App $app): void
    {
        $app->error(static function (Exception $e) use ($di, $app): void {
            /** @var Twig $view */
            $view = $di['view'];
            $view->parserOptions['cache'] = false;
            $view->parserExtensions = [
                new TwigExtension($app),
            ];

            $app->view($view);
            $app->render('error/view.twig', [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
        });
		$prefix = "";
		if($env = getenv("XHGUI_PATH_PREFIX")){
			$prefix = "/$env";
		}
        // Profile Runs routes
        $app->get($prefix.'/', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->index($request, $response);
        })->setName('home');

        $app->get($prefix.'/run/view', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->view($request, $response);
        })->setName('run.view');

        $app->get($prefix.'/run/delete', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->deleteForm($request);
        })->setName('run.delete.form');

        $app->post($prefix.'/run/delete', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->deleteSubmit($request);
        })->setName('run.delete.submit');

        $app->get($prefix.'/run/delete_all', static function () use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $controller->deleteAllForm();
        })->setName('run.deleteAll.form');

        $app->post($prefix.'/run/delete_all', static function () use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $controller->deleteAllSubmit();
        })->setName('run.deleteAll.submit');

        $app->get($prefix.'/url/view', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->url($request);
        })->setName('url.view');

        $app->get($prefix.'/run/compare', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->compare($request);
        })->setName('run.compare');

        $app->get($prefix.'/run/symbol', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->symbol($request);
        })->setName('run.symbol');

        $app->get($prefix.'/run/symbol/short', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->symbolShort($request);
        })->setName('run.symbol-short');

        $app->get($prefix.'/run/callgraph', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();

            $controller->callgraph($request);
        })->setName('run.callgraph');

        $app->get($prefix.'/run/callgraph/data', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->callgraphData($request, $response);
        })->setName('run.callgraph.data');

        $app->get($prefix.'/run/callgraph/dot', static function () use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di[Controller\RunController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->callgraphDataDot($request, $response);
        })->setName('run.callgraph.dot');

        // Import route
        $app->post($prefix.'/run/import', static function () use ($di, $app): void {
            /** @var Controller\ImportController $controller */
            $controller = $di[Controller\ImportController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->import($request, $response);
        })->setName('run.import');

        // Watch function routes.
        $app->get($prefix.'/watch', static function () use ($di): void {
            /** @var Controller\WatchController $controller */
            $controller = $di[Controller\WatchController::class];
            $controller->get();
        })->setName('watch.list');

        $app->post($prefix.'/watch', static function () use ($di, $app): void {
            /** @var Controller\WatchController $controller */
            $controller = $di[Controller\WatchController::class];
            $request = $app->request();

            $controller->post($request);
        })->setName('watch.save');

        // Custom report routes.
        $app->get($prefix.'/custom', static function () use ($di): void {
            /** @var Controller\CustomController $controller */
            $controller = $di[Controller\CustomController::class];
            $controller->get();
        })->setName('custom.view');

        $app->get($prefix.'/custom/help', static function () use ($di, $app): void {
            /** @var Controller\CustomController $controller */
            $controller = $di[Controller\CustomController::class];
            $request = $app->request();

            $controller->help($request);
        })->setName('custom.help');

        $app->post($prefix.'/custom/query', static function () use ($di, $app): void {
            /** @var Controller\CustomController $controller */
            $controller = $di[Controller\CustomController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->query($request, $response);
        })->setName('custom.query');

        // Waterfall routes
        $app->get($prefix.'/waterfall', static function () use ($di): void {
            /** @var Controller\WaterfallController $controller */
            $controller = $di[Controller\WaterfallController::class];
            $controller->index();
        })->setName('waterfall.list');

        $app->get($prefix.'/waterfall/data', static function () use ($di, $app): void {
            /** @var Controller\WaterfallController $controller */
            $controller = $di[Controller\WaterfallController::class];
            $request = $app->request();
            $response = $app->response();

            $controller->query($request, $response);
        })->setName('waterfall.data');

        // Metrics
        $app->get($prefix.'/metrics', static function () use ($di, $app): void {
            /** @var Controller\MetricsController $controller */
            $controller = $di[Controller\MetricsController::class];
            $response = $app->response();

            $controller->metrics($response);
        })->setName('metrics');
    }

    private function registerControllers(Container $app): void
    {
        $app[Controller\WatchController::class] = $app->factory(static function ($app) {
            return new Controller\WatchController($app['app'], $app['searcher']);
        });

        $app[Controller\RunController::class] = $app->factory(static function ($app) {
            return new Controller\RunController($app['app'], $app['searcher']);
        });

        $app[Controller\CustomController::class] = $app->factory(static function ($app) {
            return new Controller\CustomController($app['app'], $app['searcher']);
        });

        $app[Controller\WaterfallController::class] = $app->factory(static function ($app) {
            return new Controller\WaterfallController($app['app'], $app['searcher']);
        });

        $app[Controller\ImportController::class] = $app->factory(static function ($app) {
            return new Controller\ImportController($app['app'], $app['saver'], $app['config']['upload.token']);
        });

        $app[Controller\MetricsController::class] = $app->factory(static function ($app) {
            return new Controller\MetricsController($app['app'], $app['searcher']);
        });
    }
}
