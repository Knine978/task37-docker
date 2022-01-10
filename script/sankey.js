var mysql = require('mysql');
var express = require('express');
var cors = require('cors');

const url = require('url');

var app = express();
app.use(express.json({limit: '10mb'}));
app.use(express.urlencoded({limit: '10mb', extended: true}));
app.use(cors());


app.get("/get_data", function (req, res) {

    query(
        "SELECT origin.VC_position as 'from', exchange.to_vcposition as 'to', exchange.Kilograms as 'flow' from exchange inner join company origin on (origin.id = exchange.from_id) WHERE exchange.circular_entity_name LIKE \"%"+req.query.type+"%\";"
    ).then(result => {
        res.setHeader('Content-Type', 'application/json');
        res.send(JSON.stringify(result));
    });
});


function query(sql)
{
    return new Promise((resolve, reject) => {
        var con = mysql.createConnection({
            host: "db",
            user: "digiprime",
            password: "Horizon2020",
            database: "task37"
        });
        con.connect((err) => {
            if (err) throw err;
            con.query(sql, function (err, result) {
                if (err) reject(err);
                con.end();
                resolve(result);
            });
        });
    })
}

app.listen(9200, () => {
    console.log(`Listening at http://localhost:${9200}`)
});
