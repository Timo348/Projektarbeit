<?php
// Database connection
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../database/projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

session_start();

if (!isset($_SESSION['sesid'])) {
    echo json_encode(['success' => false, 'message' => 'No active session']);
    exit;
}

$userid = $_SESSION['sesid'];

// Handle different AJAX actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            // Add new todo
            if (isset($_POST['todo_titel']) && isset($_POST['todo_inhalt'])) {
                try {
                    $todoTitel = trim($_POST['todo_titel']);
                    $todoInhalt = trim($_POST['todo_inhalt']);
                    $zeit = date('Y-m-d H:i:s');
                    $status = 1;
                    
                    $statement = $db->prepare("INSERT INTO todo 
                    (userid, todo_titel, todo_inhalt, todo_erstellt, todo_status) 
                    VALUES (:userid, :titel, :inhalt, :erstellt, :status)");
                    $statement->bindParam(':userid', $userid);
                    $statement->bindParam(':titel', $todoTitel);
                    $statement->bindParam(':inhalt', $todoInhalt);
                    $statement->bindParam(':erstellt', $zeit);
                    $statement->bindParam(':status', $status);
                    $statement->execute();
                    
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing title or content']);
            }
            break;
            
        case 'update_status':
            // Update todo status
            if (isset($_POST['todoid']) && isset($_POST['status'])) {
                try {
                    $todoid = $_POST['todoid'];
                    $status = $_POST['status'];
                    
                    $statement = $db->prepare("UPDATE todo SET todo_status = :status WHERE userid = :userid AND todoid = :todoid");
                    $statement->bindParam(':userid', $userid);
                    $statement->bindParam(':todoid', $todoid);
                    $statement->bindParam(':status', $status);
                    $statement->execute();
                    
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing todo ID or status']);
            }
            break;
            
        case 'delete':
            // Delete todo (set status to 4)
            if (isset($_POST['todoid'])) {
                try {
                    $todoid = $_POST['todoid'];
                    
                    $statement = $db->prepare("UPDATE todo SET todo_status = 4 WHERE userid = :userid AND todoid = :todoid");
                    $statement->bindParam(':userid', $userid);
                    $statement->bindParam(':todoid', $todoid);
                    $statement->execute();
                    
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing todo ID']);
            }
            break;
            
        case 'get_column':
            // Get todos for a specific column (status)
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = :status ORDER BY todo_erstellt DESC" . ($status == 4 ? " LIMIT 10" : ""));
                $stmt->bindParam(':userid', $userid);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
                
                ob_start();
                while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="todo-item" data-id="' . $todo['todoid'] . '">';
                    echo '<div class="todo-title">' . htmlspecialchars($todo['todo_titel']) . '</div>';
                    echo '<div class="todo-content">' . htmlspecialchars($todo['todo_inhalt']) . '</div>';
                    
                    $statusText = '';
                    switch ($status) {
                        case 1: $statusText = 'Erstellt'; break;
                        case 2: $statusText = 'Bearbeitet'; break;
                        case 3: $statusText = 'Erledigt'; break;
                        case 4: $statusText = 'Gelöscht'; break;
                    }
                    echo '<div class="todo-time">' . $statusText . ': ' . $todo['todo_erstellt'] . '</div>';
                    
                    echo '<div class="todo-buttons">';
                    
                    if ($status != 4) {
                        // Show different buttons based on status
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                        
                        if ($status == 1) {
                            echo '<input type="hidden" name="status" value="2">';
                            echo '<button type="submit" name="todo_status" class="edit-btn">→</button>';
                        } else if ($status == 2) {
                            echo '<input type="hidden" name="status" value="3">';
                            echo '<button type="submit" name="todo_status" class="edit-btn">→</button>';
                        } else if ($status == 3) {
                            echo '<input type="hidden" name="status" value="1">';
                            echo '<button type="submit" name="todo_status" class="edit-btn">↺</button>';
                        }
                        
                        echo '</form>';
                        
                        if ($status != 3) {
                            echo '<button class="edit-btn">Bearbeiten</button>';
                        }
                        
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                        echo '<button type="submit" name="todo_loeschen" class="delete-btn">Löschen</button>';
                        echo '</form>';
                    } else {
                        // Trash column - show restore button
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                        echo '<input type="hidden" name="status" value="1">';
                        echo '<button type="submit" name="todo_status" class="edit-btn">Wiederherstellen</button>';
                        echo '</form>';
                        
                        // Add button for permanently deleting a todo
                        echo '<form method="POST" style="display: inline;">';
                        echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                        echo '<button type="submit" name="todo_permanent_delete" class="delete-btn">Permanent Löschen</button>';
                        echo '</form>';
                    }
                    
                    echo '</div>'; // end buttons
                    echo '</div>'; // end todo item
                }
                $html = ob_get_clean();
                echo $html;
                exit;
            }
            break;
            
        case 'permanent_delete':
            // Permanently delete todo
            if (isset($_POST['todoid'])) {
                try {
                    $todoid = $_POST['todoid'];
                    
                    $statement = $db->prepare("UPDATE todo SET todo_status = 5 WHERE userid = :userid AND todoid = :todoid");
                    $statement->bindParam(':userid', $userid);
                    $statement->bindParam(':todoid', $todoid);
                    $statement->execute();
                    
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing todo ID']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
}
?>