<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Task;
use App\Traits\FixitTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    //
    use FixitTrait;

    public function sendContract(Request $request, $taskId)
    {
        // Validate the request data for the contract
        $request->validate([
            'payment_date' => 'required|date',  // payment_date is required for the contract
            'price' => 'required|numeric',  // price is required for the contract
            'end_date' => 'required|date',  // end_date is required for the contract
        ]);

        // Find the task by ID
        $task = Task::find($taskId);

        // Check if the task exists
        if (!$task) {
            return $this->ErrorResponse('Task not found',404);
        }

        // Ensure that the task has been accepted (status = true)
        if (!$task->status) {
            return $this->ErrorResponse('The task must be accepted before sending a contract',400);
        }

        // Create the contract
        $contract = Contract::create([
            'task_id' => $task->id,
            'payment_date' => $request->payment_date,
            'price' => $request->price,
            'end_date' => $request->end_date,
            'completation_status' => false,  // Initially, the task is not completed
        ]);

        // Optionally: Here you can send a notification to the home owner or contractor
        // Notify the home owner that the contract has been sent

        // Return the contract details in the response
        return $this->SuccessResponse($contract,'Contract sent successfully',201);
    }

}
