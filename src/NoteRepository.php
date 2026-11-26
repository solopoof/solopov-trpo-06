<?php
declare(strict_types=1);
final class NoteRepository {
 public function __construct(private string $file){}
 public function all():array{
  if(!is_file($this->file))throw new RuntimeException('Хранилище notes.json не найдено.');
  $data=$this->readUnlocked();
  foreach($data as $n)if(!is_array($n)||!isset($n['id'],$n['title'])||!array_key_exists('done',$n))throw new RuntimeException('Структура записи в notes.json повреждена.');
  return $data;
 }
 private function readUnlocked():array{$raw=file_get_contents($this->file);if($raw===false)throw new RuntimeException('Не удалось прочитать notes.json.');$d=json_decode($raw,true,512,JSON_THROW_ON_ERROR);if(!is_array($d))throw new RuntimeException('В notes.json ожидался массив.');return $d;}
 private function change(callable $callback):array{
  $dir=dirname($this->file);if(!is_dir($dir)||!is_writable($dir))throw new RuntimeException('Папка хранилища недоступна для записи.');
  $lock=fopen($this->file.'.lock','c');if($lock===false)throw new RuntimeException('Не удалось открыть файл блокировки.');
  try{
   if(!flock($lock,LOCK_EX))throw new RuntimeException('Не удалось заблокировать хранилище.');
   if(!is_file($this->file))throw new RuntimeException('Хранилище notes.json не найдено.');
   $items=$callback($this->readUnlocked());$json=json_encode($items,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
   $tmp=tempnam($dir,'notes_');if($tmp===false)throw new RuntimeException('Не удалось создать временный файл.');
   try{if(file_put_contents($tmp,$json,LOCK_EX)===false)throw new RuntimeException('Не удалось записать временный файл.');if(!rename($tmp,$this->file))throw new RuntimeException('Не удалось обновить notes.json.');}finally{if(is_file($tmp))unlink($tmp);}
   return $items;
  }finally{flock($lock,LOCK_UN);fclose($lock);}
 }
 public function add(string $title):array{$title=trim($title);$len=preg_match_all('/./us',$title);if($title==='')throw new InvalidArgumentException('Введите текст заметки.');if($len===false||$len>180)throw new InvalidArgumentException('Заметка должна содержать не более 180 символов.');return $this->change(static function(array $items)use($title):array{$id=1;foreach($items as $n)$id=max($id,(int)($n['id']??0)+1);$items[]=['id'=>$id,'title'=>$title,'done'=>false];return $items;});}
 public function toggle(int $id):array{return $this->change(static function(array $items)use($id):array{foreach($items as &$n){if((int)$n['id']===$id){$n['done']=!(bool)$n['done'];unset($n);return $items;}}unset($n);throw new OutOfBoundsException('Заметка не найдена.');});}
}
