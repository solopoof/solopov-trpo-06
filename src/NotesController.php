<?php
declare(strict_types=1);
final class NotesController {
 public function __construct(private NoteRepository $notes,private string $usersFile){}
 public function handlePost():void{
  if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')return;
  $_SESSION['display_name']??='Гость';$message='';$error='';$action=(string)($_POST['action']??'');
  try{
   if($action==='add'){$this->notes->add((string)($_POST['title']??''));$message='Заметка добавлена.';}
   elseif($action==='set_name'){$users=loadJsonArray($this->usersFile);$id=(int)($_POST['user_id']??0);$selected=null;foreach($users as $user)if((int)($user['id']??0)===$id){$selected=$user;break;}if($selected===null)throw new InvalidArgumentException('Выберите пользователя.');$_SESSION['display_name']=(string)$selected['name'];$message='Имя пользователя сохранено.';}
   elseif($action==='clear'){$_SESSION=[];if(ini_get('session.use_cookies')){$params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);}session_regenerate_id(true);$_SESSION['display_name']='Гость';$message='Сессия очищена.';}
   else throw new InvalidArgumentException('Неизвестное действие формы.');
  }catch(Throwable $exception){$error=$exception->getMessage();}
  $_SESSION['flash']=['message'=>$message,'error'=>$error];
 }
 public function viewData():array{
  $flash=$_SESSION['flash']??['message'=>'','error'=>''];unset($_SESSION['flash']);
  try{$notes=$this->notes->all();$storageError='';}catch(Throwable $exception){$notes=[];$storageError=$exception instanceof JsonException?'Файл notes.json содержит некорректный JSON.':$exception->getMessage();}
  try{$users=loadJsonArray($this->usersFile);}catch(Throwable $exception){$users=[];$storageError=$storageError?:$exception->getMessage();}
  return ['notes'=>$notes,'users'=>$users,'displayName'=>(string)($_SESSION['display_name']??'Гость'),'message'=>(string)($flash['message']??''),'error'=>(string)($flash['error']??''),'storageError'=>$storageError];
 }
}
