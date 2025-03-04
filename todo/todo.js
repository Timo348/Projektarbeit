document.addEventListener('DOMContentLoaded', function() {
    // Function to update todo status
    function updateTodoStatus(todoId, newStatus) {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('todoid', todoId);
        formData.append('status', newStatus);
        
        fetch('todo_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the todo item from its current column
                const todoItem = document.querySelector(`.todo-item[data-id="${todoId}"]`);
                if (todoItem) {
                    todoItem.remove();
                }
                
                // Refresh the target column
                refreshColumn(newStatus);
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Function to delete a todo
    function deleteTodo(todoId) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('todoid', todoId);
        
        fetch('todo_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the todo item and refresh the trash column
                const todoItem = document.querySelector(`.todo-item[data-id="${todoId}"]`);
                if (todoItem) {
                    todoItem.remove();
                }
                refreshColumn(4); // Refresh trash column
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Function to add a new todo
    document.querySelector('.todo-form form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add');
        
        fetch('todo_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the form
                this.reset();
                
                // Refresh the first column (new todos)
                refreshColumn(1);
            } else {
                alert('Fehler: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Function to refresh a specific column
    function refreshColumn(columnStatus) {
        const formData = new FormData();
        formData.append('action', 'get_column');
        formData.append('status', columnStatus);
        
        fetch('todo_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Find the column to update based on status
            let columnSelector;
            switch (parseInt(columnStatus)) {
                case 1: 
                    columnSelector = '.todo-column:nth-child(1)';
                    break;
                case 2: 
                    columnSelector = '.todo-column:nth-child(2)';
                    break;
                case 3: 
                    columnSelector = '.todo-column:nth-child(3)';
                    break;
                case 4: 
                    columnSelector = '.todo-column:nth-child(4)';
                    break;
            }
            
            if (columnSelector) {
                const column = document.querySelector(columnSelector);
                if (column) {
                    // Keep the header, replace the rest
                    const header = column.querySelector('.column-header').outerHTML;
                    column.innerHTML = header + html;
                    
                    // Reattach event listeners to new elements
                    attachEventListeners();
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Attach event listeners to buttons
    function attachEventListeners() {
        // Status update buttons
        document.querySelectorAll('[name="todo_status"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const todoId = form.querySelector('[name="todoid"]').value;
                const status = form.querySelector('[name="status"]').value;
                updateTodoStatus(todoId, status);
            });
        });
        
        // Delete buttons
        document.querySelectorAll('[name="todo_loeschen"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const todoId = form.querySelector('[name="todoid"]').value;
                deleteTodo(todoId);
            });
        });
    }
    
    // Initial attachment of event listeners
    attachEventListeners();
});