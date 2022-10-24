<?php

namespace App\Http\Controllers;

use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Models\Branch;
use App\Src\Response;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function CreateBranch(CreateBranchRequest $request)
    {
        $branch = Branch::create([
            'name' => $request->name,
        ]);
        return Response::success();
    }

    public function UpdateBranch(UpdateBranchRequest $request, Branch $branch)
    {
        $branch->update([
            'name' => $request->name,
        ]);
        return Response::success();
    }

    public function ShowAllBranches(Request $request)
    {
        $branches = Branch::all(['id', 'name']);
        return Response::success(data: $branches->toArray());
    }
}
