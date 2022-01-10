var mysql = require('mysql');
var express = require('express');
var cors = require('cors');
var regione;
var nazione;
const url = require('url');

var app = express();
app.use(express.json({limit: '10mb'}));
app.use(express.urlencoded({limit: '10mb', extended: true}));
app.use(cors());


app.get("/get_nace", function (req, res) {

   query(
       // "SELECT code FROM nuts2021 WHERE region='lazio';"
        "SELECT nace_code,description FROM `nace` where 1;"
        ).then(result => {
            //res.setHeader('Content-Type', 'application/json');
            //var code = JSON.stringify(result);
            //res.send(JSON.stringfy(result));
        //res.setHeader('Content-Type', 'application/json');
        //res.send(JSON.stringify(result));
        const nace = JSON.parse(JSON.stringify(result));
        res.send(nace);
}); 
});


app.get("/get_nations", function (req, res) {

   query(
       // "SELECT code FROM nuts2021 WHERE region='lazio';"
        "SELECT nation FROM `nations` where 1"
        ).then(result => {
            //res.setHeader('Content-Type', 'application/json');
            //var code = JSON.stringify(result);
            //res.send(JSON.stringfy(result));
        //res.setHeader('Content-Type', 'application/json');
        //res.send(JSON.stringify(result));
        const nat = JSON.parse(JSON.stringify(result));
        res.send(nat);
}); 
});




app.get("/get_material", function (req, res) {

   query(
       // "SELECT code FROM nuts2021 WHERE region='lazio';"
    	"SELECT `circular_entity_name` FROM `exchange` WHERE 1;"
	).then(result => {
            //res.setHeader('Content-Type', 'application/json');
            //var code = JSON.stringify(result);
	    //res.send(JSON.stringfy(result));
	//res.setHeader('Content-Type', 'application/json');
        //res.send(JSON.stringify(result));
	const material = JSON.parse(JSON.stringify(result));
	res.send(material);
}); 
});


app.get("/get_regions", function (req, res) {

   query(
        "SELECT code FROM nations WHERE nation LIKE \"%"+req.query.nation+"%\";"
        ).then(result => {
        const nation = JSON.parse(JSON.stringify(result));
        nazione = nation[0].code;
//	console.log("nazione: "+nazione);
//      res.send(region[0].code);
});

  query(
        "SELECT region FROM `regions` WHERE code LIKE \"%"+nazione+"%\";"
        ).then(result => {
        res.setHeader('Content-Type', 'application/json');
        regioni = JSON.parse(JSON.stringify(result));
	
        console.log("nazione :"+nazione);
        console.log("regioni: "+regioni);
        res.send(regioni);


});
});



app.get("/get_sankey", function (req, res) {

   query(
        "SELECT code FROM nuts2021 WHERE region LIKE \"%"+req.query.region+"%\";"
        ).then(result => {
        //res.setHeader('Content-Type', 'application/json');
        const region = JSON.parse(JSON.stringify(result));
	//console.log("region: "+region[0].code);
	regione = region[0].code;
//	res.send(region[0].code);
});

  query(
	"SELECT nuts_code FROM `nuts` WHERE description LIKE \"%"+req.query.nation+"%\";"
	).then(result => {
        //res.setHeader('Content-Type', 'application/json');
        const nation = JSON.parse(JSON.stringify(result));
	nazione = nation[0].nuts_code;
        console.log("nation :"+nazione);
	console.log("region: "+regione);
	//res.send(nation);


}); 
    query(
        "SELECT origin.VC_position as 'from', exchange.to_vcposition as 'to', exchange.Kilograms as 'flow' from exchange inner join company origin on (origin.id = exchange.from_id AND region_id LIKE \"%"+regione+"%\") WHERE exchange.circular_entity_name LIKE \"%"+req.query.material+"%\";"
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

app.listen(9300, () => {
regione = null;
nazione = null;
    console.log(`Listening at http://localhost:${9300}`)
});
