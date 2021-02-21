/*©agpl*************************************************************************
*                                                                              *
* Napkin Visual – Visualisation platform for the Napkin platform               *
* Copyright (C) 2020  Napkin AS                                                *
*                                                                              *
* This program is free software: you can redistribute it and/or modify         *
* it under the terms of the GNU Affero General Public License as published by  *
* the Free Software Foundation, either version 3 of the License, or            *
* (at your option) any later version.                                          *
*                                                                              *
* This program is distributed in the hope that it will be useful,              *
* but WITHOUT ANY WARRANTY; without even the implied warranty of               *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                 *
* GNU Affero General Public License for more details.                          *
*                                                                              *
* You should have received a copy of the GNU Affero General Public License     *
* along with this program. If not, see <http://www.gnu.org/licenses/>.         *
*                                                                              *
*****************************************************************************©*/

"use strict";
//let sqlite3 = require('sqlite3').verbose();
//let sqlite = require('sqlite');
let datafire = require('datafire');
let project = datafire.Project.main();
let http = require('@datafire/http').create();
let twitter = require('@datafire/twitter').create(project.accounts.twitter);


module.exports = new datafire.Action({
	description: 'Twitter',
	handler: async (input, context) => {
		//let rate = await twitter.application.rate_limit_status({ "resources": "tweets" }, context);
		//if(rate.resources.tweets['/tweets/search/recent'].remaining <= 0) return;


		let res = [];

		//let r = await twitter.trends.available(null, context);
		let trends = await twitter.trends.place({ "id": "1" }, context);
		trends = trends[0].trends.sort( (a, b) => b.tweet_volume - a.tweet_volume ).slice(0, 6).map( a => decodeURIComponent(a.query.replace("+", "%20")/*.replace(/\%20/ig, "")*/) );

		/*let query = `${trends[0]}`; //has:geo
		for(let t of trends.slice(1)) { query += ` OR ${t}`; }


		let r = await http.get({
			"url": "https://api.twitter.com/2/tweets/search/recent",
			"query": {
				"max_results": "100",
				"expansions": "author_id,geo.place_id",
				"place.fields": "geo",
				"tweet.fields": "author_id,conversation_id,created_at,geo,public_metrics,source",
				//"user.fields": "location,username",
				"query": query
			},
			"headers": {
				"Authorization": "Bearer AAAAAAAAAAAAAAAAAAAAALC7MgEAAAAA%2BihsaX94SfTfwyCYFgtJn0H6UFE%3DHBECFVhMJlaoGYBlIO7UBkluGn13pwadYquEfpe0Ig24Tl9IwI"
			}
		}, context);
		r = JSON.parse(r.body);


		if(!r.includes) return null;

		let places = !!r.includes.places,
			users = !!r.includes.users;

		if(places) {
			for(let i = 0; i < r.includes.places.length; i++) {
				let bbox = r.includes.places[i].geo.bbox;
				let x = (bbox[1] + bbox[3]) / 2,
					y = (bbox[0] + bbox[2]) / 2;

				r.includes.places[i].geo.center = [x, y];
			}
		}*/


		/*let db = await sqlite.open({
			filename: "./actions/locations.db",
			driver: sqlite3.Database
		});*/

		//for(let t of r.data) {
		for(let i = 0; i < 100; i++) {
			/*let id = t.id,
				conversation_id = t.conversation_id,
				author_id = t.author_id,
				source = t.source,
				text = t.text,
				retweets = t.public_metrics.retweet_count,
				replies = t.public_metrics.reply_count,
				likes = t.public_metrics.like_count,
				quotes = t.public_metrics.quote_count,
				timestamp = t.created_at,
				count = 0;
			let lat = null, lng = null;*/


			/*let geoFound = false;
			if(places && t.geo) {
				for(let p of r.includes.places) {
					if(p.id == t.geo.place_id) {
						lat = p.geo.center[0];
						lng = p.geo.center[1];
						geoFound = true;
						break;
					}
				}
			}
			if(!lat || !lng) {}*/


			/*let username = "";
			if(users) {
				for(let u of r.includes.users) {
					if(u.id == author_id) {
						username = u.username;

						if(!geoFound && u.location) {
							try {
								let sql = `SELECT lat, lng FROM locations WHERE name LIKE ?`;
								let row = await db.get(sql, [ `%${u.location}%` ]);

								if(row) {
									lat = parseFloat(row.lat);
									lng = parseFloat(row.lng);
								}
							} catch(err) { console.error(err.message); }
						}

						break;
					}
				}
			}

			let trend = "";
			for(let t of trends) {
				if(text.toLowerCase().indexOf(t.replace(/\"/ig, "").toLowerCase()) > -1) {
					trend = t;
					break;
				}
			}

			let link = `https://twitter.com/${username}/status/${conversation_id}`;

			let date = timestamp.split('T')[0],
				time = timestamp.split('T')[1].slice(0, -1);
			let datetime = `${date} ${time}`;*/

			let latlng = generateLatlng(),
				lat = latlng.lat,
				lng = latlng.lng;

			let trend = trends[ randInt(0, trends.length - 1) ],
				source = ["iPhone", "Android", "Web"][ randInt(0, 2) ];

			let pad2 = n => n < 10 ? '0' + n : n;
			let date = new Date();
			let datetime = `${date.getFullYear().toString()}-${pad2(date.getMonth() + 1)}-${pad2(date.getDate())} ${pad2(date.getHours())}:${pad2(date.getMinutes())}:${pad2(date.getSeconds())}`;

			let count = 0;

			res.push(
				[ lat, lng, trend, source, datetime, count ]
			);

			for(let i = 0; i < randInt(30, 70); i++) {
				latlng = generateLatlngNearby(lat, lng);

				if(Math.random() <= 0.25)
					trend = trends[ randInt(0, trends.length - 1) ];

				if(Math.random() <= 0.25)
					source = ["iPhone", "Android", "Web"][ randInt(0, 2) ];

				res.push(
					[ latlng.lat, latlng.lng, trend, source, datetime, count ]
				);
			}
		}

		//await db.close(err => { if(err) console.error(err.message); });


		return res;
	},
	inputs: [
		/*{
			title: 'address',
			type: 'string',
			maxLength: 50,
			//pattern: '\\w+'
		}*/
	]
});




const _CITIES = [
	{name: "New York", lat: 40.730610, lng: -73.935242},
	{name: "London", lat: 51.509865, lng: -0.118092},
	{name: "Washington DC", lat: 38.8951, lng: -77.0364},
	{name: "Amsterdam", lat: 52.379189, lng: 4.899431},
	{name: "Los Angeles", lat: 34.052235, lng: -118.243683},
	{name: "San Francisco", lat: 37.773972, lng: -122.431297},
	{name: "Paris", lat: 48.864716, lng: 2.349014},
	{name: "Berlin", lat: 52.520008, lng: 13.404954},
	{name: "Dubai", lat: 25.276987, lng: 55.296249},
	{name: "Rome", lat: 41.902782, lng: 12.496366},
	{name: "Tokyo", lat: 35.652832, lng: 139.839478},
	{name: "Madrid", lat: 40.416775, lng: -3.703790},
	{name: "Sydney", lat: -33.865143, lng: 151.209900},
	{name: "Mexico City", lat: 19.432608, lng: -99.133209},
	{name: "Cape Town", lat: -33.918861, lng: 18.423300},
	{name: "Miami", lat: 25.761681, lng: -80.191788},
	{name: "Seoul", lat: 37.532600, lng: 127.024612},
	{name: "Hong Kong", lat: 22.302711, lng: 114.177216},
	{name: "Barcelona", lat: 41.390205, lng: 2.154007},
	{name: "Singapore", lat: 1.290270, lng: 103.851959},
	{name: "Rio de Janeiro", lat: -22.908333, lng: -43.196388},
	{name: "Seattle", lat: 47.608013, lng: -122.335167},
	{name: "Osaka", lat: 34.669529, lng: 135.497009},
	{name: "Saint Petersburg", lat: 59.937500, lng: 30.308611},
	{name: "Toronto", lat: 43.651070, lng: -79.347015},
	{name: "Frankfurt", lat: 50.110924, lng: 8.682127},
	{name: "Shanghai", lat: 31.224361, lng: 121.469170},
	{name: "Vancouver", lat: 49.246292, lng: -123.116226},
	{name: "Munich", lat: 48.137154, lng: 11.576124},
	{name: "Shenzhen", lat: 22.542883, lng: 114.062996},
	{name: "Buenos Aires", lat: -34.603722, lng: -58.381592},
	{name: "Brussels", lat: 50.85045, lng: 4.34878},
	{name: "Houston", lat: 29.749907, lng: -95.358421},
	{name: "Chicago", lat: 41.881832, lng: -87.623177},
	{name: "Istanbul", lat: 41.015137, lng: 28.979530},
	{name: "New Orleans", lat: 29.951065, lng: -90.071533},
	{name: "Perth", lat: -31.953512, lng: 115.857048},
	{name: "Venice", lat: 45.438759, lng: 12.327145},
	{name: "Auckland", lat: -36.848461, lng: 174.763336},
	{name: "Melbourne", lat: -37.840935, lng: 144.946457},
	{name: "Johannesburg", lat: -26.195246, lng: 28.034088},
	{name: "New Delhi", lat: 28.644800, lng: 77.216721},
	{name: "Geneva", lat: 46.204391, lng: 6.143158},
	{name: "Mumbai", lat: 19.076090, lng: 72.877426},
	{name: "Portland", lat: 45.523064, lng: -122.676483},
	{name: "Tel Aviv", lat: 32.109333, lng: 34.855499},
	{name: "Bangkok", lat: 13.736717, lng: 100.523186},
	{name: "Zurich", lat: 47.373878, lng: 8.545094},
	{name: "Prague", lat: 50.073658, lng: 14.418540},
	{name: "Copenhagen", lat: 55.676098, lng: 12.568337},
	{name: "Vienna", lat: 48.210033, lng: 16.363449},
	{name: "Stockholm", lat: 59.334591, lng: 18.063240},
	{name: "Athens", lat: 37.983810, lng: 23.727539},
	{name: "Oslo", lat: 59.911491, lng: 10.757933},
	{name: "Dublin", lat: 53.350140, lng: -6.266155},
	{name: "Helsinki", lat: 60.192059, lng: 24.945831},
	{name: "Taipei", lat: 25.105497, lng: 121.597366},
	{name: "Moscow", lat: 55.751244, lng: 37.618423},
	{name: "Beijing", lat: 39.916668, lng: 116.383331},
	{name: "Santiago", lat: -33.447487, lng: -70.673676},
	{name: "Caracas", lat: 10.500000, lng: -66.916664},
	{name: "Reykjavik", lat: 64.128288, lng: -21.827774}
];

let rand = (min, max) => Math.random() * (max - min) + min;
let randInt = (min, max) => {
	min = Math.ceil(min);
	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

function generateLatlng() {
	for(let c of _CITIES) {
		let r = Math.random();

		if(r <= 0.1) return c;
	}

	return _CITIES[ _CITIES.length - 1 ];
}

function generateLatlngNearby(lat, lng) {
	let r = 0.08984725965858041, // 10000 / 111300
		u = Math.random(),
		v = Math.random();

	let w = r * Math.sqrt(u),
		t = 2 * Math.PI * v;
	let x = (w * Math.cos(t)) / Math.cos(lng),
		y = w * Math.sin(t);

	return { lat: x, lng: y };
}




/*
API-calls for the real-time version
https://developer.twitter.com/en/docs/twitter-api/tweets/filtered-stream/introduction

curl -X POST 'https://api.twitter.com/2/tweets/search/stream/rules' -H "Content-type: application/json" -H "Authorization: Bearer $BEARER_TOKEN" -d '{ "add": [ {"value": "trump has:geo", "tag": "trump"} ] }'

curl -X POST 'https://api.twitter.com/2/tweets/search/stream/rules' -H "Content-type: application/json" -H "Authorization: Bearer $BEARER_TOKEN" -d '{ "delete": { "ids": ["1360374040339369988"] } }'

curl https://api.twitter.com/2/tweets/search/stream/rules -H "Authorization: Bearer $BEARER_TOKEN"

curl https://api.twitter.com/2/tweets/search/stream\?expansions=geo.place_id\&place.fields=geo\&tweet.fields=geo -H "Authorization: Bearer $BEARER_TOKEN"
*/
