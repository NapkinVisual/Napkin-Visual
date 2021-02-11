//
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
let datafire = require('datafire');
let twitter = require('@datafire/twitter').create({
	consumer_key: "9y7GTKGRTKgcNpkUnWAwRTBZk",
	consumer_secret: "soNwJ4gTh1YdZsm3SBLJpeMoyyg3LKbHBlLoq2UmMdTyH4fKsp",
	token: "2752754253-N4plBu71Xeq0C0CnR0aNpFppajMc5KjUILLpFKN",
	token_secret: "KtetYQ0sJ5hA8whYpdYE4miQQshx9hUs4oXF0CyZVMQko"
});

module.exports = new datafire.Action({
	description: 'Twitter',
	handler: async (input, context) => {
		//let r = await twitter.trends.available(null, context);
		/*let r = await twitter.trends.place({
			"id": "1"
		}, context);*/

		/*let r = await twitter.geo.search({
			"contained_within": "1",
			"attribute:street_address": input.address
			//"lat": 40.73763256552795,
			//"lng": -73.99326324462892
		}, context);*/

		let r = await twitter.search.tweets({
			"q": 0
		}, context);

		return r;
	},
	inputs: [{
		title: 'address',
		type: 'string',
		maxLength: 50,
		//pattern: '\\w+'
	}]
});
