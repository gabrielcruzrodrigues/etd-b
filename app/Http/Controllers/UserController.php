<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceContract;
use App\Enums\ResponseStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserServiceContract $userService)
    {

    }

    /**
     *  Exibe a lista de usuários paginada com validação de limites para o parâmetro `perPage`.
     *
     * Este método recupera o valor de `per_page` da requisição, aplicando as seguintes regras:
     *  - Se `per_page` for negativo ou igual a zero, o valor padrão de 30 será utilizado.
     *  - O valor de `per_page` é então limitado para estar entre 1 e 100.
     *    - Valores menores que 1 são ajustados para 1.
     *    - Valores maiores que 100 são ajustados para 100.
     *
     *  Essa lógica garante que o parâmetro de paginação esteja dentro de um intervalo seguro e apropriado
     *  para evitar sobrecarga de dados retornados na resposta.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 30);

        $perPage = min(max($perPage, 1), 100);

        $users = $this->userService->getUsers($perPage);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $users,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    /**
     * @param string $email
     * @return JsonResponse
     */
    public function findUserByEmail(Request $request, string $email): JsonResponse
    {
        $user = $this->userService->findUserByEmail($email);


        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $user,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

}
