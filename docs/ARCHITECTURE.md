# Архитектура

Запрос поступает в public/index.php. NotesController проверяет действие, обращается к NoteRepository и готовит данные для шаблона. NoteRepository отвечает за JSON-файл. templates/notes.php только отображает подготовленные данные и экранирует текст.
