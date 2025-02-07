<?php
// تحديد مسار المجلد
$directory = 'segments'; // قم بتعديل هذا المسار

// جلب قائمة الملفات
$files = scandir($directory);

// تصفية الملفات لإزالة العناصر غير المرغوب فيها مثل '.' و '..'
$files = array_diff($files, array('.', '..'));

// تحويل القائمة إلى JSON
header('Content-Type: application/json');
echo json_encode(array_values($files));
