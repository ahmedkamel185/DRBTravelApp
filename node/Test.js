// use strict;
var path = require('path');
var fs = require('fs');
var os = require('os');
var express = require('express');
var app = express();
var Busboy = require('busboy');
var fileUpload = require('express-fileupload')
var bodyParser = require('body-parser')
app.use(fileUpload());
app.use(bodyParser.urlencoded({ extended: false }))

// parse application/json
app.use(bodyParser.json())

function upload(_file, _path){
    return new Promise(
        (resolve, reject) => {
            var name = new Date().getTime()
                +"_resource_"+""+Math.floor(Math.random()*1000)+"."
                +_file.name.substring(_file.name.indexOf('.')+1);
            _file.mv(path.join(_path, name ), function (err) {
                if(err) reject(error);
                resolve(name);
            })
        }
    )
}

app.get('/', function (req, res) {
    res.send('<html><head></head><body>\
               <form method="POST" enctype="multipart/form-data">\
                <input type="text" name="textfield"><br />\
                <input type="file" name="filefield"><br />\
                <input type="submit">\
              </form>\
            </body></html>');
    res.end();
});


// accept POST request on the homepage
app.post('/',   function (req, res) {
    console.log('hi')

    console.log(req.body)
    var sampleFile = req.files.filefield;
    // console.log( sampleFile.mimetype)
    sampleFile.mv(path.join(__dirname,"test"+sampleFile.name), function () {
        console.log("upload")
    })
    upload(sampleFile,__dirname ).then((name2)=>res.end(name2))

});

var server = app.listen(8500, function () {

    var host = server.address().address
    var port = server.address().port
    console.log(server.address())
    console.log('Example app listening at http://%s:%s', host, port)

});