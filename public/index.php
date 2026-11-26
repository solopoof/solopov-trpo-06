<?php
declare(strict_types=1);
require_once __DIR__.'/../src/helpers.php';require_once __DIR__.'/../src/NoteRepository.php';require_once __DIR__.'/../src/NotesController.php';
$controller=new NotesController(new NoteRepository(__DIR__.'/../dataset/notes.json'));
$data=$controller->handle();
require __DIR__.'/../templates/notes.php';
