<?php

class Todo {
    public $todo;
    public $completed;
    public $created;
}

/*
*   CLASS TodoListAPI
*   Create and manage a todo list with PHP.
*/
class TodoListAPI {
    public function __construct() { 
      $this->method = $_SERVER['REQUEST_METHOD'];
      $todo = new Todo();
      $todo->todo = 'Create a todo list api server in PHP.';
      $todo->completed = null;
      $todo->created = time() * 1000;
      $this->todoList = [ $todo ];
    }
    
    public function init() {         
        if('GET' === $this->method) {
          $this->getRoutes();
        } else if('POST' === $this->method) {
          $this->postRoutes();
        } else if ('PUT' === $this->method) {
          $this->putRoutes();
        } else if ('DELETE' === $this->method) {
          $this->deleteRoutes();
        } else {
          $this->notFound();
            /* Since we are only dealing with
                POST PUT DELETE AND GET
                we will use the ELSE to catch everything else, and return a not found page.
            */
        }   
    }
  
    /*
    *   return Page not Found.
    */
    public function notFound(){
        echo('<h1>The page or file was <strong>not found</strong>.</h1>');
    }

    /*
    * getRoutes This function handles all GET route requests.
    */    
    public function getRoutes() { 
        /*
        * Our application has only a few routes the GET Routes include the default page, and the todo-list.
        * We can grab the query string and see that it matches our required path.
        */
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        switch($path) {
          case '/todo-list':
            header('Content-Type: application/json');
            echo json_encode(['todoList' => $this->todoList]); /* We just need to reference our class instance.*/
            break;
          default:
            echo('<h1>Todo List API</h1>');
        }
    }
    

    /*
    *   deleteRoutes Handle all DELETE route requests
    */
    public function deleteRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathArray = explode('/', $path);
        array_shift($pathArray);
        switch($pathArray[0]) {
            case 'todo':
            default:
                $this->todoIndex = (int)$pathArray[1]; 
                if( isset($this->todoIndex) && is_numeric($this->todoIndex) ) {
                        $this->todoList = array_filter($this->todoList, function($k){ return($k !== $this->todoIndex); }, ARRAY_FILTER_USE_KEY );
                        echo json_encode($this->todoList); // We just need to reference our class instance.
                } else {
                    echo json_encode($this->todoList);
                }
            
        }
    }

    /*
     *  postRoutes Handle all of the POST route requests  
     */
    public function postRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathArray = explode('/', $path);
        array_shift($pathArray);
        switch($pathArray[0]) {
            case 'todo':
            default:
                $post = file_get_contents('php://input');
                $post = json_decode($post);
                $this->todo = $post->todo; 
                array_push($this->todoList, $this->todo);
                header('Content-Type: application/json');
                echo json_encode($this->todoList);
        }
    }

    /*
    *   putRoutes Handle all PUT route requests
    */
    public function putRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathArray = explode('/', $path);
        array_shift($pathArray);
        switch($pathArray[0]) {
            case 'todo':
            default:
                $this->todoIndex = (int)$pathArray[1];
                $this->todoList[$this->todoIndex]->completed =  time() * 1000;
                header('Content-Type: application/json');                
                echo json_encode($this->todoList);
            
        }
    }
}

header('Access-Control-Allow-Origin: http://localhost:3000');
header("Access-Control-Allow-Methods : POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers : Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
            
$todoListServer = new TodoListAPI();
$todoListServer->init();
