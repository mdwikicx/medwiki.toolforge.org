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

$dirs = array_filter(glob(__DIR__ . '/revisions_new/*/'), 'is_dir');
$tbody = '';
// ---
$number = 0;
// ---
foreach ($dirs as $dir) {
    // ---
    $number += 1;
    // ---
    $dir = rtrim($dir, '/');
    // ---
    $oldid = basename($dir);
    $oldid_number = str_replace('_all', '', $oldid);
    // ---
    $files = array_filter(glob("$dir/*"), 'is_file');
    $file_count = count($files);
    $tbody .= <<<HTML
        <tr>
            <td>$number</td>
            <td>
                <a class="card-link" href="https://mdwiki.org/wiki/index.php?oldid=$oldid_number" target="_blank">$oldid</a>
            </td>
            <td>
                <a class="card-link" href="revisions_new/$dir/wikitext.txt" target="_blank">Wikitext</a>
            </td>
            <td>
                <a class="card-link" href="revisions_new/$dir/html.html" target="_blank">Html</a>
            </td>
            <td>
                <a class="card-link" href="revisions_new/$dir/seg.html" target="_blank">Segments</a>
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
