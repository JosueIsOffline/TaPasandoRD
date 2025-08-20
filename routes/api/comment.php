<?php

use App\Controllers\Api\CommentController;

return [
  ['GET', '/api/comment/{id:\d+}', [CommentController::class, 'getAllComments']],
  ['POST', '/api/comment', [CommentController::class, 'createComment']]
];
