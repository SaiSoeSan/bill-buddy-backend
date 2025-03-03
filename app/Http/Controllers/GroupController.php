<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupController extends Controller
{
    // Create a new group
    public function store(Request $request)
    {
        $group = Group::create($request->all());
        return response()->json($group, 201);
    }

    // Get all groups
    public function index()
    {
        $groups = Group::all();
        return response()->json($groups, 200);
    }

    // Get a single group by ID
    public function show($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }
        return response()->json($group, 200);
    }

    // Update a group by ID
    public function update(Request $request, $id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }
        $group->update($request->all());
        return response()->json($group, 200);
    }

    // Delete a group by ID
    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }
        $group->delete();
        return response()->json(['message' => 'Group deleted successfully'], 200);
    }

    // Join a group
    public function join(Request $request, Group $group)
    {
        try {
            $user = $request->user();

            if ($group->users()->where('user_id', $user->id)->exists()) {
                return response()->json(['message' => 'User already in the group'], 400);
            }

            $group->users()->attach($user->id);

            return response()->json(['message' => 'User joined the group successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
