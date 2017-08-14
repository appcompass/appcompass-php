<?php
Route::group([
    'middleware' => ['web', 'cp'],
    'namespace' => 'P3in\Controllers',
], function ($router) {
    $router->get('routes', 'CpResourcesController@routes');
    $router->get('get-resources/{route?}', 'CpResourcesController@resources');
});

Route::group([
    'prefix' => 'auth',
    'middleware' => ['web'],
    'namespace' => 'P3in\Controllers',
], function ($router) {
    // login and auth check
    $router->post('login', 'AuthController@login');
    $router->get('logout', 'AuthController@logout')->middleware(['auth']);
    $router->get('user', 'AuthController@user')->middleware('auth');

    // registration
    $router->post('register', 'AuthController@register');
    $router->get('activate/{code}', 'AuthController@activate')->name('cp-activate-account');

    // password reset
    $router->post('password/email', 'PasswordController@sendResetLinkEmail');
    $router->post('password/reset', 'PasswordController@reset');
});

Route::group([
    'namespace' => 'P3in\Controllers',
    'middleware' => ['auth', 'api']
], function ($router) {
    $router->get('dashboard', 'CpResourcesController@getDashboard')->name('cp-dashboard');
    $router->resource('users', UsersController::class);
    $router->resource('roles', RolesController::class);
    $router->resource('roles.permissions', RolePermissionsController::class);
    $router->resource('permissions', PermissionsController::class);
    $router->resource('users.roles', UserRolesController::class);
    $router->resource('users.permissions', UserPermissionsController::class);

    $router->resource('galleries', GalleriesController::class);
    $router->resource('galleries.photos', GalleryPhotosController::class);
    $router->post('galleries/{gallery}/photos/sort', 'GalleryPhotosController@sort'); // @TODO see about this
    $router->resource('galleries.videos', GalleryVideosController::class);

    // $router->resource('menus', MenusController::class);
    $router->resource('pages', PagesController::class);
    $router->resource('pages.contents', PageContentController::class);
    $router->resource('websites', WebsitesController::class);
    $router->get('websites/{website}/setup', 'WebsiteSetupController@getSetup')->name('websites-setup');
    $router->match(['post', 'put'], 'websites/{website}/setup', 'WebsiteSetupController@postSetup')->name('websites-setup.update');
    // $router->get('websites/{website}/setup', 'WebsiteSetupController@getSetup')->name('websites-setup');
    $router->resource('websites.menus', WebsiteMenusController::class);
    // @TODO use generic forms getter once that's done (maybe)
    $router->get('websites/{website}/menus/forms/{form_name}', 'WebsiteMenusController@getForm');
    $router->post('websites/{website}/menus/forms/{form_name}', 'WebsiteMenusController@storeForm');
    // @TODO: should prob make a resourceful controller for websites.links, though some links can be set to website agnostic.
    $router->post('websites/{website}/menus/links', 'WebsiteMenusController@storeLink');
    $router->delete('websites/{website}/menus/links/{link_id}', 'WebsiteMenusController@deleteLink');
    $router->resource('websites.navigation', WebsiteMenusController::class);
    $router->resource('websites.pages', WebsitePagesController::class);
    $router->post('websites/{website}/pages/{page}', 'WebsitePagesController@uploadMedia');
    $router->resource('websites.layouts', WebsiteLayoutsController::class);
    $router->resource('websites.redirects', WebsiteRedirectsController::class);
    // $router->resource('websites.sections', WebsiteSectionsController::class);
    $router->get('websites/{website}/sections', 'WebsitesController@sections');
    $router->get('websites/{website}/containers', 'WebsitesController@containers');
    $router->get('websites/{website}/page-links', 'WebsitesController@pageLinks');
    $router->get('websites/{website}/external-links', 'WebsitesController@externalLinks');
    // @TODO: websites/{website}/containers
    $router->get('websites/{website}/pages/{page}/containers', 'WebsitePagesController@containers');
    // @TODO: websites/{website}/sections
    $router->get('websites/{website}/pages/{page}/sections', 'WebsitePagesController@sections');
    // @TODO: websites/{website}/page-links
    $router->get('websites/{website}/pages/{page}/page-links', 'WebsitePagesController@pageLinks');
    // @TODO: websites/{website}/extrnal-links
    $router->get('websites/{website}/pages/{page}/external-links', 'WebsitePagesController@externalLinks');

    $router->resource('resources', ResourcesController::class);
    $router->resource('forms', FormsController::class);
    $router->resource('disks', DisksController::class);
});

// Public Front-end website endpoints
Route::group([
    'namespace' => 'P3in\Controllers',
    'middleware' => ['web'],
], function ($router) {
    $router->group([
        'prefix' => 'web-forms',
    ], function ($router) {
        $router->get('token', 'PublicWebsiteController@getToken');
        $router->get('{path}', 'PublicWebsiteController@getForm')->where('path', '(.*)');
        $router->post('{path?}', 'PublicWebsiteController@submitForm')->where('path', '(.*)');
    });

    $router->group([
        'prefix' => 'render',
    ], function ($router) {
        $router->get('sitemap.{type}', 'PublicWebsiteController@renderSitemap')->where('type', '(xml|html|txt|ror-rss|ror-rdf)');
        $router->get('robots.txt', 'PublicWebsiteController@renderRobotsTxt');
    });

    $router->group([
        'prefix' => 'content',
    ], function ($router) {
        $router->get('menus', 'PublicWebsiteController@getSiteMenus');
        $router->get('site-meta', 'PublicWebsiteController@getSiteMeta');
        $router->get('{path?}', 'PublicWebsiteController@getPageData')->where('path', '(.*)');
    });
});
