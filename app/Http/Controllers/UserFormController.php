<?php

namespace App\Http\Controllers\UserForm;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserForms\StoreUserFormRequest;
use Inertia\Inertia;
use App\Services\UserForm\UserFormService;

class UserFormController extends Controller
{
    public function index(UserFormService $service)
    {
        return Inertia::render(
            "UserForm/UserFormIndex",
            ["forms" => $service->listAllForms()]
        );
    }

    public function show($formKey, UserFormService $service)
    {
        return Inertia::render(
            "UserForm/UserFormShow",
            ["formComponent" => $service->userFormConfig($formKey)]
        );
    }

    public function store(
        StoreUserFormRequest $request,
        UserFormService $service
    ) {
        $validData = $request->validated();
        $service->formSubmission($validData);

        return Inertia::render(
            "UserForm/UserFormIndex",
            ["forms" => $service->listAllForms()]
        );
    }
}
