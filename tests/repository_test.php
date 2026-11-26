<?php
declare(strict_types=1);
require_once __DIR__.'/../src/helpers.php';
require_once __DIR__.'/../src/NoteRepository.php';
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
$directory=sys_get_temp_dir().'/trpo06_'.bin2hex(random_bytes(5));mkdir($directory);$file=$directory.'/notes.json';
file_put_contents($file,'[{"id":1,"title":"Тест"}]');
try{
 $repository=new NoteRepository($file);check(count($repository->all())===1,'Чтение заметки');
 $notes=$repository->add('Новая заметка');check(count($notes)===2&&$notes[1]['id']===2,'Добавление и уникальный ID');
 $invalid=false;try{$repository->add('   ');}catch(InvalidArgumentException){$invalid=true;}check($invalid,'Пустой текст отклонён');
 $missing=new NoteRepository($directory.'/missing.json');$invalid=false;try{$missing->all();}catch(RuntimeException){$invalid=true;}check($invalid,'Отсутствующий файл обработан');
 file_put_contents($file,'{broken');$invalid=false;try{$repository->all();}catch(JsonException){$invalid=true;}check($invalid,'Повреждённый JSON обработан');
 $users=loadJsonArray(__DIR__.'/../dataset/users.json');$scenarios=loadJsonArray(__DIR__.'/../dataset/session_scenarios.json');
 check(count($users)===2,'Данные users.json прочитаны');check(count($scenarios)===3,'Сценарии сессии прочитаны');
 check($scenarios[1]['value']===$users[0]['name'],'Сценарий выбора пользователя соответствует users.json');
 check(e('<script>')==='&lt;script&gt;','HTML-экранирование');
}finally{foreach(glob($directory.'/*')?:[] as $path)unlink($path);rmdir($directory);}
echo "TRPO-06: OK\n";
