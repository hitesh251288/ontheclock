<?php
// Write your code in PHP 8.2.5, Laravel 10.2.2

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


/**
 * Class FormController
 * @package App\Http\Controllers
 */
class FormController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('pages.form.show');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'header_line' => 'required|string|max:1024',
            'stars' => 'nullable|numeric|between:1,5',
        ];

        $messages = [
            'title.required' => 'Title is required.',
            'title.max' => 'Title not exceed 255 characters.',
            'header_line.required' => 'Header line is required.',
            'header_line.max' => 'Header line not exceed 1024 characters.',
            'stars.numeric' => 'Stars should be a number.',
            'stars.between' => 'Stars should be between 1 and 5.',
        ];

        $this->validate($request, $rules, $messages);
    }
}

// Second Test

if (!class_exists('DataService')) {
class DataService {
    public function getItems() {
        $users = [
            ["id" => 1, "name" => "John", "role" => "admin"],
            ["id" => 2, "name" => "Juan", "role" => "developer"]
        ];

        return $users;
    }
}
}
$app = AppFactory::create();

$app->get('/users', function (Request $request, Response $response) {
    $dataService = new DataService();

    $queryParams = $request->getQueryParams();
    $name = isset($queryParams['name']) ? $queryParams['name'] : null;
    $users = $dataService->getItems();


    if ($name) {
        $users = array_filter($users, function ($user) use ($name) {
            return $user['name'] === $name;
        });
    }

    $response->getBody()->write(json_encode($users));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

return $app;