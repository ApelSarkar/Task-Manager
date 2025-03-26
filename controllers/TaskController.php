<?php
require_once '../models/Task.php';

class TaskController
{
    private $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    public function addTask()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add") {
            $title = trim($_POST["title"]);
            $description = trim($_POST["description"]);
            $user_id = $_POST["user_id"];
            $due_date = $_POST["due_date"];

            if (!empty($title) && !empty($user_id)) {
                $taskId = $this->taskModel->addTask($user_id, $title, $description, $due_date);

                if ($taskId) {
                    $newTask = [
                        'id' => $taskId,
                        'title' => $title,
                        'description' => $description,
                        'due_date' => $due_date,
                        'status' => 'pending'
                    ];
                    echo json_encode(['success' => true, 'message' => 'Task added successfully', 'task' => $newTask]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add task']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Title and User ID are required']);
            }
        }
    }


    public function markTaskAsCompleted()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
            $task_id = $_POST['task_id'];

            if ($this->taskModel->markTaskAsCompleted($task_id)) {
                echo json_encode(['success' => true, 'message' => 'Task marked as completed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to complete task']);
            }
        }
    }

    public function deleteTask()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "delete") {
            $taskId = $_POST["task_id"];

            if (!empty($taskId)) {
                $deleted = $this->taskModel->deleteTask($taskId);

                if ($deleted) {
                    echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            }
        }
    }


    public function postToFacebook($taskId)
    {
        $task = $this->taskModel->getTasksById((int)$taskId);
        $description = $task['description'];
        $accessToken = ''; // Replace with your actual token
        $pageId = ''; // Replace with your Page ID

        $url = "https://graph.facebook.com/v22.0/{$pageId}/feed";
        $data = [
            'message' => $description,
            'access_token' => $accessToken,
        ];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo json_encode([
                'success' => false,
                'message' => 'cURL Error: ' . curl_error($ch),
            ]);
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['id'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Post published successfully!',
                'post_id' => $responseData['id'],
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error posting to Facebook: ' . ($responseData['error']['message'] ?? 'Unknown error'),
            ]);
        }
    }
}

$controller = new TaskController();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $controller->addTask();
    } elseif ($_POST['action'] === 'complete') {
        $controller->markTaskAsCompleted();
    } elseif ($_POST['action'] === 'delete') {
        $controller->deleteTask();
    } elseif ($_POST['action'] == 'postToFacebook') {
        $taskId = $_POST['task_id'];
        $controller->postToFacebook($taskId);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
}
