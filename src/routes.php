<?php
// Routes

/*$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/


// API group
$app->group('/api', function () use ($app) {
    // Version group
    $app->group('/v1', function () use ($app) {
        $app->get('/employees', 'getEmployes');
        $app->get('/employee/{id}', 'getEmployee');
        $app->post('/create', 'addEmployee');
        $app->put('/update/{id}', 'updateEmployee');
        $app->delete('/delete/{id}', 'deleteEmployee');
    });
});