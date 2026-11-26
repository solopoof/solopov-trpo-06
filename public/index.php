<?php
declare(strict_types=1);
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
require_once __DIR__.'/../src/helpers.php';
require_once __DIR__.'/../src/NoteRepository.php';
require_once __DIR__.'/../src/NotesController.php';
$controller=new NotesController(new NoteRepository(__DIR__.'/../dataset/notes.json'),__DIR__.'/../dataset/users.json');
if($_SERVER['REQUEST_METHOD']==='POST'){$controller->handlePost();header('Location: /?page=notes',true,303);exit;}
$page=(string)($_GET['page']??'notes');
if($page!=='notes'){http_response_code(404);render('404.php',['requestedPage'=>$page]);exit;}
render('notes.php',$controller->viewData());
