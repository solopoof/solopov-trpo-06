<?php
declare(strict_types=1);
final class NotesController {
 public function __construct(private NoteRepository $notes,private string $usersFile,private string $scenariosFile){}
 public function handlePost():void{
  if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')return;$_SESSION['display_name']??='Гость';$message='';$error='';$action=(string)($_POST['action']??'');
  try{
   if($action==='add'){$this->notes->add((string)($_POST['title']??''));$message='Заметка добавлена.';}
   elseif($action==='toggle'){$this->notes->toggle((int)($_POST['id']??0));$message='Статус заметки изменён.';}
   elseif($action==='set_name'){$users=loadJsonArray($this->usersFile);$id=(int)($_POST['user_id']??0);$selected=null;foreach($users as $u)if((int)($u['id']??0)===$id){$selected=$u;break;}if($selected===null)throw new InvalidArgumentException('Выберите пользователя из users.json.');$_SESSION['display_name']=(string)$selected['name'];$message='Пользователь сессии изменён.';}
   elseif($action==='clear'){$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),' ',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_regenerate_id(true);$_SESSION['display_name']='Гость';$message='Сессия сброшена.';}
   else throw new InvalidArgumentException('Неизвестное действие формы.');
  }catch(Throwable $x){$error=$x->getMessage();}
  $_SESSION['flash']=['message'=>$message,'error'=>$error];
 }
 public function viewData():array{
  $flash=$_SESSION['flash']??['message'=>'','error'=>''];unset($_SESSION['flash']);$storageError='';
  try{$notes=$this->notes->all();}catch(Throwable $x){$notes=[];$storageError=$x instanceof JsonException?'Файл notes.json содержит некорректный JSON.':$x->getMessage();}
  try{$users=loadJsonArray($this->usersFile);}catch(Throwable $x){$users=[];$storageError=$storageError?:$x->getMessage();}
  try{$scenarios=loadJsonArray($this->scenariosFile);}catch(Throwable $x){$scenarios=[];$storageError=$storageError?:$x->getMessage();}
  return ['notes'=>$notes,'users'=>$users,'scenarios'=>$scenarios,'displayName'=>(string)($_SESSION['display_name']??'Гость'),'message'=>(string)($flash['message']??''),'error'=>(string)($flash['error']??''),'storageError'=>$storageError];
 }
}
