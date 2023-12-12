document.addEventListener("DOMContentLoaded", function () {
  var form = document.getElementById("todo-form") as HTMLFormElement;
  var todoList = document.getElementById("todo-list") as HTMLDivElement; // Add this ID to your todo list container in HTML

  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    var xhr = new XMLHttpRequest();
    const todoInput = document.getElementById("new_todo") as HTMLInputElement;
    var todoText = todoInput.value;

    xhr.open("POST", "index.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest"); // Set the AJAX header

    xhr.onload = function () {
      // Process our return data
      if (xhr.status >= 200 && xhr.status < 300) {
        // This will run when the request is successful
        console.log("success!", xhr.responseText);
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
          // Append the new todo item to the list

          var newTodoItem = document.createElement("div");
          newTodoItem.className = "todo-item";
          newTodoItem.innerHTML = `
                        <span>${todoText}</span>
                        <a href="?toggle=0">[Check]</a>
                        <a href="?remove=0">[Remove]</a>
                    `;

          // Append the new todo item to the list
          todoList.appendChild(newTodoItem);

          // Clear the input field
          todoInput.value = "";
        }
      } else {
        // This will run when it's not successful
        console.log("The request failed!");
      }
    };

    xhr.send("new_todo=" + encodeURIComponent(todoText));
    // Clear the input field after sending
    todoInput.value = "";
  });
});
