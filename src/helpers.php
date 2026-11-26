<?php
declare(strict_types=1);
function e(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function loadJsonArray(string $file):array{
 if(!is_file($file)||!is_readable($file))throw new RuntimeException('Файл '.basename($file).' отсутствует или недоступен.');
 $raw=file_get_contents($file);if($raw===false)throw new RuntimeException('Не удалось прочитать '.basename($file).'.');
 $data=json_decode($raw,true,512,JSON_THROW_ON_ERROR);if(!is_array($data))throw new RuntimeException('В '.basename($file).' ожидался JSON-массив.');return $data;
}
function render(string $template,array $data=[]):void{
 $base=realpath(__DIR__.'/../templates');$path=realpath(__DIR__.'/../templates/'.$template);
 if($base===false||$path===false||!str_starts_with($path,$base.DIRECTORY_SEPARATOR)||!is_file($path))throw new RuntimeException('Шаблон не найден.');
 extract($data,EXTR_SKIP);require $path;
}
