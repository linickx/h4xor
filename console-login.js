function decode_entities(str){
    var temp=document.createElement("div");
    temp.innerHTML=str;
    return temp.firstChild.nodeValue;
}

function output(message) {
    var d = document.getElementById("out");
    d.innerHTML += message;
}

function error(message) {
    output('<div class="login_error">' + message + '</div>');
}

function message(message) {
    output('<p class="message">' + message + '</p>');
}

function printResponse(data) {
    if (data.err) {
        error(data.err);
    }
    if (data.msgs) {
        message(data.msgs);
    }
    if (data.msg) {
        output(data.msg);
    }
}

var rp_key, rp_login;

function parseResponse(new_stage) {
    if (loginDebug) {
        console.debug("parsing response (" + new_stage + ")");
    }
    var data = JSON.parse(response);
    if (data.title) {
        document.title = decode_entities(data.title);
    }
    if (data.output) {
        printResponse(data.output);
    }
    if (data.key) {
        rp_key = data.key;
    }
    if (data.login) {
        rp_login = data.login;
    }
    if (data.redirect) {
        if (loginDebug) {
            console.debug("redirecting to: " + data.redirect);
        }
        window.location = data.redirect;
    } else {
        stage = new_stage;
        setAction(data.action);
        doAction();
    }
}

function showInput(passwd) {
    var d = document.getElementById("in");
    d.value = "";
    if (passwd) {
        d.style.color = "transparent";
    } else {
        d.style.color = "";
    }
    d.style.visibility = "visible";
    d.focus();
}

function hideInput() {
    var d = document.getElementById("in");
    d.style.visibility = "hidden";
    return d.value;
}

function getInput() {
    var d = document.getElementById("in");
    return d.value;
}

function isInputShown() {
//    var d = document.getElementById("in");
//    return d.style.visibility == "visible";
    var d = document.getElementById("out");
    var e = d.lastChild;
    var f;
    while (true) {
        f = e;
        if ("tagName" in e) {
            if (e.innerHTML.trim()) {
                var tag = e.tagName.toUpperCase();
                if (tag == "DIV" || tag == "SPAN") {
                  e = e.lastChild;
                } else {
                    break;
                }
            } else {
                break;
            }
        } else {
            if (e.data.trim()) {
                return true;
            } else {
                e = e.previousSibling;
            }
        }
        if (!e) {
            if (f.parentNode == d) {
                return false;
            }
            e = f.parentNode.previousSibling;
        }
    }
    if (e) {
        if ("tagName" in e) {
            var tag = e.tagName.toUpperCase();
            return !(tag == "BR" || tag == "P");
        }
        return true;
    }
    return false;
}

function sendPost(params, get_params) {
    var url1;
    if (url.indexOf('?') == -1) {
        url1 = url + "?action=" + action + "&ajax=true";
    } else {
        url1 = url + "&action=" + action + "&ajax=true";
    }
    if (get_params) {
        url1 = url1 + "&" + get_params;
    }
    var xhr = new XMLHttpRequest();
    xhr.open("POST",url1,true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Content-length", params.length);
    xhr.setRequestHeader("Connection", "close");
    
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4) {
            if (xhr.status == 200) {
                response = xhr.responseText;
            } else {
                error("Connection error: " + xhr.status + " " + xhr.statusText);
                stage = 0;
            }
            doAction();
        }
    }
    xhr.send(params);
}

function sendGet(get_params) {
    var url1;
    if (url.indexOf('?') == -1) {
        url1 = url + "?action=" + action + "&ajax=true";
    } else {
        url1 = url + "&action=" + action + "&ajax=true";
    }
    if (get_params) {
        url1 = url1 + "&" + get_params;
    }
    var xhr = new XMLHttpRequest();
    xhr.open("GET",url1,true);
    xhr.setRequestHeader("Connection", "close");
    
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4) {
            if (xhr.status == 200) {
                response = xhr.responseText;
            } else {
                error("Connection error: " + xhr.status + " " + xhr.statusText);
                stage = 0;
            }
            doAction();
        }
    }
    xhr.send(null);
}

var action;
var stage = 0;
var url;
var response;
var prefix = " ";
var loginDebug = false;

function hashCallback(hash) {
    if (loginDebug) {
        console.debug("hashCallback");
    }
    var act = hash.substring(1)
    switch (act) {
        case "login":
        case "lostpassword":
        case "retrievepassword":
        case "resetpass":
        case "rp":
        case "register":
            if (loginDebug) console.debug("hash OK");
            action = act;
            stage = -1;
            sendGet();
            return true;
        default:
            if (loginDebug) console.debug("hash wrong");
            return false;
    }   
}

function setAction(act) {
    if (!act) {
        act = "login";
    }
    action = act;
    setHashSilently(act);
}

function setupAction() {
    setupSmartFragment(hashCallback, false);
    if (!action) {
        action = window.location.hash.substring(1);
    }
    document.getElementById("in").onkeypress = function(event) {
        if (event.keyCode == 13) {
            doAction();
        }
    };
    doAction();
}

function doAction() {
    if (loginDebug) {
        console.debug("Action: " + action + " Stage: " + stage);
    }
    setAction(action);
    if (stage == -1) {
        parseResponse(0);
        return;
    }
    switch (action) {
        case "login":
            login();
            break;
        case "lostpassword":
        case "retrievepassword":
            lostpasswd();
            break;
        case "resetpass":
        case "rp":
            resetpass();
            break;
        case "register":
            register();
            break;
    }
}

var username;

function login() {
    if (stage == 1) {
        username = hideInput();
        output(username + "<br/>");
        if (username) {
            ++stage;
        }
    }
    if (stage == 0) {
        ++stage;
    }
    if (stage < 2) {
        if (isInputShown()) output("<br/>");
        output(prefix + "login: ");
        showInput(false);
    }
    if (stage == 4) {
        parseResponse(0);
    }
    if (stage == 3) {
        var password = hideInput();
        output("<br/>");
        var params = "log=" + encodeURIComponent(username) + "&pwd=" + encodeURIComponent(password);
        sendPost(params);
        ++stage;
    }
    if (stage == 2) {
        output("Password: ");
        showInput(true);
        ++stage;
    }
}

function lostpasswd() {
    if (stage == 1) {
        username = hideInput();
        output(username + "<br/>");
        if (username) {
            ++stage;
        }
    }
    if (stage == 0) {
        ++stage;
    }
    if (stage < 2) {
        if (isInputShown()) output("<br/>");
        output("login or e-mail: ");
        showInput(false);
    }
    if (stage == 3) {
        parseResponse(0);
    }
    if (stage == 2) {
        var params = "user_login=" + encodeURIComponent(username);
        sendPost(params);
        ++stage;
    }
}

var newpass;

function resetpass() {
    if (stage == 1) {
        newpass = hideInput();
        output("<br/>");
        if (newpass) {
            ++stage;
        }
    }
    if (stage == 0) {
        ++stage;
    }
    if (stage == 3) {
        var confirm = hideInput();
        if (confirm == newpass) {
            var params = "pass1=" + encodeURIComponent(newpass) + "&pass2=" + encodeURIComponent(confirm);
            var get_params = "key=" + encodeURIComponent(rp_key) + "&login=" + encodeURIComponent(rp_login);
            sendPost(params, get_params);
            ++stage;
        } else {
            error("The passwords do not match.");
            stage -= 2;
        }
    }
    if (stage < 2) {
        if (isInputShown()) output("<br/>");
        output("new password: ");
        showInput(true);
    }
    if (stage == 2) {
        output("confirm password: ");
        showInput(true);
        ++stage;
    }
    if (stage == 4) {
        parseResponse(0);
    }
}

function register() {
    if (stage == 1) {
        username = hideInput();
        output(username + "<br/>");
        if (username) {
            ++stage;
        }
    }
    if (stage == 0) {
        ++stage;
    }
    if (stage < 2) {
        if (isInputShown()) output("<br/>");
        output("username: ");
        showInput(false);
    }    
    if (stage == 4) {
        parseResponse(0);
    }
    if (stage == 3) {
        var email = hideInput();
        output(email + "<br/>");
        var params = "user_login=" + encodeURIComponent(username) + "&user_email=" + encodeURIComponent(email);
        sendPost(params);
        ++stage;
    }
    if (stage == 2) {
        output("e-mail: ");
        showInput(false);
        ++stage;
    }
}