<?php

namespace App\Repositories;

use App\Models\Comment;
use JosueIsOffline\Framework\Database\DB;

class CommentRepository
{
  public function getAll(int $id): ?array
  {
    $sql = "
        SELECT c.*, u.name AS user_name
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.incident_id = :incident_id
        ORDER BY c.created_at ASC
    ";

    $stmt = DB::raw($sql, ['incident_id' => $id]);
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: null;

    return $result;
  }


  public function create(array $data): void
  {
    $comment = new Comment();

    $comment->create($data);
  }
}
