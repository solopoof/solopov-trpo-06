<?php
declare(strict_types=1);
final class NotesController {
 public function __construct(private NoteRepository $repo){}
 public function handle():array {
  if(session_status()!==PHP_SESSION_ACTIVE)session_start();$_SESSION['display_name']??='Гость';
  $error='';$message='';
  if($_SERVER['REQUEST_METHOD']==='POST'){
   $action=(string)($_POST['action']??'');
   try {
    if($action==='add'){$this->repo->add((string)($_POST['title']??''));$message='Заметка добавлена.';}
    elseif($action==='toggle')$this->repo->toggle((int)($_POST['id']??0));
    elseif($action==='name'){$name=trim((string)($_POST['display_name']??''));if($name==='')throw new InvalidArgumentException('Имя не может быть пустым');$_SESSION['display_name']=mb_substr($name,0,60);}
    elseif($action==='clear'){$_SESSION=[];session_regenerate_id(true);$_SESSION['display_name']='Гость';$message='Сессия сброшена.';}
   }catch(Throwable $e){$error=$e->getMessage();}
   if($message===''&&$error==='')$message='Изменения сохранены.';
  }
  return ['notes'=>$this->repo->all(),'displayName'=>$_SESSION['display_name'],'error'=>$error,'message'=>$message];
 }
}
