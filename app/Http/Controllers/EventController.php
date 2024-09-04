<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Eventos",
 *     description="Documentação da API de Eventos",
 *     @OA\Contact(
 *         email="dwerlich21@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/events",
     *     tags={"Events"},
     *     summary="Obter lista de eventos",
     *     description="Retorna todos os eventos com os usuários associados.",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de eventos retornada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function index(): Collection|array
    {
        return Event::with('users')->get();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/events/{id}",
     *     tags={"Events"},
     *     summary="Obter um evento específico",
     *     description="Retorna um evento pelo ID com os usuários associados.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do evento",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento retornado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function show($id): Model|Collection|Builder|array|null
    {
        return Event::with('users')->findOrFail($id);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/events",
     *     tags={"Events"},
     *     summary="Criar um novo evento",
     *     description="Cria um novo evento e, opcionalmente, associa usuários ao evento.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "date"},
     *             @OA\Property(property="name", type="string", example="Evento de Exemplo"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-09-01"),
     *             @OA\Property(property="user_ids", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Evento criado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação do evento"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'date' => 'required|date',
                'user_ids' => 'array|exists:users,id'
            ]);

            $event = Event::create($validated);

            if ($request->has('user_ids')) {
                $event->users()->attach($request->input('user_ids'));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Evento criado com sucesso!'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/events/{id}",
     *     tags={"Events"},
     *     summary="Atualizar um evento",
     *     description="Atualiza um evento existente pelo ID e, opcionalmente, sincroniza os usuários associados.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do evento",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "date"},
     *             @OA\Property(property="name", type="string", example="Evento Atualizado"),
     *             @OA\Property(property="date", type="string", format="date-time", example="2024-10-01T14:30:00Z"),
     *             @OA\Property(property="user_ids", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na atualização do evento"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $event = Event::findOrFail($id);
            $event->update($request->all());

            if ($request->has('user_ids')) {
                $event->users()->sync($request->input('user_ids'));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Evento alterado com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/events/{id}",
     *     tags={"Events"},
     *     summary="Excluir um evento",
     *     description="Exclui um evento existente pelo ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do evento",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evento excluído com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Evento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao excluir o evento"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Evento excluído com sucesso!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
