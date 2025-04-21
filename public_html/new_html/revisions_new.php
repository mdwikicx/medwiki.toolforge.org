<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/font-awesome/5.15.3/css/all.min.css'>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css'>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.css'>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css'>
    <link rel='stylesheet' href='https://tools-static.wmflabs.org/cdnjs/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.css'>

    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.7.0/jquery.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/popper.js/2.11.8/umd/popper.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/datatables.net/2.2.2/dataTables.js'></script>
    <script src='https://tools-static.wmflabs.org/cdnjs/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.min.js'></script>
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

<?php
// Enable error reporting for debugging
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function make_badge($files, $file)
{
    // ---
    if (!in_array($file, $files)) {
        return "<span class='badge bg-danger'>Missing</span>";
    }
    // ---
    // return "<span class='badge bg-success'>OK</span>";
    return "";
}
// ---
$mainDir = __DIR__ . '/revisions_new/';
// ---
$dirs = array_filter(glob(__DIR__ . '/revisions_new/*/'), 'is_dir');
// sort directories by last modified date
usort($dirs, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});
// ---
$tbody = '';
// ---
$number = 0;
// ---
$main_url = $_SERVER['REQUEST_URI'];
$main_url = str_replace('/revisions_new.php', '', $main_url);
// ---
foreach ($dirs as $dir) {
    // ---
    $number += 1;
    // ---
    $lastModified = date('Y-m-d H:i', filemtime($dir));
    // ---
    $dir = rtrim($dir, '/');
    // ---
    $dir_path = basename($dir);
    $oldid_number = str_replace('_all', '', $dir_path);
    // ---
    $files = array_filter(glob("$dir/*"), 'is_file');
    // ---
    $files = array_map('basename', $files);
    // ---
    // if wikitext.txt in $files
    $wikitext_tag = make_badge($files, 'wikitext.txt');
    $html_tag = make_badge($files, 'html.html');
    $seg_tag = make_badge($files, 'seg.html');
    // ---
    $title = (is_file("$dir/title.txt")) ? file_get_contents("$dir/title.txt") : '';
    $title = htmlspecialchars($title);
    // ---
    $tbody .= <<<HTML
        <tr>
            <td>$number</td>
            <td>$lastModified</td>
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?title=$title" target="_blank">$title</a>
            </td>
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?oldid=$oldid_number" target="_blank">$dir_path</a>
            </td>
            <td>
                <a class="card-link" href="$main_url/revisions_new/$dir_path/wikitext.txt" target="_blank">Wikitext</a> $wikitext_tag
            </td>
            <td>
                <a class="card-link" href="$main_url/revisions_new/$dir_path/html.html" target="_blank">Html</a> $html_tag
            </td>
            <td>
                <a class="card-link" href="$main_url/revisions_new/$dir_path/seg.html" target="_blank">Segments</a> $seg_tag
            </td>
        </tr>
    HTML;
}
// ---
?>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MDWiki</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table class="table compact table-striped" id="main_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>lastModified</th>
                            <th>Title</th>
                            <th>Revision</th>
                            <th>Wikitext</th>
                            <th>Html</th>
                            <th>Segments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tbody; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $('#main_table').DataTable({
            paging: false,
            lengthMenu: [
                [25, 50, 100, 200],
                [25, 50, 100, 200]
            ],
        });
    </script>



</body>

</html>
