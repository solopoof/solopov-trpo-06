<?php
declare(strict_types=1);
final class NoteRepository {
 public function __construct(private string $file){}
 public function all():array {
  if(!is_file($this->file))return [];
  $raw=file_get_contents($this->file);if($raw===false)throw new RuntimeException('Ошибка чтения');
  $data=json_decode($raw,true,512,JSON_THROW_ON_ERROR);if(!is_array($data))throw new RuntimeException('Ожидался массив JSON');return $data;
 }
 private function change(callable $callback):array {
  $h=fopen($this->file,'c+');if(!$h)throw new RuntimeException('Ошибка открытия хранилища');
  try {if(!flock($h,LOCK_EX))throw new RuntimeException('Не удалось заблокировать файл');$raw=stream_get_contents($h);$items=$raw?json_decode($raw,true,512,JSON_THROW_ON_ERROR):[];$items=$callback($items);rewind($h);ftruncate($h,0);fwrite($h,json_encode($items,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));fflush($h);flock($h,LOCK_UN);return $items;}
  finally{fclose($h);}
 }
 public function add(string $title):array {$title=trim($title);if($title==='')throw new InvalidArgumentException('Введите текст заметки');return $this->change(function($items)use($title){$id=1;foreach($items as $n)$id=max($id,(int)$n['id']+1);$items[]=['id'=>$id,'title'=>$title,'done'=>false];return $items;});}
 public function toggle(int $id):array{return $this->change(function($items)use($id){$found=false;foreach($items as &$n)if((int)$n['id']===$id){$n['done']=!$n['done'];$found=true;break;}unset($n);if(!$found)throw new OutOfBoundsException('Заметка не найдена');return $items;});}
}
