
async function get_titles_exists() {
    return await fetch('files.php')
        .then(response => response.json())
        .then(files => {
            return files
        })
        .catch(error => console.error('Error loading files:', error));
}

function AllArticles_add(len) {
    var len_in = parseFloat($("#AllArticles").text());
    $("#AllArticles").text(len_in + len);
}

async function one_cat(key, titles, files) {

    var len = 0
    const key2 = key.replaceAll(' ', '_');

    for (const title in titles) {
        len += 1
        var html_link = titles[title] + '.html';
        if (files.includes(html_link)) {
            /*
            const div = document.createElement('div');
            div.className = 'col';
            div.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">
                                <a class="card-link" href="https://mdwiki.org/wiki/${title}" target="_blank">${title}</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            <a class="card-link" href="wikitext/${titles[title]}.txt" target="_blank">Wikitext</a>
                            <a class="card-link" href="html/${titles[title]}.html" target="_blank">Html</a>
                            <a class="card-link" href="segments/${titles[title]}.html" target="_blank">Segments</a>
                        </div>
                    </div>
                `;*/
            const row = `
                <tr>
                    <td class="align-middle">
                    </td>
                    <td class="align-middle">
                        <a class="card-link" href="https://mdwiki.org/wiki/${title}" target="_blank">${title}</a>
                    </td>
                    <td class="align-middle">
                        <a class="card-link" href="wikitext/${titles[title]}.txt" target="_blank">Wikitext</a>
                    </td>
                    <td class="align-middle">
                        <a class="card-link" href="html/${titles[title]}.html" target="_blank">Html</a>
                    </td>
                    <td class="align-middle">
                        <a class="card-link" href="segments/${titles[title]}.html" target="_blank">Segments</a>
                    </td>
                </tr>`
            $(`#tbody_${key2}`).append(row)
            // to_add.append(row);
        }
    }
    $(`#${key2}_count`).text(`(${len} titles)`);
    AllArticles_add(len);

}

async function add_titles() {
    const data = await fetch('cats_titles.json')
        .then(response => response.json())
        .then(data => {
            return data
        })

    for (const key in data) {

        const key2 = key.replaceAll(' ', '_');
        const key_id = `tbody_${key2}`
        const mainlist = document.getElementById('main');

        const fileList = document.createElement('div');
        fileList.className = 'row-cols-1 mt-4';
        fileList.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <a class="card-link" href="https://mdwiki.org/wiki/Category:${key}" target="_blank">Category:${key}</a> <span id="${key2}_count"></span>
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table compact table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Wikitext</th>
                                <th>Html</th>
                                <th>Segments</th>
                            </tr>
                        </thead>
                        <tbody id="${key_id}">
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        mainlist.appendChild(fileList);
    }
}

async function by_cat(files) {
    const data = await fetch('cats_titles.json')
        .then(response => response.json())
        .then(data => {
            return data
        })
        .catch(error => console.error('Error loading titles.json:', error));

    for (const key in data) {
        one_cat(key, data[key], files);
    }
}

async function table_ready() {
    $('.table').DataTable({
        select: true,
        columnDefs: [
            {
                targets: 0, // Target the first column
                render: function (data, type, row, meta) {
                    return meta.row + 1; // Return the row number (1-based index)
                }
            }
        ]
    });

}
// load when window ready
$(document).ready(async function () {
    await add_titles();
    // ---
    const files = await get_titles_exists();
    // ---
    $("#Articles").text(files.length);
    // ---
    await by_cat(files);
    // ---
    await table_ready();
});
