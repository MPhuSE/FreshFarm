<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q')->toString();
                $query->where(function ($userQuery) use ($search): void {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate((int) $request->input('per_page', 20));

        $data = $users->getCollection()->map(fn (User $user): array => [
            'id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'role' => $user->role,
            'status' => $user->status,
            'created_at' => $user->created_at,
        ])->values();

        return response()->json([
            'success' => true,
            'message' => 'Danh sách người dùng thành công.',
            'data' => $data,
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'errors' => null,
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,locked'],
        ]);
        $user = User::findOrFail($id);

        if ($request->user()?->is($user)) {
            return $this->error('Không thể khóa tài khoản của chính mình.', 'CANNOT_LOCK_SELF', 422);
        }

        $user->update($validated);

        return $this->success(null, 'Cập nhật trạng thái người dùng thành công.');
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:customer,staff,admin'],
        ]);
        $user = User::findOrFail($id);

        if ($request->user()?->is($user)) {
            return $this->error('Không thể thay đổi vai trò của chính mình.', 'CANNOT_CHANGE_SELF_ROLE', 422);
        }

        $user->update($validated);

        return $this->success(null, 'Cập nhật vai trò người dùng thành công.');
    }
}
