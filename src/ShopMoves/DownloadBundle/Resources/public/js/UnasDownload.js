/**
 * Created by miskolczicsego on 2017.07.04..
 */

let soap = require('soap');
let xml2json = require('xml2json');
let xmlparser = require('xml2js').parseString;
let fs = require('fs');
let request = require('request');
let csvtojson = require('csvtojson');
let commandLineArgs = require('command-line-args');
const optionDefinitions = [
    {name: 'username', alias: 'u', type: String},
    {name: 'passwordcrypt', alias: 'p', type: String},
    {name: 'shopid', alias: 's', type: String},
    {name: 'authcode', alias: 'a', type: String},
    {name: 'downloadDestiny', alias: 'd', type: String},
    {name: 'migrationId', alias: 'i', type: String},
];
const args = commandLineArgs(optionDefinitions, {partial: true});
console.log(args);
let filePrefix = args.downloadDestiny + "/" + args.migrationId;
/**
 * wsetesztg
 * a8f5f167f44f4964e6c998dee827110c
 * 63724
 * e9fc1e4293
 *
 * wsetesztgfreemail
 * a8f5f167f44f4964e6c998dee827110c
 * 97690
 * a6b3932dd4
 */
let url = 'https://api.unas.eu/shop/?wsdl';
let auth = '<?xml version="1.0" encoding="UTF-8" ?>' +
    '<Auth>' +
    '<Username>' + args.username + '</Username>' +
    '<PasswordCrypt>' + args.passwordcrypt + '</PasswordCrypt>' +
    '<ShopId>' + args.shopid + '</ShopId>' +
    '<AuthCode>' + args.authcode + '</AuthCode>' +
    '</Auth>';
let soapArgsCustomer = {
    Auth: auth,
    Params:{}
}

let soapArgs = {
    Auth: auth,
    Params: [
        {
            "param": {
                "attributes": {
                    "name": "ContentType"
                },
                $value: "full"
            }
        }
    ]

};

console.log(soapArgs);
prettyJson = function (jsonString) {
    let object = JSON.parse(jsonString);
    return JSON.stringify(object, null, 2);
};
saveXml = function (xml, type) {
    fs.writeFile(filePrefix + type +".json", prettyJson(xml2json.toJson(xml)));
    console.log("finish customer");
};
downloadCsv = function (urlString) {
    request(urlString, function (err, response, body) {
        fs.writeFile(filePrefix + ".csv", body);
        console.log("finish product")
    });
};
soap.createClient(url, function (err, client) {
    console.log("start customer");
    client.getCustomer(soapArgsCustomer, function (err, result) {
        saveXml(result.getCustomerReturn.$value, "customer");
    });
    console.log("start product");
    client.getProduct(soapArgs, function (err, result) {
        saveXml(result.getProductReturn.$value, "product");
    });
});
