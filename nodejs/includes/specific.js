var info = require('../info');
var connection = require('../mysql/DB');
var unixTime = require('unix-time');
var R = require('./rutes');
var T = require('./tables');
var entities = require('../utils/entities');
var url_regex = require('../utils/url_regex');
var async = require('async');
var forEach = require('async-foreach').forEach;
var _ = require('lodash');
var cookie = require('cookie');
var SETTINGS = Settings();
var util = require('util');
var fs = require('fs');
var iconv = require('iconv-lite');


var Sha1 = require('sha1'),
    Md5 = require('md5'),
    Download = require('image-downloader'),
    Sharp = require('sharp'),
    path = require('path');

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
                    await query(`SELECT user_id FROM ${T.SESSION} WHERE token = ? LIMIT 1`, [cookies._LOGIN_TOKEN]).then(async function(res){
                        if (res.length > 0) {
                            var user_id = res[0].user_id,
                                socket_id = socket.id;
                                
                            setSocket(socket_id, user_id);
                            validUsers[cookies._LOGIN_TOKEN] = user_id;
                            await BlockedUsers(user_id).then(function(res) {
                                blocked_users = res;
                            });
                            data = {
                                loggedin: !0,
                                user_id: user_id,
                                blocked_users: blocked_users
                            };
                        }
                    }).catch(function(err){
                        console.log(err)
                    });
                }
            }
        } catch (err) {
            console.log(err);
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


function Admin(socket) {
    var TEMP = global.TEMP[socket.id];
    return TEMP.loggedin === !1 ? !1 : TEMP.user.role == 'admin' ? !0 : !1;
}

function Moderator(socket) {
    var TEMP = global.TEMP[socket.id],
        role = TEMP.user.role;
    return TEMP.loggedin === !1 ? !1 : role == 'moderator' || role == 'admin' ? !0 : !1;
}

function Publisher(socket) {
    var TEMP = global.TEMP[socket.id],
        role = TEMP.user.role;
    return TEMP.loggedin === !1 ? !1 : role == 'publisher' || role == 'moderator' || role == 'admin' ? !0 : !1;
}

function Viewer(socket) {
    var TEMP = global.TEMP[socket.id],
        role = TEMP.user.role;
    return TEMP.loggedin === !1 ? !1 : role == 'viewer' ? !0 : !1;
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
    } catch (err) {
        console.log(err);
    }
    return data;
}

function BlockedUsers(user_id){
    var data = [];
    return new Promise(function(resolve, reject) {
        try {
            if (SETTINGS['blocked_users'] == 'on') {
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

        if(user.notifications != null){
            user.notifications = JSON.parse(user.notifications);
        } else {
            user.notifications = [
                'followers',
                'post',
                'followed',
                'collab',
                'react',
                'pcomment',
                'preply',
                'ucomment',
                'ureply'
            ];
        }
        if(user.shows != null){
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
        prefix = `themes/${SETTINGS.theme}/`;
    } else {
        if(type == 3) {
            prefix = 'uploads/entries/';
        } else if(type == 5){
            prefix = `themes/${SETTINGS.theme}/images/users/`;
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

    await IsOwner(socket, data.user_id).then(async function(res){
        if(!res){
            await Data(socket, data.user_id).then(async function(res){
                if(res.notifications.indexOf(typet) !== -1){
                    var type = `n_${data.type}`;
                    await query(`SELECT COUNT(*) as count FROM ${T.NOTIFICATION} WHERE user_id = ? AND notified_id = ? AND type = ?`, [data.user_id, data.notified_id, type]).then(async function(res){
                        if (res[0].count == 0) {
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
    }).catch(function(err){
        retrn = !1;
    })

    return retrn;
}

async function Followers(socket, user_id){
    var number = 0;
    await query(`SELECT COUNT(*) as count FROM ${T.FOLLOWER} WHERE profile_id = ?`, user_id).then(function(res){
        number = res[0].count;
    }).catch(function(error){
        console.log(error);
    });

    var followers = NumberShorten(number);
    var text = `${followers} ${global.TEMP[socket.id].word.follower}`;
    if(number > 1){
        text = `${followers} ${global.TEMP[socket.id].word.followers}`;
    }

    return {number, text};
}

function NumberShorten(n, precision = 1) {
    // 0.9qi+
    var n_format = NumberFormat(n / 1000000000000000000, precision),
        suffix = 'Qi';
    if(n < 999) {
        // 0 - 900
        n_format = NumberFormat(n, precision);
        suffix = '';
    } else if(n < 999999) {
        // 0.9k-850k
        n_format = NumberFormat(n / 1000, precision);
        suffix = 'K';
    } else if(n < 999999999) {
        // 0.9m-850m
        n_format = NumberFormat(n / 1000000, precision);
        suffix = 'M';
    } else if(n < 999999999999) {
        // 0.9b-850b
        n_format = NumberFormat(n / 1000000000, precision);
        suffix = 'B';
    } else if(n < 999999999999999) {
        // 0.9t+
        n_format = NumberFormat(n / 1000000000000, precision);
        suffix = 'T';
    } else if(n < 999999999999999999) {
        // 0.9qa+
        n_format = NumberFormat(n / 1000000000000000, precision);
        suffix = 'Qa';
    }

      // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
      // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if(precision > 0) {
        var dotzero = `.${'0'.repeat(precision)}`;
        n_format = n_format.replace(dotzero, '');
    }

    return `${n_format} ${suffix}`;
}


function NumberFormat(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function SizeFormat(bytes, precision = 2) {
    var unit = ["B", "KB", "MB", "GB"],
        exp = Math.floor(Math.log(bytes) / Math.log(1024)) | 0;

    return `${(bytes / Math.pow(1024, exp)).toFixed(precision)} ${unit[exp]}`;
}

async function IsOwner(socket, user_id) {
    var retrn = !1;
    if (global.TEMP[socket.id].loggedin == true) {
        await getCurrentUser(socket.id).then(function(res){
            if (res.user_id == user_id) {
                retrn = !0;
            }
        }).catch(function(err){
            retrn = !1;
        })

        
    }
    return retrn;
}
    
function CreateDirImage(folder){
    var time = new Date(),
        month = time.toLocaleString("en-US", {month: '2-digit'}),
        year = time.getFullYear(),
        folder_first = `${global.TEMP.path}/uploads/${folder}/${year}-${month}`,
        dates = `${year}-${month}/${month}`,
        folder_last = `${global.TEMP.path}/uploads/${folder}/${dates}`;

    if (!fs.existsSync(folder_first)) {
        fs.mkdir(folder_first, {recursive: true});
    }

    if (!fs.existsSync(folder_last)) {
        fs.mkdir(folder_last, {recursive: true});
    }
    return {
        full: folder_last,
        dates: dates
    };
}

async function UploadThumbnail(data = {}) {
    if(Object.keys(data).length == 0 || data.media.toLowerCase().indexOf('.gif') != -1){
        return {
            return: !1
        };
    }

    var dir_image = CreateDirImage(data.folder),
        getImage = data.media,
        retrn = {
            return: !1
        };

    if(data.folder == 'posts'){
        var image_one = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_one = `${dir_image.full}/${image_one}`,
            image_two = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_two = `${dir_image.full}/${image_two}`,
            filename_one_b = `${file_one}-b.jpeg`,
            filename_one_s = `${file_one}-s.jpeg`,
            filename_two_b = `${file_two}-b.jpeg`,
            filename_two_s = `${file_two}-s.jpeg`,
            url_dates = `${dir_image.dates}/${image_two}`;

        if (getImage != ''){
            await Download.image({
                url: getImage,
                dest: filename_one_b
            }).then(async function(filename){
                await Sharp(filename_one_b).resize(780, 440).toFile(filename_two_b).then(function(res) {
                    fs.unlink(filename_one_b, function(err) {
                        if(err && err.code == 'ENOENT') {
                            // file doens't exist
                            console.info("File doesn't exist, won't remove it.");
                        } else if (err) {
                            // other errors, e.g. maybe we don't have enough permission
                            console.error("Error occurred while trying to remove file");
                        } else {
                            console.info(`removed`);
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                console.log(err);
            })

            await Download.image({
                url: getImage,
                dest: filename_one_s
            }).then(async function(filename){
                await Sharp(filename_one_s).resize(400, 266).toFile(filename_two_s).then(function(res) {
                    fs.unlink(filename_one_s, function(err) {
                        if(err && err.code == 'ENOENT') {
                            // file doens't exist
                            console.info("File doesn't exist, won't remove it.");
                        } else if (err) {
                            // other errors, e.g. maybe we don't have enough permission
                            console.error("Error occurred while trying to remove file");
                        } else {
                            console.info(`removed`);
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                console.log(err);
            })

            if (fs.existsSync(filename_two_b) && fs.existsSync(filename_two_s)){
                retrn = {
                    return: !0,
                    image: url_dates,
                    image_ext: `${url_dates}.jpeg`
                };
            }
        }
    } else {
        var image = `${data.post_id}-${data.eorder}-${Md5(`${Time()}${RandomKey()}`)}`,
            filename = `${dir_image.full}/${image}.jpeg`;

        await Download.image({
            url: getImage,
            dest: filename
        }).then(function(filename){
            var url_dates = `${dir_image.dates}/${image}`;
            retrn = {
                return: !0,
                image: url_dates,
                image_ext: `${url_dates}.jpeg`
            };
        }).catch(function(err){
            console.log(err);
        });
    }
    return retrn;
}

async function UploadImage(data = {}){
    var dir_image = CreateDirImage(data.folder),
        retrn = {
            return: !1
        };

    if (Object.keys(data).length == 0 || ['.jpeg','.jpg','.png'].indexOf(path.extname(data.name)) === -1 || ['image/jpeg', 'image/png'].indexOf(data.type) === -1 || data.size > SETTINGS.file_size_limit) {
        return retrn;
    }

    if(data.folder == 'posts'){
        var image_one = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            image_two = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_one = `${dir_image.full}/${image_one}`,
            file_two = `${dir_image.full}/${image_two}`,
            filename_one_b = `${file_one}-b.jpeg`,
            filename_one_s = `${file_one}-s.jpeg`,
            filename_two_b = `${file_two}-b.jpeg`,
            filename_two_s = `${file_two}-s.jpeg`,
            url_dates = `${dir_image.dates}/${image_two}`;
        
        await fs.promises.readFile(data.tmp_name).then(async function(res){
            await fs.promises.writeFile(filename_one_b, res).then(async function(res){
                await Sharp(filename_one_b).resize(780, 440).toFile(filename_two_b).then(function(res) {
                    fs.copyFile(filename_one_b, filename_one_s, async function(error){
                        if (error) {
                            console.log(error)
                        }
                        fs.unlink(filename_one_b, function(err) {
                            if(err && err.code == 'ENOENT') {
                                // file doens't exist
                                console.info("File doesn't exist, won't remove it.");
                            } else if (err) {
                                // other errors, e.g. maybe we don't have enough permission
                                console.error("Error occurred while trying to remove file");
                            } else {
                                console.info(`removed`);
                            }
                        });
                        await Sharp(filename_one_s).resize(400, 266).toFile(filename_two_s).then(function(res) {
                            fs.unlink(filename_one_s, function(err) {
                                if(err && err.code == 'ENOENT') {
                                    // file doens't exist
                                    console.info("File doesn't exist, won't remove it.");
                                } else if (err) {
                                    // other errors, e.g. maybe we don't have enough permission
                                    console.error("Error occurred while trying to remove file");
                                } else {
                                    console.info(`removed`);
                                }
                            });
                        }).catch(function(err) {
                            console.log(err);
                        });
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                return retrn;
            })
        }).catch(function(err){
            return retrn;
        });

        retrn = {
            return: !0,
            image: url_dates,
            image_ext: `${url_dates}.jpeg`
        };
    } else {
        var image = `${data.post_id}-${data.eorder}-${Md5(`${Time()}${RandomKey()}`)}`,
            filename = `${dir_image.full}/${image}.jpeg`,
            url_dates = `${dir_image.dates}/${image}`;
        
        await fs.promises.readFile(data.tmp_name).then(async function(res){
            await fs.promises.writeFile(filename, res).then(function(res){
                retrn = {
                    return: true,
                    image: url_dates,
                    image_ext: `${url_dates}.jpeg`
                }
            }).catch(function(err){
                return retrn;
            });
        }).catch(function(err){
            return retrn;
        });
    }
    return retrn;
}

function CreateSlug(str, char = "-", tf = "lowercase", max = 120){
    str = iconv.encode(str, "ASCII");
    str = iconv.decode(str, 'utf8'); // transliterate

    str = _.replace(str, "'", ""); // remove “'” generated by iconv
    str = str.substr(0, max);
    str = str.replace("~[^a-z0-9]+~ui", char); // replace unwanted by single “-”
    str = str.replace(/\?+/gi, ''); // replace “?”
    str = str.replace(/\s+/gi, char); // trim “-”

    if(tf == "lowercase"){
        str = str.toLowerCase(); // lowercase
    } else if($tf == "uppercase"){
        str = str.toUpperCase();
    }
    return str;
}

function MaketFrame(url, attrs = [], defult = !0, is_amp = !1){
    if(!Array.isArray(attrs)){
        attrs = htmlEntityDecode(attrs);
        attrs = JSON.parse(attrs);
    }

    var defaults = {
        width: '100%',
        height: '450',
        frameborder: 0
    };

    if(is_amp){
        defaults = {
            width: '200',
            height: '100',
            layout: 'responsive',
            sandbox: 'allow-scripts allow-same-origin',
            frameborder: 0
        };
    }

    var attributes = '';

    forEach(attrs, function(item, index, arr){
        if(item.name == 'attribute'){
            if(!item.value.match(/[^A-Za-z0-9\-\_]+/)){
                attributes += ` ${item.value}`;
                if(arr[index+1].name == 'value' && arr[index+1].value.match(/[^\"]+/)){
                    attributes += `="${arr[index+1].value}"`;
                }
                if(Object.values(defaults).indexOf(item.value) !== -1){
                    delete defaults[item.value];
                }
            } else {
                if(defult){
                    attrs.splice(index, 1)[0];
                    attrs.splice(index+1, 1)[0];
                }
            }
        }
    })

    if(Object(defaults).length > 0 && defult){
        forEach(defaults, function(item, index, arr){
            attributes += ` ${index}="${item}"`;
        })
    }
    
    if(ValidateUrl(url)){
        url = url.replace('/([h|H][t|T]{2}[p|P][s|S]?|[r|R][t|T][s|S][p|P]):\/\//', '//');
    }

    var html = `<iframe src="${url}"${attributes}></iframe>`
    if(is_amp){
        html = `<amp-iframe src="${url}"${attributes}></amp-iframe>`;
    }

    return {
        attrs: attrs,
        html: html
    };
}

// Tal vez no lo utilice
function Language(socket, cookies){
    var language = SETTINGS.language;

    if (global.TEMP[socket.id].loggedin == true) {
        var user = Data(socket, null, 4);
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

function Rand(min, max){
    return Math.floor(Math.random() * (max - min + 1) + min);
}


function RandomKey(minlength = 12, maxlength = 20, number = !0) {
    var length = Math.floor(Rand(maxlength, minlength));
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
    return !isNaN(str) ? str : str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function(char){
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

function htmlEntityDecode(str){
    return str.replace(/&([a-z0-9]+|#[0-9]{1,6}|#x[0-9a-f]{1,6});/gi, (t) => (entities[t] && entities[t].characters) || t);
}

function ValidateUrl(url, protocol = !1){
    //var match = url.match(url_regex);
    try {
        var UR = new URL(url);
        if(protocol){
            if(!UR.protocol){
                url = `http://${url}`;
            }
            return {
                return: !0,
                url: url
            };
        }
        return !0;
    } catch (error) {
        return !1;
    }
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

async function getCurrentUser(socket_id){
    var user = await sockets.find(sock => sock.socket_id === socket_id);
    return user;
}

function pullSocket(socket_id){
    const index = sockets.findIndex(sock=>sock.socket_id === socket_id);
    if(index !== -1){
        sockets.splice(index, 1)[0];
    }
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
    Admin,
    Moderator,
    Publisher,
    Viewer,
    Settings,
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
    htmlEntityDecode,
    ValidateUrl,
    stripSlashes,
    Time,
    SetNotify,
    IsOwner,
    CreateDirImage,
    UploadThumbnail,
    UploadImage,
    CreateSlug,
    MaketFrame,
    Followers,
    NumberShorten,
    SizeFormat,
    Rand,
    RandomKey,
    MysqlRealEscapeString,
    getCurrentUser,
    emitChangesAll,
    emitChangesOffme,
    emitChangesJustMe
};