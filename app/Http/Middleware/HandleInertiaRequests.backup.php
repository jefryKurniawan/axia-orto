<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // $ziggyData = [];

        // try {
        //     // Untuk Ziggy v1.x
        //     $ziggy = new \Tightenco\Ziggy\Ziggy;
        //     $ziggyData = $ziggy->toArray();
        // } catch (\Exception $e) {
        //     $ziggyData = $this->getManualRoutes();
        // }

        // try {
        //     // Coba dengan namespace Ziggy v2.x
        //     if (class_exists(\Tighten\Ziggy\Ziggy::class)) {
        //         $ziggy = new \Tighten\Ziggy\Ziggy;
        //         $ziggyData = $ziggy->toArray();
        //     }
        //     // Fallback ke namespace Ziggy v1.x
        //     elseif (class_exists(\Tightenco\Ziggy\Ziggy::class)) {
        //         $ziggy = new \Tightenco\Ziggy\Ziggy;
        //         $ziggyData = $ziggy->toArray();
        //     }
        // } catch (\Exception $e) {
        //     // Jika error, buat manual routes
        //     $ziggyData = $this->getManualRoutes();
        // }

        // // Tambahkan location dan query
        // $ziggyData['location'] = $request->url();
        // $ziggyData['query'] = $request->query();

        // Debug: Log untuk memastikan middleware berjalan
        \Log::info('HandleInertiaRequests executed for: ' . $request->url());

        // Untuk Ziggy v1.x
        $ziggy = new Ziggy;
        $ziggyArray = $ziggy->toArray();

        // Debug: Log Ziggy data
        \Log::info('Ziggy routes count: ' . count($ziggyArray['namedRoutes'] ?? []));
        \Log::info('Ziggy routes:', array_keys($ziggyArray['namedRoutes'] ?? []));

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
            'ziggy' => array_merge($ziggyArray, [
                'location' => $request->url(),
            ]),
        ]);

        // return [
        //     'auth' => [
        //         'user' => $request->user(),
        //     ],
        //     'flash' => [
        //         'message' => fn() => $request->session()->get('message')
        //     ],
        //     'ziggy' => function () use ($request) {
        //         return array_merge((new Ziggy)->toArray(), [
        //             'location' => $request->url(),
        //             'query' => $request->query(),
        //         ]);
        //     },
        // ];

    }

    protected function getManualRoutes(): array
    {
        return [
            'namedRoutes' => [
                'login' => [
                    'uri' => 'login',
                    'methods' => ['GET', 'HEAD'],
                ],
                'register' => [
                    'uri' => 'register',
                    'methods' => ['GET', 'HEAD'],
                ],
                'dashboard' => [
                    'uri' => 'dashboard',
                    'methods' => ['GET', 'HEAD'],
                ],
                'patients.index' => [
                    'uri' => 'patients',
                    'methods' => ['GET', 'HEAD'],
                ],
                'patients.store' => [
                    'uri' => 'patients',
                    'methods' => ['POST'],
                ],
                'patients.create' => [
                    'uri' => 'patients/create',
                    'methods' => ['GET', 'HEAD'],
                ],
                'patients.show' => [
                    'uri' => 'patients/{patient}',
                    'methods' => ['GET', 'HEAD'],
                ],
                'patients.edit' => [
                    'uri' => 'patients/{patient}/edit',
                    'methods' => ['GET', 'HEAD'],
                ],
            ],
            'baseUrl' => config('app.url'),
            'baseProtocol' => parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'http',
            'baseDomain' => parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost',
            'basePort' => parse_url(config('app.url'), PHP_URL_PORT) ?? null,
            'defaultParameters' => [],
        ];
    }
}
