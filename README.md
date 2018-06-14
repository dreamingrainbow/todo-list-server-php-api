# todo-list-server-php-api
This is a basic example of a PHP todo-list server script.

This will use some basic PHP to demonstrate building a Todo List API in PHP, to be used with our react front end.


Like our Express server we need to setup our system to use PHP. So download it and make it available in the command line. For this excersize we will use the built in developer server, check out these resouces --- Link Here --- on setting up an Apache Server to serve your API script. 

lets start by making a directory to host our server in. I'll call ours www and inside it im going to make an index.php file.

```
php -S localhost:80 -t www
```
This command get's out local developer server up and running, and serving our www folder.

Inside our www folder we can create our `index.php` file. It will contain the logic below.

```PHP
<?php
/**
*     We setup our file to handle the request method.
*/
$todoList = [];
$method = $_SERVER['REQUEST_METHOD'];
```

Now, that we can identify the method of the request being handled, we can write our logic to break down the request, and capture any data.

I'm going to handle the method's of our routes first. The first of these being the default page, we show when someone lands on our API.

```PHP
if('GET' === $method) {
  getRoutes();
} else if('POST' === $method) {
  postRoutes();
} else if ('PUT' === $method) {
  putRoutes();
} else if ('DELETE' === $method) {
  deleteRoutes();
} else {
  notFound();
    /* Since we are only dealing with POST PUT DELETE AND GET we will use the ELSE to catch everything else, and return a not found page. */
}
```

Great, we will try to keep this simple and secure as possible, however this application is by no means a production level application. In the above example I am using functional php, This could be simple rewritten as a class. Here's how..

```php
<?php
class TodoListAPI {
  public function __construct() { 
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->todoList = [];
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
}

$todoListServer = new TodoListAPI();
$todoListServer->init();
```

Now, that we have our basic routing methods in place, lets write the logic. Let's start with the easiest of them. 

## Not Found ##

```php
  /*
  *   return Page not Found.
  */
  function notFound(){
    echo('<h1>The page or file was <strong>not found</strong>.</h1>');
  }
```
`NOTE : this function can be used as a class method as well.`
Wow, simple enough right! Well of course you can go crazy here and import a page that you have designed, Go head try that now!


##  GET Routes  ##

Since, we have the default case done we will need to create our logic to handle our GET requests. We will call the function getRoutes
as well we can name our class method the same, as the logic for this is almost identicle.

```php
  /*
  * getRoutes This function handles all GET route requests.
  */
  function getRoutes() { 
      /*
      * Our application has only a few routes the GET Routes include the default page, and the todo-list.
      * We can grab the query string and see that it matches our required path.
      */
      $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      switch($path) {
        case '/todo-list':
          header('Content-Type: application/json');
          echo json_encode($todoList);
          break;
        default:
          echo('<h1>Todo List API</h1>');
      }
  }
```
Interesting enough there is only one small change to turn this function into a method that works in our class.

```php
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
```

Great! We now have a way to handle our routes, and have handled a couple of them. Let's continue by creating the rest of the logic we need to complete the rest of the route methods PUT, POST, and DELETE.
```php
    /*
    *   deleteRoutes Handle any delete route requests.
    */
    public function deleteRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        switch($path) {
            case '/todo':
            default:
                $delete = file_get_contents('php://input');
                $delete = json_decode($delete);
                $todoIndex = $delete->todoIndex; 
                if( isset($delete->todoIndex)
                     && is_numeric($delete->todoIndex) ) {
                        header('Content-Type: application/json');
                        $todoList = array_filter($todoList, 
                        function($k){ return($k !== $todoIndex); },
                        ARRAY_FILTER_USE_KEY );
                        echo json_encode($todoList); 
                } else {
                    header('Content-Type: application/json');
                    echo json_decode($todoList);
                }
        }
    }
```

Now As part of our class
```php
    /*
    *   deleteRoutes Handle any delete route requests.
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
                        $this->todoList = array_filter($this->todoList,
                        function($k){ return($k !== $this->todoIndex); },
                        ARRAY_FILTER_USE_KEY );
                        echo json_encode($this->todoList); /* We just need to reference our class instance.*/
                } else {
                    header('Content-Type: application/json');
                    echo json_decode($this->todoList);
                }
        }
    }
```

Sweet, well we are almost done now, just a little more to go. When need to handle our posted data and our update(PUT) data.

## POST ROUTES

In our next function we add a new todo to our list.
First we do the logic in a functional programming style.
```php
    /*
     *  postRoutes Handle all of the POST route requests  
     */
    function postRoutes() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        switch($path) {
            case '/todo':
            default:
                $post = file_get_contents('php://input');
                $post = json_decode($post);
                $todo = $_POST->todo; 
                array_push($todoList, $todo);
                header('Content-Type: application/json');
                echo json_decode($todoList);
        }
    }
 ```
 
 Now the same code for our class!
```php
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
 ```
 
Now we can add our todo's to our todo list. Only thing left to do is update our todo once we have completed it.

```php
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
                $todoIndex = $put->todoIndex;
                $todoList[$todoIndex]['completed'] = time();
                header('Content-Type: application/json');
                echo json_encode($todoList);
        }
    }
```
and finally our object oriented class method for handling our put requests.

```php
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
```
 
So, now we have the functionality, go head try it for yourself. You will notice, that as of now we dont persist our data! In the next lesson we expand on this lesson and persist our data in a file.
