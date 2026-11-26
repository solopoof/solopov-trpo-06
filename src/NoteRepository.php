<?php
declare(strict_types=1);
final class NoteRepository {
 public function __construct(private string $file){}
 public function all():array{
  if(!is_file($this->file)||!is_readable($this->file))throw new RuntimeException('Файл notes.json отсутствует или недоступен.');
  $raw=file_get_contents($this->file);if($raw===false)throw new RuntimeException('Не удалось прочитать notes.json.');
  $notes=json_decode($raw,true,512,JSON_THROW_ON_ERROR);if(!is_array($notes))throw new RuntimeException('В notes.json ожидался массив.');
  foreach($notes as $note)if(!is_array($note)||!isset($note['id'],$note['title']))throw new RuntimeException('Структура notes.json некорректна.');
  return $notes;
 }
 public function add(string $title):array{
  $title=trim($title);$length=preg_match_all('/./us',$title);
  if($title==='')throw new InvalidArgumentException('Введите текст заметки.');
  if($length===false||$length>180)throw new InvalidArgumentException('Заметка должна содержать не более 180 символов.');
  $notes=$this->all();$id=1;foreach($notes as $note)$id=max($id,(int)$note['id']+1);
  $notes[]=['id'=>$id,'title'=>$title];
  $json=json_encode($notes,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
  if(file_put_contents($this->file,$json,LOCK_EX)===false)throw new RuntimeException('Не удалось сохранить notes.json.');
  return $notes;
 }
}
