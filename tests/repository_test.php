<?php
declare(strict_types=1);
require_once __DIR__.'/../src/helpers.php';require_once __DIR__.'/../src/NoteRepository.php';
function check(bool $ok,string $m):void{if(!$ok)throw new RuntimeException($m);}
$dir=sys_get_temp_dir().'/trpo06_'.bin2hex(random_bytes(5));mkdir($dir);$file=$dir.'/notes.json';file_put_contents($file,'[{"id":1,"title":"Тест","done":false}]');
try{
 $r=new NoteRepository($file);check(count($r->all())===1,'Read initial note');$a=$r->add('Новая заметка');check(count($a)===2&&$a[1]['id']===2,'Unique ID');$a=$r->toggle(2);check($a[1]['done']===true,'Toggle status');
 $bad=false;try{$r->add('   ');}catch(InvalidArgumentException){$bad=true;}check($bad,'Reject blank title');
 $missing=new NoteRepository($dir.'/missing.json');$bad=false;try{$missing->all();}catch(RuntimeException){$bad=true;}check($bad,'Missing file handled');
 file_put_contents($file,'{broken');$bad=false;try{$r->all();}catch(JsonException){$bad=true;}check($bad,'Corrupt JSON detected');
 $users=loadJsonArray(__DIR__.'/../dataset/users.json');$scenarios=loadJsonArray(__DIR__.'/../dataset/session_scenarios.json');check(count($users)===2,'Two users loaded');check(count($scenarios)===3,'Three scenarios loaded');check($scenarios[1]['value']===$users[0]['name'],'set_name scenario matches users.json');check(e('<script>')==='&lt;script&gt;','HTML escaping');
}finally{foreach(glob($dir.'/*')?:[] as $x)unlink($x);rmdir($dir);}
echo "TRPO-06: OK\n";
