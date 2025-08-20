<?php

namespace App\Controllers\Api;

use App\Repositories\CommentRepository;
use JosueIsOffline\Framework\Auth\AuthService;
use JosueIsOffline\Framework\Controllers\AbstractController;
use JosueIsOffline\Framework\Http\Response;

class CommentController extends AbstractController
{
  private CommentRepository $cRepo;
  private AuthService $auth;

  public function __construct()
  {
    $this->cRepo = new CommentRepository();
    $this->auth = new AuthService();
  }

  public function getAllComments(int $id): Response
  {
    $comments = $this->cRepo->getAll($id);

    return $this->success($comments);
  }

  public function createComment(): Response
  {
    $params = $this->request->getAllPost();

    $params['incident_id'] = (int)$params['incident_id'];
    $params['user_id'] = $this->auth->id();

    $this->cRepo->create($params);

    return $this->success([], 'Comentario publicado exitosamente!', 201);
  }
}
