var connection = require('./mysql/DB');
var specific = require('./includes/specific');

var R = require('./includes/rutes');
var T = require('./includes/tables');

var util = require('util');
const query = util.promisify(connection.query).bind(connection);

module.exports = async function(socket){
    var TEMP = global.TEMP[socket.id],
        io = global.TEMP.io,
        user = TEMP.user,
        user_id = user.id,
        blocked_users = TEMP.blocked_users;

    const WORD = global.TEMP[socket.id].word,
          ERR = {
            S: 400,
            E: `*${WORD.oops_error_has_occurred}`
          };

    function emitChamgesMe(emit, data) {
        var sockets = specific.getSockets(user_id);
        io.to(sockets).emit(emit, data);
    }

    function emitChamgesBMe(emit, data) {
        var sockets = specific.getSockets(user_id, socket.id);
        io.to(sockets).emit(emit, data);
    }
    
    socket.on('setInsave', function(e, x){
        try {
            var post_id = specific.Filter(e.post_id);

            if(post_id != '' && !isNaN(post_id)){
                connection.query(`SELECT COUNT(*) as count FROM ${T.POST} WHERE id = ? AND user_id NOT IN (${blocked_users}) AND status = "approved"`, [post_id], function (error, result, field){
                    if (error) { 
                        return x(ERR);
                    }

                    if(result[0].count > 0){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.SAVED} WHERE user_id = ? AND post_id = ?`, [user_id, post_id], function (error, result, field){
                            if (error) { 
                                return x(ERR);
                            }
        
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.SAVED} WHERE user_id = ? AND post_id = ?`, [user_id, post_id], function (error, result, field){
                                    if (error) { 
                                        return x(ERR);
                                    }
                                    
                                    if(result.affectedRows > 0){
                                        var deliver = {
                                            S: 200,
                                            AC: 'delete',
                                            BT: '.btn_save[data-id='+post_id+']'
                                        };
                                        emitChamgesBMe('setOutsave', deliver);
                                        x(deliver);
                                    } else {
                                        x(ERR);
                                    }
                                })
                            } else {
                                connection.query(`INSERT INTO ${T.SAVED} (user_id, post_id, created_at) VALUES (?, ?, ?)`, [user_id, post_id, specific.Time()], function (error, result, field){
                                    if (error) { 
                                        return x(ERR);
                                    }
                                    
                                    if(result.insertId > 0){
                                        var deliver = {
                                            S: 200,
                                            AC: 'save',
                                            BT: '.btn_save[data-id='+post_id+']'
                                        };
                                        emitChamgesBMe('setOutsave', deliver);
                                        x(deliver);
                                    } else {
                                        x(ERR);
                                    }
                                })
                            }
        
                        })
                    }

                })  
            } else {
                x(ERR);
            }
        } catch {
            x(ERR)
        }
    });

    
    socket.on('setInpreaction', async function(e, x){
        var post_id = specific.Filter(e.post_id),
            type = specific.Filter(e.type);

        if(post_id != '' && !isNaN(post_id) && ['like', 'dislike'].indexOf(type) != -1){
			connection.query(`SELECT *, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND status = "approved"`, [post_id], function(error, result, field){
                if(error){
                    return x(ERR);
                }
                var post = result[0];
                if(post.count > 0){
                    if(type == 'like'){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [user_id, post_id], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                var likes = (post.likes-1);
        
                                connection.query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    if(result.affectedRows > 0){
                                        connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"`, [user_id, post_id], async function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [user_id, post_id], function(error, result, field){
                                                if(error){
                                                    return x(ERR);
                                                }
                                                var deliver = {
                                                    S: 200,
                                                    DB: `.btn_plike[data-id=${post.id}]`,
                                                    CR: likes
                                                };
                                                if(result.affectedRows > 0){
                                                    specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                                    x(deliver);
                                                } else {
                                                    x(ERR)
                                                }
                                            })
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                })
                            } else {
                                var deliver = {};
    
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [user_id, post_id]).then(async function(res){
                                    if(res[0].count > 0){
                                        var dislikes = (post.dislikes-1);
                                        await query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id]).then(async function(res){
                                            if(res.affectedRows > 0){
                                                await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"`, [user_id, post_id]).then(async function(res){
                                                    await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [user_id, post_id]).then(function(res){
                                                        if(res.affectedRows > 0){
                                                            deliver.DO = `.btn_pdislike[data-id=${post.id}]`;
                                                            deliver.CO = dislikes;
                                                        } else {
                                                            x(ERR)
                                                        }
                                                    }).catch(function(err){
                                                        console.log(err);
                                                    })
                                                }).catch(function(err){
                                                    console.log(err);
                                                })
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
    
                                var likes = (post.likes+1);
                                await query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id]).then(async function(res){
                                    if(res.affectedRows > 0){
                                        await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", "post", ?)`, [user_id, post_id, specific.Time()]).then(function(res){
                                            if(res.insertId > 0){
                                                deliver.S = 200;
                                                deliver.AB = `.btn_plike[data-id=${post.id}]`;
                                                deliver.CR = likes;
                                                specific.SetNotify(socket, {
                                                    user_id: post.user_id,
                                                    notified_id: res.insertId,
                                                    type: 'preact',
                                                });
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
                                specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                x(deliver);
                            }
                        })
                    } else {
    
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [user_id, post_id], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                var dislikes = (post.dislikes-1);
                        
                                connection.query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    if(result.affectedRows > 0){
                                        connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"`, [user_id, post_id], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [user_id, post_id], function(error, result, field){
                                                if(error){
                                                    return x(ERR);
                                                }
                                                var deliver = {
                                                    S: 200,
                                                    DB: `.btn_pdislike[data-id=${post.id}]`,
                                                    CR: dislikes
                                                };
                                                if(result.affectedRows > 0){
                                                    specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                                    x(deliver);
                                                } else {
                                                    x(ERR)
                                                }
                                            })
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                })
                            } else {
                                var deliver = {};
                        
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [user_id, post_id]).then(async function(res){
    
                                    if(res[0].count > 0){
                                        var likes = (post.likes-1);
                                        await query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id]).then(async function(res){
                                            if(res.affectedRows > 0){
                                                await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"`, [user_id, post_id]).then(async function(res){
                                                    await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [user_id, post_id]).then(function(res){
                                                        if(res.affectedRows > 0){
                                                            deliver.DO = `.btn_plike[data-id=${post.id}]`;
                                                            deliver.CO = likes;
                                                        } else {
                                                            x(ERR)
                                                        }
                                                    }).catch(function(err){
                                                        console.log(err);
                                                    })
                                                }).catch(function(err){
                                                    console.log(err);
                                                })
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
                        
                                var dislikes = (post.dislikes+1);
                                await query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id]).then(async function(res){
                                    if(res.affectedRows > 0){
                                        await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", "post", ?)`, [user_id, post_id, specific.Time()]).then(function(res){
                                            if(res.insertId > 0){
                                                deliver.S = 200;
                                                deliver.AB = `.btn_pdislike[data-id=${post.id}]`;
                                                deliver.CR = dislikes;
                                                specific.SetNotify(socket, {
                                                    user_id: post.user_id,
                                                    notified_id: res.insertId,
                                                    type: 'preact',
                                                });
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
                                specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                x(deliver);
                            }
                        })
                    }
                }
            });
		}
    })
    

    socket.on('setIncrection', async function(e, x){
        var comment_id = specific.Filter(e.comment_id),
            type = specific.Filter(e.type),
		    treact = specific.Filter(e.treact);
        
        if(comment_id != '' && !isNaN(comment_id) && ['like', 'dislike'].indexOf(type) != -1 && ['comment', 'reply'].indexOf(treact) != -1){

            var n_react = 'creact',
                t_query = T.COMMENTS;
			if(treact == 'reply'){
				n_react = 'rreact';
				t_query = T.REPLY;
			}

			var likes = 0,
                dislikes = 0,
                n_creact = "n_{$n_react}";
            
            await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "like" AND place = ?`, [comment_id, treact]).then(function(res){
                likes = res[0].count;
            }).catch(function(err){
                console.log(err);
            });
			await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "dislikes" AND place = ?`, [comment_id, treact]).then(function(res){
                dislikes = res[0].count;
            }).catch(function(err){
                console.log(err);
            });

            connection.query(`SELECT COUNT(*) as count FROM ${t_query} WHERE id = ?`, [comment_id], function(error, result, field){
                if(error){
                    return x(ERR);
                }
                if(result[0].count > 0){
                    if(type == 'like'){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [user_id, comment_id, treact], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?`, [user_id, comment_id, treact, n_creact], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [user_id, comment_id, treact], function(error, result, field){
                                        if(error){
                                            return x(ERR);
                                        }
                                        var deliver = {
                                            S: 200,
                                            DB: `.btn_clike[data-id=${comment_id}]`,
                                            CR: likes > 0 ? (likes-1) : 0
                                        };
                                        if(result.affectedRows > 0){
                                            specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                            x(deliver);
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            } else {
                                var deliver = {};
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [user_id, comment_id, treact]).then(async function(res){
                                    if(res[0].count > 0){
                                        await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?`, [user_id, comment_id, treact, n_creact]).then(async function(res){
                                            await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [user_id, comment_id, treact]).then(function(res){
                                                if(res.affectedRows > 0){
                                                    deliver.DO = `.btn_cdislike[data-id=${comment_id}]`;
                                                    deliver.CO = dislikes > 0 ? (dislikes-1) : 0;
                                                } else {
                                                    x(ERR)
                                                }
                                            }).catch(function(err){
                                                console.log(err);
                                            })
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })

                                await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", ?, ?)`, [user_id, comment_id, treact, specific.Time()]).then(function(res){
                                    if(res.insertId > 0){
                                        deliver.S = 200;
                                        deliver.AB = `.btn_clike[data-id=${comment_id}]`;
                                        deliver.CR = (likes+1)
                                        connection.query(`SELECT user_id FROM ${t_query} WHERE (SELECT reacted_id FROM ${T.REACTION} WHERE id = ?) = id`, [res.insertId], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            if(result.length > 0){
                                                specific.SetNotify(socket, {
                                                    user_id: result[0].user_id,
                                                    notified_id: res.insertId,
                                                    type: n_react,
                                                });
                                            }
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
    
                                specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                x(deliver);
                            }
                        })
                    } else {
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [user_id, comment_id, treact], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?`, [user_id, comment_id, treact, n_creact], async function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [user_id, comment_id, treact], function(error, result, field){
                                        if(error){
                                            return x(ERR);
                                        }
                                        var deliver = {
                                            S: 200,
                                            DB: `.btn_cdislike[data-id=${comment_id}]`,
                                            CR: dislikes > 0 ? (dislikes-1) : 0
                                        };
                                        if(result.affectedRows > 0){
                                            specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                            x(deliver);
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            } else {
                                var deliver = {};
                        
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [user_id, comment_id, treact]).then(async function(res){
                                    if(res[0].count > 0){
                                        await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?`, [user_id, comment_id, treact, n_creact]).then(async function(res){
                                            await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [user_id, comment_id, treact]).then(function(res){
                                                if(res.affectedRows > 0){
                                                    deliver.DO = `.btn_clike[data-id=${comment_id}]`;
                                                    deliver.CO = likes > 0 ? (likes-1) : 0;
                                                } else {
                                                    x(ERR)
                                                }
                                            }).catch(function(err){
                                                console.log(err);
                                            })
                                        }).catch(function(err){
                                            console.log(err);
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })

                                await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", ?, ?)`, [user_id, comment_id, treact, specific.Time()]).then(function(res){
                                    if(res.insertId > 0){
                                        deliver.S = 200;
                                        deliver.AB = `.btn_cdislike[data-id=${comment_id}]`;
                                        deliver.CR = (dislikes+1);
                                        connection.query(`SELECT user_id FROM ${t_query} WHERE (SELECT reacted_id FROM ${T.REACTION} WHERE id = ?) = id`, [res.insertId], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            if(result.length > 0){
                                                specific.SetNotify(socket, {
                                                    user_id: result[0].user_id,
                                                    notified_id: res.insertId,
                                                    type: n_react,
                                                });
                                            }
                                        })
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })

                                specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                x(deliver);
                            }
                        })
                    }
                }
            })

        }
    });

    socket.on('setIncdelete', function(e, x){
        var comment_id = specific.Filter(e.comment_id),
            type = specific.Filter(e.type);

		if(comment_id != '' && !isNaN(comment_id) && ['comment', 'reply'].indexOf(type) != -1){
			if(type == 'comment'){
                connection.query(`SELECT *, COUNT(*) as count FROM ${T.COMMENTS} WHERE id = ?`, [comment_id], async function(error, result, field){
                    if(error){
                        return x(ERR);
                    }

                    if(result[0].count > 0){
                        await query(`SELECT user_id FROM ${T.POST} WHERE id = ? AND status = "approved"`, [result[0].post_id]).then(function(res){
                            if(specific.IsOwner(socket, result[0].user_id) || specific.IsOwner(socket, res[0].user_id)){

                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE notified_id = ? AND type = "n_pcomment"`, [comment_id], function(error, result){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.COMMENTS} WHERE id = ?`, [comment_id], function(error, result){
                                        if(error){
                                            return x(ERR);
                                        }
                                        if(result.affectedRows > 0){
                                            x({
                                                S: 200,
                                                DL: `.content_comment[data-id=${comment_id}]`
                                            })
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            }
                        }).catch(function(err){
                            console.log(err)
                        })
                    }
                })
			} else {
                connection.query(`SELECT *, COUNT(*) as count FROM ${T.REPLY} WHERE id = ?`, [comment_id], async function(error, result, field){
                    if(error){
                        return x(ERR);
                    }

                    if(result[0].count > 0){
                        await query(`SELECT user_id FROM ${T.POST} WHERE (SELECT post_id FROM ${T.COMMENTS} WHERE id = ? AND status = "approved") = id`, [result[0].comment_id]).then(function(res){
                            if(specific.IsOwner(socket, result[0].user_id) || specific.IsOwner(socket, res[0].user_id)){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE notified_id = ? AND (type = "n_preply" OR type = "n_ureply")`, [comment_id], function(error, result){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.REPLY} WHERE id = ?`, [comment_id], function(error, result){
                                        if(error){
                                            return x(ERR);
                                        }
                                        console.log(result);
                                        if(result.affectedRows > 0){
                                            x({
                                                S: 200,
                                                DL: `.content_reply[data-id=${comment_id}]`
                                            })
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            }
                        }).catch(function(err){
                            console.log(err);
                        })
                    }
                })
			}
		}
    });

    socket.on('setIndelete', function(e, x){
        try {
            var post_id = specific.Filter(e.post_id);
            
            if(post_id != '' && !isNaN(post_id)){
                connection.query(`SELECT user_id, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND status <> "deleted"`, [post_id], function (error, result, field){
                    if (error) { 
                        return x(ERR);
                    };

                    if(result[0].count > 0 && specific.IsOwner(socket, result[0].user_id)){
                        connection.query(`UPDATE ${T.POST} SET status = "deleted", deleted_at = ? WHERE id = ?`, [specific.Time(), post_id], function (error, result, field){
                            if (error) { 
                                return x(ERR);
                            }
                            if(result.affectedRows > 0){
                                x({
                                    S: 200,
                                    LK: specific.Url(`${R.p_show_alert}=${R.p_deleted_post}`)
                                })
                            } else {
                                x(ERR)
                            }
                        });
                    } else {
                        x(ERR)
                    }
                })
            } else {
                x(ERR)
            }
        } finally {
            x(ERR)
        }
    })

    socket.on('setInreport', async function(e, x){
        var E = String;
        var error = !1;
        var reported_id = specific.Filter(e.reported_id);
        var type = specific.Filter(e.type);
        var place = specific.Filter(e.place);
        var description = specific.Filter(e.description);

        if(reported_id != '' && !isNaN(reported_id) && ['user', 'post', 'comment', 'reply'].indexOf(place) != -1){
            if(description.length > 500){
                error = !0;
            }
            if(place == 'user'){
                if(['r_spam', 'r_none', 'ru_hate', 'ru_picture', 'ru_copyright'].indexOf(type) == -1){
                    error = !0;
                }
                try {
                    var user = await query(`SELECT id, COUNT(*) as count FROM ${T.USER} WHERE id = ? AND status = "active"`, [reported_id]);
                    user = user[0];

                    if(user.count == 0){
                        error = !0;
                    }
                    if(specific.IsOwner(socket, user_id)){
                        error = !0;
                    }
                
                    if(error == false){
                        E = WORD.have_already_reported_user;
                    }

                } catch {
                    error = !0;
                }
            } else if(place == 'post'){
                if(['r_spam', 'r_none', 'rp_writing', 'rp_thumbnail', 'rp_copyright'].indexOf(type) == -1){
                    error = !0;
                }
                    
                try {
                    var post = await query(`SELECT user_id, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND user_id NOT IN (${blocked_users}) AND status = "approved"`, [reported_id]);
                    
                    post = post[0];

                    if(post.count == 0){
                        error = !0;
                    }
                
                    if(specific.IsOwner(socket, post.user_id)){
                        error = !0;
                    }

                    if(error == false){
                        E = WORD.have_already_reported_post;
                    }

                } catch {
                    error = !0;
                }
            } else if(['comment', 'reply'].indexOf(place) != -1){
                if(['r_spam', 'r_none', 'rc_offensive', 'rc_abusive', 'rc_disagree', 'rc_marketing'].indexOf(type) == -1){
                    error = !0;
                }
                var t_query = T.COMMENTS;
                if(place == 'reply'){
                    t_query = T.REPLY;
                }

                try {
                    var comment = await query(`SELECT *, COUNT(*) as count FROM ${t_query} WHERE id = ?`, [reported_id]);
                    comment = comment[0];

                    if(comment.count == 0){
                        error = true;
                    }
                
                    if(specific.IsOwner(socket, comment.user_id)){
                        error = true;
                    }
                
                    if(error == false){
                        E = WORD.have_already_reported_comment;
                    }
                } catch {
                    error = !0;
                }
            }

            if(error == false){
                try {
                    connection.query(`SELECT COUNT(*) as count FROM ${T.REPORT} WHERE user_id = ? AND reported_id = ? AND place = ?`, [user_id, reported_id, place], function(error, result, field){
                        if(error){
                            return x(ERR);
                        }

                        if(result[0].count == 0){
                            connection.query(`INSERT INTO ${T.REPORT} (user_id, reported_id, type, place, description, created_at) VALUES (?, ?, ?, ?, ?, ?)`, [user_id, reported_id, type, place, description, specific.Time()], function(error, result, field){
                                if (error) {
                                    return x(ERR);
                                }
                                x({
                                    S: 200
                                })
                            });
                        } else {
                            x({
                                S: 400,
                                E: `*${E}`
                            })
                        }
                    });
                } catch {
                    x(ERR)
                }
            } else {
                x(ERR)
            }
        }
    })

    /*
    socket.on('setIndelete', function(e){
        try {
            
        } catch (err) {
            console.log(err)
        }
    })
    */


}