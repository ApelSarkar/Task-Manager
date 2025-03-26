<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../models/Task.php';
$task = new Task();
$tasks = $task->getTasks($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Task Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h3>Task List</h3>
                <table class="table table-bordered table-striped" id="task-table">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['title']); ?></td>
                                <td><?= htmlspecialchars($task['description']); ?></td>
                                <td><?= ($task['due_date']); ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($task['status']); ?></span></td>
                                <td>
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <button class="btn btn-success btn-sm complete-btn" data-task-id="<?= $task['id']; ?>">Complete</button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-task-id="<?= $task['id']; ?>">Delete</button>
                                    <?php else: ?>
                                        <button id="postToFacebookBtn" class="btn btn-primary btn-sm post-btn" data-task-id="<?= $task['id']; ?>">Post</button>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h3>Add Task</h3>
                <form id="addTaskForm">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="taskTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="taskDescription" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="taskDueDate" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Task</button>
                </form>
                <div id="message"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    //for task status change to completed
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('complete-btn')) {
                let taskId = event.target.getAttribute('data-task-id');
                let buttonElement = event.target;

                fetch('../controllers/TaskController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=complete&task_id=' + taskId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let parentTd = buttonElement.parentNode;
                            parentTd.innerHTML = '<span class="badge bg-success">Completed</span>';

                            let postButton = document.createElement("button");
                            postButton.className = "btn btn-primary btn-sm";
                            postButton.innerText = "Post";

                            parentTd.appendChild(postButton);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });


    // add task 
    document.getElementById("addTaskForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append("action", "add");
        formData.append("user_id", document.querySelector("input[name='user_id']").value);
        formData.append("title", document.getElementById("taskTitle").value);
        formData.append("description", document.getElementById("taskDescription").value);
        formData.append("due_date", document.getElementById("taskDueDate").value);

        fetch('../controllers/TaskController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById("message");
                if (data.success) {
                    console.log(data);
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    let taskStatusHtml = '';
                    if (data.task.status !== 'completed') {
                        taskStatusHtml = `
                                            <button class="btn btn-success btn-sm complete-btn" data-task-id="${data.task.id}">Complete</button>
                                            <button class="btn btn-danger btn-sm delete-btn" data-task-id="${data.task.id}">Delete</button>
                                        `;
                    } else {
                        taskStatusHtml = `
                                          
                                            <button class="btn btn-primary btn-sm post-btn" data-task-id="<?= $task['id']; ?>">Post</button>
                                        `;
                    }

                    var newTaskRow = `
                                        <tr>
                                            <td>${data.task.title}</td>
                                            <td>${data.task.description}</td>
                                            <td>${data.task.due_date}</td>
                                            <td>${data.task.status}</td>
                                            <td>${taskStatusHtml}</td>
                                        </tr>
                                    `;

                    $('#task-table tbody').prepend(newTaskRow);
                    document.getElementById("task-form").reset();
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
    });


    // for delete operation
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-btn')) {
                let taskId = event.target.getAttribute('data-task-id');
                let rowElement = event.target.closest('tr');

                if (confirm("Are you sure you want to delete this task?")) {
                    fetch('../controllers/TaskController.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'action=delete&task_id=' + taskId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                rowElement.remove();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }
        });
    });

    // publish post into facebook using graph API
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.post-btn').forEach(button => {
            button.addEventListener('click', function() {
                let taskId = this.getAttribute('data-task-id');

                if (confirm("Are you sure you want to post into facebook")) {
                    fetch('../controllers/TaskController.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=postToFacebook&task_id=' + taskId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Post published successfully: ' + data.post_id);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    });
</script>