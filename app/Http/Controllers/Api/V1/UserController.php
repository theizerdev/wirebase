<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\UserCreateDTO;
use App\Application\DTOs\UserUpdateDTO;
use App\Application\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\Api\V1\UserCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return UserCollection
     */
    public function index(Request $request): UserCollection
    {
        $this->authorize('viewAny', User::class);

        $perPage = (int) $request->get('per_page', 15);
        $filters = $this->buildFilters($request);

        $users = $this->userService->paginate($perPage, $filters);

        Log::info('Users listed', [
            'user_id' => auth()->id(),
            'per_page' => $perPage,
            'filters' => $filters,
            'total' => $users->total(),
        ]);

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        try {
            $userCreateDTO = UserCreateDTO::fromArray($request->validated());

            // Apply multitenancy constraints
            if (!auth()->user()->hasRole('Super Administrador')) {
                $userCreateDTO->empresaId = auth()->user()->empresa_id;
                $userCreateDTO->sucursalId = auth()->user()->sucursal_id;
            }

            $user = $this->userService->create($userCreateDTO);

            Log::info('User created via API', [
                'user_id' => auth()->id(),
                'created_user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            Log::warning('User not found', [
                'user_id' => auth()->id(),
                'requested_user_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('view', $user);

        Log::info('User viewed', [
            'user_id' => auth()->id(),
            'viewed_user_id' => $id,
        ]);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $user);

        try {
            $userUpdateDTO = UserUpdateDTO::fromArray($request->validated());

            // Apply multitenancy constraints
            if (!auth()->user()->hasRole('Super Administrador')) {
                // Prevent changing empresa/sucursal for non-super admins
                $userUpdateDTO->empresaId = null;
                $userUpdateDTO->sucursalId = null;
            }

            $updatedUser = $this->userService->update($id, $userUpdateDTO);

            Log::info('User updated via API', [
                'user_id' => auth()->id(),
                'updated_user_id' => $id,
                'changes' => $userUpdateDTO->getChangedFields(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($updatedUser),
            ]);

        } catch (\Exception $e) {
            Log::error('User update failed', [
                'user_id' => auth()->id(),
                'updated_user_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $user);

        try {
            $this->userService->delete($id);

            Log::info('User deleted via API', [
                'user_id' => auth()->id(),
                'deleted_user_id' => $id,
                'deleted_user_email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('User deletion failed', [
                'user_id' => auth()->id(),
                'deleted_user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search users by query.
     *
     * @param Request $request
     * @return UserCollection
     */
    public function search(Request $request): UserCollection
    {
        $this->authorize('viewAny', User::class);

        $query = $request->get('q', '');
        $perPage = (int) $request->get('per_page', 15);

        if (strlen($query) < 2) {
            return new UserCollection(collect());
        }

        $users = $this->userService->search($query, $perPage);

        Log::info('Users searched', [
            'user_id' => auth()->id(),
            'search_query' => $query,
            'results_count' => $users->total(),
        ]);

        return new UserCollection($users);
    }

    /**
     * Get users by empresa.
     *
     * @param int $empresaId
     * @return JsonResponse
     */
    public function byEmpresa(int $empresaId): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        // Apply multitenancy constraints
        if (!auth()->user()->hasRole('Super Administrador')) {
            if (auth()->user()->empresa_id !== $empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view users from this empresa',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $users = $this->userService->findByEmpresaId($empresaId);

        Log::info('Users listed by empresa', [
            'user_id' => auth()->id(),
            'empresa_id' => $empresaId,
            'users_count' => $users->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Get users by sucursal.
     *
     * @param int $sucursalId
     * @return JsonResponse
     */
    public function bySucursal(int $sucursalId): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        // Apply multitenancy constraints
        if (!auth()->user()->hasRole('Super Administrador')) {
            if (auth()->user()->sucursal_id !== $sucursalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view users from this sucursal',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $users = $this->userService->findBySucursalId($sucursalId);

        Log::info('Users listed by sucursal', [
            'user_id' => auth()->id(),
            'sucursal_id' => $sucursalId,
            'users_count' => $users->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Bulk delete users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $this->authorize('delete', User::class);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $userIds = $request->get('user_ids');
        $deletedCount = 0;
        $errors = [];

        foreach ($userIds as $userId) {
            try {
                $user = $this->userService->findById($userId);

                if (!$user) {
                    $errors[] = "User {$userId} not found";
                    continue;
                }

                $this->authorize('delete', $user);

                $this->userService->delete($userId);
                $deletedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to delete user {$userId}: " . $e->getMessage();
            }
        }

        Log::info('Users bulk deleted', [
            'user_id' => auth()->id(),
            'deleted_count' => $deletedCount,
            'requested_count' => count($userIds),
            'errors' => $errors,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} of " . count($userIds) . ' users',
            'deleted_count' => $deletedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Export users to CSV.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', User::class);

        $format = $request->get('format', 'csv');
        $filters = $this->buildFilters($request);

        // TODO: Implement export logic using a job queue
        // For now, return a placeholder response

        Log::info('Users export requested', [
            'user_id' => auth()->id(),
            'format' => $format,
            'filters' => $filters,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Export job queued successfully',
            'job_id' => 'placeholder',
        ]);
    }

    /**
     * Build filters from request.
     *
     * @param Request $request
     * @return array
     */
    private function buildFilters(Request $request): array
    {
        $filters = [];

        if ($request->has('empresa_id')) {
            $filters['empresa_id'] = (int) $request->get('empresa_id');
        }

        if ($request->has('sucursal_id')) {
            $filters['sucursal_id'] = (int) $request->get('sucursal_id');
        }

        if ($request->has('status')) {
            $filters['status'] = $request->get('status');
        }

        if ($request->has('role')) {
            $filters['role'] = $request->get('role');
        }

        // Apply multitenancy constraints
        if (!auth()->user()->hasRole('Super Administrador')) {
            $filters['empresa_id'] = auth()->user()->empresa_id;

            if (auth()->user()->sucursal_id) {
                $filters['sucursal_id'] = auth()->user()->sucursal_id;
            }
        }

        return $filters;
    }
}
