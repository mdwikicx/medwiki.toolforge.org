
async function get_titles_exists() {
    try {
        const response = await fetch('files.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error loading files:', error);
        $('#error-alert').text('Failed to load files. Please try again.').show();
        return [];
    }
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
    $(`#${key2}_count`).text(`(${len})`);
    AllArticles_add(len);

}

async function cats_titles() {
    try {
        const response = await fetch('cats_titles.json');
        if (!response.ok) {
            $('#error-alert').text(`HTTP error! status: ${response.status}`).removeClass('d-none');
            return {};
        }
        const data = await response.json();
        if (!data || typeof data !== 'object') {
            $('#error-alert').text('Invalid data format').removeClass('d-none');
            return {};
        }
        return data;
    } catch (error) {
        console.error('Error loading categories:', error);
        $('#error-alert').text('Failed to load categories. Please try again.').removeClass('d-none');
        return {};
    }
}
async function add_titles() {
    const data = await cats_titles();

    const rows_ul = document.getElementById('rows_ul');
    const mainlist = document.getElementById('main');

    // const key_1 = 'all';
    // const li_f = document.createElement('li');
    // li_f.className = 'nav-item';
    // li_f.role = 'presentation';
    // li_f.innerHTML = `
    //     <button class="nav-link" id="tab_${key_1}" data-bs-toggle="tab" data-bs-target="#s_${key_1}" type="button"
    //         role="tab" aria-controls="s_${key_1}" aria-selected="true">ALL</button>
    // `;
    // rows_ul.appendChild(li_f);

    const main_rows = document.getElementById('main_rows');

    // var div = document.createElement('div');
    // div.className = 'tab-pane fade show active';
    // div.id = `s_${key_1}`;
    // div.role = 'tabpanel';
    // div.setAttribute('aria-labelledby', `tab_${key_1}`);
    // div.setAttribute('tabindex', `0`);
    // main_rows.appendChild(div);
    var active_done = false;

    for (const key in data) {

        const key2 = key.replaceAll(' ', '_');
        const key_id = `tbody_${key2}`
        var key_title = key;
        if (key == 'World Health Organization essential medicines') {
            key_title = 'WHO EM';
        }
        var active_class = '';
        var selected = 'false';
        if (!active_done) {
            active_done = true;
            active_class = 'active';
            selected = 'true';
        }
        const li_f = document.createElement('li');
        li_f.className = 'nav-item';
        li_f.role = 'presentation';
        li_f.innerHTML = `
            <button class="nav-link" id="tab_${key2}" data-bs-toggle="tab" data-bs-target="#s_${key2}" type="button"
                role="tab" aria-controls="s_${key2}" aria-selected="${selected}">${key_title} <span id="${key2}_count"></span></button>
        `;
        rows_ul.appendChild(li_f);


        const fileList = document.createElement('div');
        fileList.className = 'row-cols-1 mt-4';
        fileList.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <a class="card-link" href="https://mdwiki.org/wiki/Category:${key}" target="_blank">Category:${key}</a>
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

        var div2 = document.createElement('div');
        div2.className = 'tab-pane fade show ' + active_class;
        div2.id = `s_${key2}`;
        div2.role = 'tabpanel';
        div2.setAttribute('aria-labelledby', `tab_${key2}`);
        div2.setAttribute('tabindex', `0`);


        div2.appendChild(fileList);

        main_rows.appendChild(div2);

    }
}

async function by_cat(files) {
    const data = await cats_titles();

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
