<?php
require_once __DIR__.'/../src/NoteRepository.php';
$file=sys_get_temp_dir().'/notes_'.uniqid().'.json';file_put_contents($file,'[{"id":1,"title":"Тест","done":false}]');
$r=new NoteRepository($file);assert(count($r->all())===1);$a=$r->add('Новая');assert(count($a)===2);$a=$r->toggle(2);assert($a[1]['done']===true);unlink($file);echo "OK\n";
