console.log('Javascript Start, attempt to acces mysql resources');

import mysql from '../node_modules/mysql';

console.log('After attempting to acces mysql resources, attempt to connect to eden db');
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: null,
    database: 'eden'
});

console.log('After attempting to connect to eden db, log in console error or if connection was successful.')
connection.connect((err) => {
    if (err) throw err;
    console.log('Connected to database through Javascript!');
})