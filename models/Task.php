<?php
require_once '../models/Database.php';
class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createTask($userId, $title, $description, $due_date)
    {
        $stmt = $this->db->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)");
        return $stmt->execute(['user_id' => $userId, 'title' => $title, 'description' => $description, 'due_date' => $due_date]);
    }

    public function getTasks($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date DESC");
        $stmt->execute(['user_id' => $userId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $tasks;
    }

    public function addTask($user_id, $title, $description, $due_date)
    {
        $date = DateTime::createFromFormat('Y-m-d', $due_date);

        $formattedDate = $date->format('Y-m-d');
        $stmt = $this->db->prepare("INSERT INTO tasks (user_id, title, due_date, description) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$user_id, $title, $formattedDate, $description]);

        if ($result) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function markTaskAsCompleted($task_id)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
        return $stmt->execute([$task_id]);
    }

    public function deleteTask($taskId)
    {
        $sql = "DELETE FROM tasks WHERE id = :task_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTasksById($taskId)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :task_id LIMIT 1");
        $stmt->execute(['task_id' => (int) $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}
