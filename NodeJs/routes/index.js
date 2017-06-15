var express = require('express');
var passport = require('passport');
// var upload = require('multer');
var multer = require('multer')
    // var upload = multer({ dest: 'public/images/uploads' })
var Account = require('../models/account');
var router = express.Router();
var app = express()

var storage = multer.diskStorage({
    destination: function(req, file, cb) {
        cb(null, 'uploads')
    },
    filename: function(req, file, cb) {
        cb(null, file.originalname)
    },
    originalname: function(req, file, cb) {
        cb(null, file.filename)
    }
})

var upload = multer({ storage: storage })

router.get('/profile', function(req, res) {
    res.render('profile', { user: req.user });
    console.log(req.user)

});


router.get('/search/:query', function(req, res){
    console.log(req.query)
})

router.get('/', function(req, res) {
  Account.find(function(err, users) {
    console.log(users)
  if (err) return console.error(err);
  res.render('index', {users: users, user: req.user})
});

});


// router.get('/user/:name', function(req, res) {
//   Account.findOne({'username': req.params.name}, function(err, person) {
//     if (err) return console.log(err);
//     console.log(person)
//     res.render('user_profile', {user: person})
//     // console.log(person.username)
//   })
// })

router.get('/user/:name', function(req, res) {
  Account.findOne({username: req.params.name},function(err, person) {
    if (err) return console.log(err);
    console.log(person)
    res.render('user_profile', {person: person, user: req.user})
    // console.log(person.username)
  })
})

router.post('/update', upload.single('photo'), function(req, res, next) {
    // Account.findOneAndUpdate()
    // res.redirect('/')
    // console.log(req.user._id)
    console.log(req.file)
    Account.findOneAndUpdate({ _id: req.user._id }, {
        $set: { photo: req.file }
    }, function(err, data) {
        if (err) {
            console.log("Ошибка")
        }
        console.log(data)
    })
    res.redirect('/profile')
})

router.post('/register', function(req, res) {
    Account.register(new Account({ username: req.body.username, e_mail: req.body.e_mail,"photo.fieldname": "", "photo.originalname": "default-user-image.png", "photo.encoding":"","photo.mimetype":"","photo.destination":"","photo.filename":"", "photo.path": "uploads/default-user-image.png","photo.size":""}), req.body.password, function(err, account) {
        if (err) {
            return res.render('register', { error: err.message });
        }
        passport.authenticate('local')(req, res, function() {
            req.session.save(function(err) {
                if (err) {
                    return next(err);
                }
                res.redirect('/');
            });
        });
    });
});

router.get('/login', function(req, res) {
    res.render('login', { user: req.user });
});
router.get('/register', function(req, res) {
    req.logout();
    res.render('register', { user: req.user });
});

router.post('/login', passport.authenticate('local'), function(req, res) {
    res.redirect('/profile');
});

router.get('/logout', function(req, res) {
    req.logout();
    res.redirect('/login');
});

// router.get('/ping', function(req, res){
//     res.status(200).send("pong!");
// });

module.exports = router;
