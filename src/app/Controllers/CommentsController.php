<?php

namespace App\Controllers;

use \App\Models\CommentsModel;

class CommentsController extends BaseController
{
    public function index()
    {
        return view('comments');
    }
    public function getComments($page = 1, $sortBy = 'id', $sortOrder = 'asc')
    {
        $commentsModel = new CommentsModel();

        $comments = $commentsModel->orderBy($sortBy, $sortOrder)->paginate(3, 'default', $page);
        
        if (empty($comments)) {
            return $this->response->setJSON(['error' => 'No comments'])->setStatusCode(400);
        }

        return $this->response->setJSON(['status' => 'success', 'comments' => $comments]);
    }

    public function create()
    {
        $commentsModel = new CommentsModel();

        $postData = $this->request->getPost();

        if (empty($postData)) {
            return $this->response->setJSON(['error' => 'No data received'])->setStatusCode(400);
        }

        $postData['date'] = date('Y-m-d');

        // Ошибки из model заполняются только с методами insert, update, save
        if (!$commentsModel->insert($postData)) {
            return $this->response->setJSON(['validate_errors' => $commentsModel->errors()])->setStatusCode(400);
        }

        $postData['id'] = $commentsModel->getInsertID();

        return $this->response->setJSON(['status' => 'success', 'data' => $postData]);
    }

    public function delete($id)
    {
        $commentsModel = new CommentsModel();

        if (empty($commentsModel->find($id))) {
            return $this->response->setJSON(['error' => 'No such id'])->setStatusCode(400);
        }

        $commentsModel->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }
}
