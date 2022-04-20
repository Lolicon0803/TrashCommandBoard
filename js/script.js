
function login() {
    var username = document.getElementById("username");
    var password = document.getElementById("password");
    const name = username.value;
    const pwd = password.value;

    let body = new FormData();
    body.append('username', name);
    body.append('password', pwd);


    fetch('./api/login_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {
            if (json['status'] === 'success') {
                window.location.href = './';
                alert(json['message']);
            } else if (json['status'] === 'error') {
                alert(json['message']);
            }
        });
}


function logon() {


    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const password2 = document.getElementById("password2").value;
    var uploadType = document.getElementsByName("uploadType");
    var type = '0';
    for (const element of uploadType) {
        if (element.checked) {
            type = element.value;
            break;
        }
    }


    let body = new FormData();
    body.append('username', username);
    body.append('password', password);
    body.append('password2', password2);
    body.append('uploadType', type);

    if (type === '0') {
        const file = document.getElementById('file').files[0];
        body.append('file', file);
        body.append('photoLink', "null");

    } else if (type === '1') {
        const photoLink = document.getElementById("photoLink").value;
        body.append('photoLink', photoLink);

    } else {
        return 0;
    }



    fetch('./api/logon_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {
            if (json['status'] === 'success') {
                window.location.href = './';
                alert(json['message']);
            } else if (json['status'] === 'error') {
                alert(json['message']);
            }
        });
}

function logout() {

    fetch('./api/logout_api.php')
        .then((response) => response.text())
        .then((json) => {
            console.log(json);

            if (json['status'] === 'success') {

                alert(json['message']);
            }
        });

}

function allMsg() {

    let body = new FormData();
    body.append('all_msg', '1');
    body.append('msg_id', '0');
    fetch('./api/showmsg_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {

            let msgdata = json['data'];
            json2table(msgdata, $("#table"));

            alert(json['message']);
        });

}


function del_msg() {
    const del_id = document.getElementById("del_id").value;

    let body = new FormData();
    body.append('msg_id', del_id);


    fetch('./api/delmsg_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {
            window.location.href = './';
            alert(json['message']);
        });
}

function add_msg() {

    // content isfile

    const content = document.getElementById("content").value;


    var isfile = document.getElementsByName("isFile");
    var type = '0';
    for (const element of isfile) {
        if (element.checked) {
            type = element.value;
            break;
        }
    }


    let body = new FormData();
    body.append('content', content);
    body.append('isfile', type);

    if (type === '1') {
        const file = document.getElementById('file').files[0];
        body.append('file', file);
    }

    fetch('./api/addmsg_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {

            //console.log(json);

            window.location.href = './';
            alert(json['message']);


        });
}

function change_title() {

    const title = document.getElementById("title").value;
    let body = new FormData();
    body.append('title', title);


    fetch('./api/admin_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {

            //console.log(json);

            window.location.href = './';
            alert(json['message']);


        });
}


function load_title() {


    fetch('./api/title_api.php', {
        method: "POST"
    })
        .then((response) => response.json())
        .then((json) => {

            document.getElementById("btn_title").value = json['title'][0]['title'];
        });



}


function single_msg() {
    const msg_id = document.getElementById("msg_id").value;

    let body = new FormData();
    body.append('all_msg', '0');
    body.append('msg_id', msg_id);
    fetch('./api/showmsg_api.php', {
        method: "POST",
        body: body
    })
        .then((response) => response.json())
        .then((json) => {

            let msgdata = json['data'];
            json2table(msgdata, $("#table"));

            alert(json['message']);
        });
}

function json2table(json, $table) {
    //var json = JSON.parse(jsonString);
    var cols = Object.keys(json[0]);

    var headerRow = '';
    var bodyRows = '';

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    $table.html('<thead><tr></tr></thead><tbody></tbody>');

    cols.map(function (col) {
        headerRow += '<th>' + capitalizeFirstLetter(col) + '</th>';
    });

    json.map(function (row) {
        bodyRows += '<tr>';

        // bodyRows += '<td><type="button"  name="btn1" value="刪除" onClick="login()"></td>'
        cols.map(function (colName) {
            bodyRows += '<td>' + row[colName] + '</td>';
        })

        bodyRows += '</tr>';
    });

    $table.find('thead tr').append(headerRow);
    $table.find('tbody').append(bodyRows);
}


