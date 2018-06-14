<?php
/*
*   CLASS TodoListAPI
*   Create and manage a todo list with PHP.
*/
class TodoListAPI {
    public function __construct() { 
      $this->method = $_SERVER['REQUEST_METHOD'];
      $todo["todo"] = 'Create a todo list api server in PHP.';
      $todo["completed"] = false;
      $todo["created"] = time();
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
            echo json_encode($this->todoList); /* We just need to reference our class instance.*/
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
        switch($path) {
            case '/todo':
            default:
                $delete = file_get_contents('php://input');
                $delete = json_decode($delete);
                $this->todoIndex = $delete->todoIndex; 
                if( isset($delete->todoIndex)
                     && is_numeric($delete->todoIndex) ) {
                        header('Content-Type: application/json');
                        $this->todoList = array_filter($this->todoList, function($k){ return($k !== $this->todoIndex); }, ARRAY_FILTER_USE_KEY );
                        echo json_encode($this->todoList); /* We just need to reference our class instance.*/
                } else {
                    header('Content-Type: application/json');
                    echo json_decode($this->todoList);
                }
        }
    }


    /*
     *  postRoutes Handle all of the POST route requests  
     */
    public function postRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        switch($path) {
            case '/todo':
            default:
                $post = file_get_contents('php://input');
                $post = json_decode($post);
                $this->todo = $_POST->todo; 
                array_push($this->todoList, $this->todo);
                header('Content-Type: application/json');
                echo json_decode($this->todoList);
        }
    }

    /*
    *   putRoutes Handle all PUT route requests
    */
    public function putRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        switch($path) {
            case '/todo':
            default:
                $put = file_get_contents('php://input');
                $put = json_decode($put);
                $this->todoIndex = $put->todoIndex;
                $this->todoList[$this->todoIndex]['completed'] = time();
                header('Content-Type: application/json');
                echo json_encode($this->todoList);
        }
    }
}
  
$todoListServer = new TodoListAPI();
$todoListServer->init();
