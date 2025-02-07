import React, { useEffect, useState } from "react";
import axios from "axios";

const TodoList = () => {
    const [tasks, setTasks] = useState([]);
    const [newTask, setNewTask] = useState("");

    // Fetch tasks
    useEffect(() => {
        axios.get("http://localhost/todo-app/api.php")
            .then(response => setTasks(response.data))
            .catch(error => console.error("Error fetching tasks", error));
    }, []);

    // Add task
    const addTask = () => {
        if (newTask.trim()) {
            axios.post("http://localhost/todo-app/api.php", { title: newTask })
                .then(response => {
                    const addedTask = { id: response.data.id, title: newTask, completed: false };
                    setTasks(prevTasks => [addedTask, ...prevTasks]); // Add new task to the state
                    setNewTask(""); // Clear input field
                })
                .catch(error => console.error("Error adding task", error));
        }
    };

    // Delete task
    const deleteTask = (id) => {
        axios.delete("http://localhost/todo-app/api.php", {
            data: { id }  // Send ID in the request body
        })
            .then(() => {
                // Remove the deleted task from the state
                setTasks(prevTasks => prevTasks.filter(task => task.id !== id));
            })
            .catch(error => console.error("Error deleting task", error));
    };

    // Toggle task as completed
    const toggleTask = (id, completed) => {
        axios.put("http://localhost/todo-app/api.php", { id, completed: !completed })
            .then(() => {
                // Update the task's completed status in the state
                setTasks(prevTasks => prevTasks.map(task =>
                    task.id === id ? { ...task, completed: !completed } : task
                ));
            })
            .catch(error => console.error("Error updating task", error));
    };

    return (
        <div className="todo-container">
            <h1>To-Do List</h1>
            <div className="input-container">
                <input
                    type="text"
                    placeholder="Write something here..."
                    value={newTask}
                    onChange={(e) => setNewTask(e.target.value)}
                />
                <button onClick={addTask}>Add Task</button>
            </div>
            <ul>
                {tasks.map(task => (
                    <li key={task.id}>
                        <span style={{ textDecoration: task.completed ? "line-through" : "none" }}>
                            {task.title}
                        </span>
                        <button onClick={() => toggleTask(task.id, task.completed)}>✔</button>
                        <button onClick={() => deleteTask(task.id)}>❌</button>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default TodoList;
