var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var passportLocalMongoose = require('passport-local-mongoose');

var Account = new Schema({
    username: String,
    e_mail: String,
    password: String,
    photo: {
     fieldname: String, 
     originalname: String,
     encoding: String,
     mimetype: String,
     destination: String,
     filename: String,
     path: String,
     size: Number
    }
    
});

Account.plugin(passportLocalMongoose);

module.exports = mongoose.model('Account', Account);