// index.ts
document.addEventListener("DOMContentLoaded", function() {
  var form = document.getElementById("todo-form");
  var todoList = document.getElementById("todo-list");
  form.addEventListener("submit", function(event) {
    event.preventDefault();
    var xhr = new XMLHttpRequest;
    const todoInput = document.getElementById("new_todo");
    var todoText = todoInput.value;
    xhr.open("POST", "index.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
        console.log("success!", xhr.responseText);
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
          var newTodoItem = document.createElement("div");
          newTodoItem.className = "todo-item";
          newTodoItem.innerHTML = `
                        <span>${todoText}</span>
                        <a href="?toggle=0">[Check]</a>
                        <a href="?remove=0">[Remove]</a>
                    `;
          todoList.appendChild(newTodoItem);
          todoInput.value = "";
        }
      } else {
        console.log("The request failed!");
      }
    };
    xhr.send("new_todo=" + encodeURIComponent(todoText));
    todoInput.value = "";
  });
});
