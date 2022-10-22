var info = require('../info');
var connection = require('../mysql/DB');
var unixTime = require('unix-time');
var R = require('./rutes.js');
var T = require('./tables.js');
var async = require('async');
var forEach = require('async-foreach').forEach;
var _ = require('lodash');
var cookie = require('cookie');
var settings = Settings();
var util = require('util');

const sockets = [];

const query = util.promisify(connection.query).bind(connection);

async function Init(socket){
    var data = {
        loggedin: !1,
        word: {}
    };

    var validUsers = {};
    var blocked_users = [];
    
    var cookies = cookie.parse(socket.handshake.headers.cookie);

    if (!!cookies._LOGIN_TOKEN) {
        try {
            if (_.isString(cookies._LOGIN_TOKEN)) {
                if (!_.includes(validUsers, cookies._LOGIN_TOKEN)) {
                    await new Promise(function(resolve, reject) {
                        connection.query(`SELECT user_id FROM ${T.SESSION} WHERE token = ? LIMIT 1`, [cookies._LOGIN_TOKEN], async function(error, result, field) {
                            if (error) {
                                throw reject(error);
                            }
                            if (result.length > 0) {
                                var user_id = result[0].user_id,
                                    socket_id = socket.id;
                                
                                setSocket(socket_id, user_id);
                                validUsers[cookies._LOGIN_TOKEN] = user_id;
                                await BlockedUsers(user_id).then(function(res) {
                                    // Manejar la respuesta:
                                    blocked_users = res;
                                });
                                return resolve({
                                    loggedin: !0,
                                    user_id: user_id,
                                    blocked_users: blocked_users
                                })
                            }
                        });
                    }).then(function(res){
                        data = res;
                    }).catch(function(err){
                        console.log(err)
                    });
                    
                }
            }
        } catch (e) {
            console.log(e);
        }
    }
    /*
    emitChangesJustMe('setCookie', {
        name: 'language',
        value: Language(loggedin, cookies),
        maxAge: Time() + 315360000,
        path: "/"
    })
    */
    await Words(cookies.language).then(function(res){
        data.word = res;
    }).catch(function(err){
        console.log(err);
    });
    return data;
}

function setSocket(socket_id, user_id){
    var user = {socket_id, user_id};           
    sockets.push(user);
}

function getSockets(user_id, socket_id = 0){
    var data = [];
    forEach(sockets, function(sock){
        if(sock.user_id === user_id && sock.socket_id != socket_id){
            data.push(sock.socket_id);
        }
    })
    return data;
}

function getCurrentUser(socket_id){
    return sockets.find(sock => sock.socket_id === socket_id);
}

function pullSocket(socket_id){
    const index = sockets.findIndex(sock=>sock.socket_id === socket_id);
    if(index !== -1){
        sockets.splice(index, 1)[0];
    }
}

function Settings() {
    var data = {};
    try {
        connection.query(`SELECT * FROM ${T.SETTING}`, function(error, result, field){
            if (error) { 
                console.log(error);
                return;
            };
            forEach(result, function(item, index, arr){
                data[item['name']] = item['value'];
            })
        });
    } catch (e) {
        console.log(e);
    }
    return data;
}

function BlockedUsers(user_id){
    var data = [];
    return new Promise(function(resolve, reject) {
        try {
            if (settings['blocked_users'] == 'on') {
                connection.query(`SELECT user_id FROM ${T.BLOCK} WHERE user_id <> ? AND profile_id = ?`, [user_id, user_id], function(error, result, field){
                    if (error) { 
                        console.log(error);
                        return !1;
                    };
                    if(result.length > 0){
                        forEach(result, function(item, index, arr){
                            data.push(item.user_id);
                        })
                    }
                });
                connection.query(`SELECT profile_id FROM ${T.BLOCK} WHERE user_id = ? AND profile_id <> ?`, [user_id, user_id], function(error, result, field){
                    if (error) { 
                        console.log(error);
                        return !1;
                    };
                    if(result.length > 0){
                        forEach(result, function(item, index, arr){
                            data.push(item.profile_id);
                        })
                    }
                    return resolve(data)
                });
            }
        } catch (e) {
            return reject(e);
        }
    });
}

async function Data(socket, data, type = 1) {
    var user = {};
    
    if(!isNaN(type)){
        if(type == 1){
            await query(`SELECT * FROM ${T.USER} WHERE id = ?`, [data]).then(function(res){
                user = res;
            }).catch(function(err){
                console.log(err);
            });
        } else if(type == 3){
            user = data;
        } else {
            var cookies = cookie.parse(socket.handshake.headers.cookie);
            await query(`SELECT * FROM ${T.USER} WHERE (SELECT user_id FROM ${T.SESSION} WHERE token = ?) = id`, [cookies._LOGIN_TOKEN]).then(function(res){
                user = res;
            }).catch(function(err){
                console.log(err);
            });
        }
        
        if (user.length == 0) {
            return !1;
        }

        user = user[0];
        
        user.user = user.username;
        user.slug = ProfileUrl(user.username);
        
        if(user.notifications != 'NULL'){
            user.notifications = JSON.parse(user.notifications);
        } else {
            user.notifications = [
                'followers',
                'followed',
                'collab',
                'react',
                'pcomment',
                'preply',
                'ucomment',
                'ureply'
            ];
        }
        if(user.shows != 'NULL'){
            user.shows = JSON.parse(user.shows);
        } else {
            user.shows = {
                'birthday': 'on',
                'gender': 'on',
                'contact_email': 'on',
                'followers': 'on',
                'messages': 'on'
            };
        }
    } else {
        user = await query(`SELECT ${type.join(',')} FROM ${T.USER} WHERE id = ?`, [data]);
        user = user[0];
    }

    if (Object.keys(user).length == 0) {
        return !1;
    }

    if(user.name != '' && user.surname != ''){
        user.username = `${user.name} ${user.surname}`;
    } else if(user.username != ''){
        user.username = user.username;
    }

    if(user.birthday != 0){
        var birthday = new Date(user.birthday*1000);
        user.birth_day = birthday.getUTCDate();
        user.birthday_month = birthday.getUTCMonth()+1;
        user.birthday_year = birthday.getFullYear();
        user.birthday_format = DateFormat(socket, user.birthday);
    }
    
    if(user.avatar != ''){
        var rute = 4;
        if(user.avatar == 'default-holder'){
            rute = 5;
        }
        user.ex_avatar_b = `uploads/users/${user.avatar}-b.jpeg`;
        user.ex_avatar_s = `uploads/users/${user.avatar}-s.jpeg`;
        user.avatar_b = GetFile(user.avatar, rute, 'b');
        user.avatar_s = GetFile(user.avatar, rute, 's');
    }
    if(user.gender != ''){
        user.gender_txt = global.TEMP[socket.id].word[user.gender];
    }
    if(user.created_at != ''){
        user.created_fat = DateFormat(socket, user.created_at);
        user.created_sat = DateString(socket, user.created_at);
    }
    return Object.keys(user).length > 1 ? user : Object.values(user);
}

function DateFormat(socket, ptime, type = 'normal') {
    var word = global.TEMP[socket.id].word;

    var date = new Date(ptime*1000);
    var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    var months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
    
    var day = days[date.getDay()];
    var month = months[date.getMonth()+1];
    day = word[day];
    month = word[month];
    var B = month.substr(0, 3);

    var dateFinaly = `${date.getUTCDate()} ${B} ${date.getFullYear()}`;
    if(type == 'day'){
        dateFinaly = date.getUTCDate();
    }
    if(type == 'month'){
        dateFinaly = month;
    }
    if(type == 'complete'){
        dateFinaly =  `${day}, ${date.getUTCDate()} ${word.of} ${month} ${date.getFullYear()}`;
    }

    return dateFinaly;
}

function DateString(socket, time) {
    var word = global.TEMP[socket.id].word;

    var diff = Time() - time,
        string = String;
    if (diff < 1) {
        return word.now;
    }
    dates = {
        31536000: [word.year, word.years],
        2592000: [word.month, word.months],
        86400: [word.day, word.days],
        3600: [word.hour, word.hours],
        60: [word.minute, word.minutes],
        1: [word.second, word.seconds]
    };

    async.forEachOf(dates, function(value, key){
        var was = diff/key;
        if (was >= 1) {
            var was_int = parseInt(was);
            string = was_int > 1 ? value[1] : value[0];
            string = `${word.does} ${was_int} ${string}`;
        }
    });
    return string;
}

function GetFile(file, type = 1, size = 's'){
    if (file == '') {
        return '';
    }
    var prefix = '';
    var suffix = '';
    if(type == 2){
        prefix = `themes/${settings.theme}/`;
    } else {
        if(type == 3) {
            prefix = 'uploads/entries/';
        } else if(type == 5){
            prefix = `themes/${settings.theme}/images/users/`;
            if(size != ''){
                suffix = `-${size}.jpeg`;
            }
        } else {
            var folder = type == 4 ? 'users' : 'posts';
            prefix = `uploads/${folder}/`;
            if(size != ''){
                suffix = `-${size}.jpeg`;
            }
        }
    }
    return Url(prefix+file+suffix);
}

async function Words(language = 'en', paginate = false, page = 1, keyword = ''){
    var data = {};
    if(paginate == true){
        query = '';
        if(keyword != ''){
            query = ` WHERE word LIKE '%${keyword}%' OR ${language} LIKE '%${keyword}%'`;
        }
        /*
        $data['sql'] = $dba->query("SELECT * FROM word{$query} LIMIT ? OFFSET ?", 10, $page)->fetchAll();
        $data['total_pages'] = $dba->totalPages;
        */
    } else {
        await query(`SELECT word, ${language} FROM ${T.WORD}`).then(function(res){
            forEach(res, function(value){
                data[value.word] = value[language];
            })
        }).catch(function(err){
            console.log(err);
        });
    }
    return data;
}

async function SetNotify(socket, data = {}){
    var retrn = !1,
        typet = data.type;

    if(['preact', 'creact', 'rreact'].indexOf(data.type) != -1){
        typet = "react";
    }

                          
    if(!IsOwner(socket, data.user_id)){
        await Data(socket, data.user_id).then(async function(res){
            if(res.notifications.indexOf(typet) != -1){
                var type = `n_${data.type}`;
                await query(`SELECT COUNT(*) as count FROM ${T.NOTIFICATION} WHERE user_id = ? AND notified_id = ? AND type = ?`, [data.user_id, data.notified_id, type]).then(async function(res){
                    if (res[0].count == 0) {
                        console.log([data.user_id, data.notified_id, type, Time()])
                        await query(`INSERT INTO ${T.NOTIFICATION} (user_id, notified_id, type, created_at) VALUES (?, ?, ?, ?)`, [data.user_id, data.notified_id, type, Time()]).then(function(res){
                            retrn = !0;
                        }).catch(function(err){
                            retrn = !1;
                        })
                    }
                }).catch(function(err){
                    console.log(err);
                });
            }
        }).catch(function(err){
            console.log(err);
        });
    }
    return retrn;
}

function IsOwner(socket, user_id) {
    if (global.TEMP[socket.id].loggedin == true) {
        if (getCurrentUser(socket.id).user_id == user_id) {
            return !0;
        }
    }
    return !1;
}

// Tal vez no lo utilice
function Language(socket, cookies){
    var language = settings.language;

    if (global.TEMP[socket.id].loggedin == true) {
        user = Data(socket, null, 4);
        if (in_array(user.language, $TEMP['#languages'])) {
            language = user.language;
        }
    } else {
        if(cookies.language != ''){
            language = Filter(cookies.language);	
        }
    }
    return language;
}

function RandomKey(minlength = 12, maxlength = 20, number = !0) {
    var length = Math.floor(Math.random() * (maxlength - minlength)) + minlength;
    number = number == true ? "1234567890" : "";
    return _.shuffle(`ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz${number}`).join('').substr(0, length);
}

function Filter(input = ''){
    if(input != ''){
        input = MysqlRealEscapeString(input);
        input = htmlEntities(input);
        input = input.replace('/(\r\n|\n\r|\r|\n)/gm', " <br>");
        input = stripSlashes(input);
    }
    return input;
}

function MysqlRealEscapeString(str){
    return str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function(char){
        switch (char) {
            case "\0":
                return "\\0";
            case "\x08":
                return "\\b";
            case "\x09":
                return "\\t";
            case "\x1a":
                return "\\z";
            case "\n":
                return "\\n";
            case "\r":
                return "\\r";
            case "\"":
            case "'":
            case "\\":
            case "%":
                return "\\"+char; // prepends a backslash to backslash, percent,
                                  // and double/single quotes
        }
    });
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function stripSlashes(str) {
    return (str + '').replace(/\\(.?)/g, function(s,n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}

function Time(){
    return unixTime(new Date());
}

function Url(params = '') {
    return `${info.site_url}/${params}`;
}

function ProfileUrl(username){
    return Url(`${R.r_user}/${username}`);
}

function emitChangesAll(emit, data) {
    global.TEMP.io.sockets.emit(emit, data);
}

function emitChangesJustMe(socket_id, emit, data) {
    global.TEMP.io.to(socket_id).emit(emit, data);
}

function emitChangesOffme(socket, emit, data) {
    socket.broadcast.emit(emit, data);
}

module.exports = {
    Init,
    setSocket,
    getSockets,
    pullSocket,
    BlockedUsers,
    Url,
    ProfileUrl,
    Data,
    DateFormat,
    DateString,
    GetFile,
    Words,
    Language,
    Filter,
    htmlEntities,
    stripSlashes,
    Time,
    SetNotify,
    IsOwner,
    MysqlRealEscapeString,
    getCurrentUser,
    emitChangesAll,
    emitChangesOffme,
    emitChangesJustMe
};