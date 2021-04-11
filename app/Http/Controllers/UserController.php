<?php

namespace App\Http\Controllers;

use App\Http\Requests\Import\ImportTransactionRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Resources\FileImportedResource;
use App\Http\Resources\TransactionFailResource;
use App\Http\Resources\TransactionResource;
use App\Imports\TransactionImport;
use App\Jobs\UpdateStateFileJob;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()
            ->json([
                'token' => $user->createToken('mobile')->plainTextToken
            ]);

    }

    public function getUser(): \Illuminate\Http\JsonResponse
    {
        return response()
            ->json([
                'user' => auth()->user(),
            ]);
    }

    public function importTransaction(ImportTransactionRequest $request): \Illuminate\Http\JsonResponse
    {
        $fileUpload = $request->file('file');
        $user = auth()->user();
        $fileImported = $user
            ->filesImport()
            ->create([
                'name' => $fileUpload->getClientOriginalName(),
                'path' => $fileUpload->store('excel')
            ]);
        try {
            (new TransactionImport($fileImported))
                ->queue($fileImported->path)
                ->chain([
                    new UpdateStateFileJob($fileImported)
                ]);
            return response()->json([
                'message' => 'Upload success'
            ], 201);

        } catch (\Exception $exception) {
            $fileImported->delete();
            return response()->json([
                'message' => 'Cannot upload file'
            ], 400);

        }
    }


    public function getFileImported(): FileImportedResource
    {
        $state = request()->query('state');
        $fileImported = auth()->user()
            ->filesImport()
            ->when($state != '', function (Builder $query) use ($state) {
                // Filter file import complete
                if ($state == 1) {
                    $query->where('state', 1);
                }
                // Filter file import has record fail
                if ($state == 2) {
                    $query->where('state', 2);
                }
                // Filter file importing
                if ($state == 3) {
                    $query->where('state', 0);
                }
            })
            ->paginate(50);

        return new FileImportedResource($fileImported);

    }

    public function getTransactionFails($file_id): TransactionFailResource
    {
        $file = auth()->user()
            ->filesImport()
            ->where('state', 2)
            ->findOrFail($file_id);
        $transactionFails = $file->transactionFailed()
            ->paginate(50);
        return new TransactionFailResource($transactionFails);
    }


    public function getTransaction(): TransactionResource
    {
        $transactions = Transaction::query()
            ->latest()
            ->paginate(100);
        return new TransactionResource($transactions);
    }
}
