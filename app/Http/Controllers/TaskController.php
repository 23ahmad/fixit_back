<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Contractor;
use App\Models\Task;
use App\Models\Task_image;
use App\Traits\FixitTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    //
    use FixitTrait;


    public function createTask(Request $request)
    {
        // Check if contractor exists
        $contractor = Contractor::find($request->contractor_id);
        if (!$contractor) {
            return $this->ErrorResponse('Contractor not found',404);
        }
        // Validate the request data
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id', // Ensure contractor exists
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',  // Validate location field
            'country' => 'required|string|max:255',   // Validate country field
            'city' => 'required|string|max:255',      // Validate city field
            'images' => 'nullable|array',  // Validate that images are provided as an array
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate each image file
        ]);

        // Get the authenticated user
        $user = Auth::user();


        // Create a new task with the data from the request
        $task = Task::create([
            'user_id' => $user->id,  // Set user_id from the authenticated user
            'contractor_id' => $request->contractor_id, // Get contractor_id from request
            'title' => $request->title,  // Get title from request
            'description' => $request->description,  // Get description from request
            'location' => $request->location,  // Get location from request
            'country' => $request->country,  // Get country from request
            'city' => $request->city,  // Get city from request
        ]);

        // Check if images are provided
        if ($request->hasFile('images') && count($request->file('images')) > 0) {
            $images = $request->file('images');
            foreach ($images as $image) {
                // Store image and get its path
                $imagePath = $image->store('task_images', 'public');

                // Save the image record in the images table, and associate it with the task
                $imageRecord = \App\Models\Image::create([
                    'name' => $imagePath,
                ]);

                // Associate the image with the task in task_images table
                Task_image::create([
                    'task_id' => $task->id,
                    'image_id' => $imageRecord->id,
                ]);
            }
        }
        // Return a success response
        return $this->SuccessResponse($task,'Task created successfully',201);
    }


    public function acc_rej_task(Request $request, $taskId)
    {
        // Validate the request data
        $request->validate([
            'status' => 'required|boolean',  // Ensure status is provided as a boolean (true for accept, false for reject)
        ]);

        // Get the authenticated user (the contractor)
        $contractor = Auth::user();  // Get the authenticated user

        // Find the task by ID
        $task = Task::find($taskId);

        if (!$task) {
            return $this->ErrorResponse('Task not found',404);
        }

        // Check if the authenticated user is the contractor for this task
        if ($task->contractor_id !== $contractor->contractor->id) {
            return $this->ErrorResponse('Unauthorized',403);  // If the contractor doesn't own the task
        }

        // Update the task status
        $task->status = $request->status; // Accept or reject the task

        // Save the updated task
        $task->save();

        // Return a success response
        return $this->SuccessResponse($task,'Task status updated successfully',200);
    }


    public function getAllContractorTasks(Request $request)
    {
        // Get the authenticated user (contractor)
        $contractor = Auth::user();  // Assuming contractor is the authenticated user
        // Get tasks with the related 'task_image' and 'contract' relationships
        $tasks = Task::with(['task_image.image'])
        ->where('contractor_id', $contractor->contractor->id) // You can filter by contractor_id or any other condition
        ->get();

        // Transform the response to match your desired structure
        $tasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'user_id' => $task->user_id,
                'contractor_id' => $task->contractor_id,
                'title' => $task->title,
                'description' => $task->description,
                'location' => $task->location,
                'country' => $task->country,
                'city' => $task->city,
                'status' => $task->status,
                'has_contract' => $task->contract ? true : false,  // Check if the task has a contract
                'task_image' => $task->task_image->map(function ($taskImage) {
                    return [
                    'id' => $taskImage->id,
                    'name' => $taskImage->image->name,  // Assuming 'image' is the related model with 'name' attribute
                    'created_at' => $taskImage->created_at,
                    'updated_at' => $taskImage->updated_at,
                    ];
                })
            ];
        });

        // Return the tasks in the response
        return $this->SuccessResponse($tasks,'All Task Contractor',200);
    }

}
